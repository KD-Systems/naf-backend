<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceCollection;
use App\Http\Resources\InvoiceResource;
use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $company = auth()->user()->details?->company;
        $invoices = $company->invoices(
            'deliveryNote',
            'quotation',
            'company:id,name,logo',
            'quotation.requisition',
            'partItems.part.aliases',
            'quotation.requisition.machines:id,machine_model_id',
            'quotation.requisition.machines.model:id,name',
        )->latest();

        $invoices = $invoices->withCount(['paymentHistory as totalPaid' => function ($query) {
            $query->select(DB::raw("SUM(amount) as totalAmount"));
        }])->withCount(['partItems as totalAmount' => function ($query) {
            $query->select(DB::raw("SUM(total_value) as totalValue"));
        }]);

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
        DB::beginTransaction();
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
                        'created_by' => auth()->user()->name,
                        'remarks' => $request->requisition['remarks'],
                    ]);

                    // create unique id
                    $id = $invoice->id;
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
                    $total = $items->sum('total_value');
                    
                    $com = Company::find($request->company['id']);
                    $com->update(['due_amount'=> $com->due_amount+$total]);

                    $invoice->partItems()->createMany($items);
                    DB::commit();
                    return message('Invoice created successfully', 201, $data);
                } else {
                    return message('Quotation must be locked', 422);
                }
            } else {
                return message('Invoice already exists', 422);
            }
        } catch (\Throwable $th) {
            DB::rollback();
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
    public function show(Invoice $clientInvoice)
    {
        $clientInvoice->load([
            'company',
            'quotation.requisition.machines:id,machine_model_id',
            'quotation.requisition.machines.model:id,name',
            'quotation.requisition',
            'partItems.part.aliases',
            'paymentHistory',
            'deliveryNote:id,invoice_id'
        ]);

        return InvoiceResource::make($clientInvoice);
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
}
