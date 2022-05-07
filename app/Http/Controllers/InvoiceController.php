<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Resources\PartCollection;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\InvoiceCollection;
use App\Http\Resources\InvoiceSearchCollection;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $invoices = Invoice::with(
            'quotation',
            'company:id,name',
            'quotation.requisition',
            'partItems.part.aliases',
            'quotation.requisition.machines:id,machine_model_id',
            'quotation.requisition.machines.model:id,name',
        );

        //Search the invoice
        if ($request->q)
            $invoices = $invoices->where(function ($invoices) use ($request) {
                //Search the data by company name and invoice number
                $invoices = $invoices->whereHas('company', fn ($q) => $q->where('name', 'LIKE', '%' . $request->q . '%'))->orWhere('invoice_number', 'LIKE', '%' . $request->q . '%');
            });

        if ($request->rows == 'all')
            return Invoice::collection($invoices->get());

        $invoices = $invoices->paginate($request->get('rows', 10));

        return InvoiceCollection::collection($invoices);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();

        try {
            //Store the data

            if (Invoice::where('quotation_id', $request->id)->doesntExist()) {
                if ($request->locked_at != null) {
                    $invoice = Invoice::create([
                        'quotation_id' => $request->id,
                        'company_id' => $request->company['id'],
                        'expected_delivery' => $request->requisition['expected_delivery'],
                        'payment_mode' => $request->requisition['payment_mode'],
                        'payment_term' => $request->requisition['payment_term'],
                        'payment_partial_mode' => $request->requisition['payment_partial_mode'],
                        'next_payment' => $request->requisition['next_payment'],
                        'last_payment' => $request->requisition['next_payment'],
                        'remarks' => $request->requisition['remarks'],
                    ]);

                    // create unique id
                    $id = \Illuminate\Support\Facades\DB::getPdo()->lastInsertId();
                    $data = Invoice::findOrFail($id);
                    $data->update([
                        'invoice_number'   => 'IN' . date("Ym") . $id,
                    ]);

                    $items = collect($request->part_items);

                    $items = $items->map(function ($dt) {
                        return [
                            'part_id' => $dt['part_id'],
                            'quantity' => $dt['quantity'],
                            'unit_value' => $dt['unit_value'],
                            'total_value' => $dt['quantity'] * $dt['unit_value']
                        ];
                    });

                    $invoice->partItems()->createMany($items);

                    return message('Invoice created successfully', 201, $data);
                } else {
                    return message('Quotation must be locked', 422);
                }
            } else {
                return message('Invoice already exists', 422);
            }
        } catch (\Throwable $th) {
            return message(
                $th->getMessage(),
                400
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        $invoice->load([
            'company',
            'quotation.requisition.machines:id,machine_model_id',
            'quotation.requisition.machines.model:id,name',
            'quotation.requisition',
            'partItems.part.aliases',
            'paymentHistory'
        ]);

        return InvoiceResource::make($invoice);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function Search(Request $request)
    {

        $invoice = Invoice::with(
            'company',
            'quotation.requisition.machines:id,machine_model_id',
            'quotation.requisition.machines.model:id,name',
            'quotation.requisition',
            'partItems.part.aliases',
            'paymentHistory'
        )->where('invoice_number', 'LIKE', '%' . $request->q . '%')->get();

        return InvoiceSearchCollection::collection($invoice);

        return message('Found', 201, $invoice);
    }

    public function PartSearch(Request $request)
    {

        $parts = Part::with('aliases', 'machines', 'stocks')
            ->leftJoin('part_aliases', 'part_aliases.part_id', '=', 'parts.id')
            ->leftJoin('part_stocks', 'part_stocks.part_id', '=', 'parts.id')
            ->leftJoin('machines', 'part_aliases.machine_id', '=', 'machines.id')
            ->leftJoin('part_headings', 'part_headings.id', 'part_aliases.part_heading_id')
            ->where('part_aliases.part_number', 'LIKE', '%' . $request->q . '%')->get();

        return PartCollection::collection($parts);

    }
}
