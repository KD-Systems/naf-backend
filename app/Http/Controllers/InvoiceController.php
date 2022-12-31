<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PartCollection;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\InvoiceCollection;
use App\Http\Resources\InvoiceSearchCollection;
use App\Http\Resources\TransactionSummeryCollection;
use App\Models\Company;
use App\Models\ReturnPart;
use App\Models\ReturnPartItem;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Authorize the user
        abort_unless(access('transaction_summery_access'), 403);

        $invoices = Invoice::with(
            'paymentHistory',
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

        //filtering the invoice
        if ($request->company_id)
            $invoices = $invoices->where(function ($invoices) use ($request) {
                //Search the data by company name and invoice number
                $invoices = $invoices->whereHas('company', fn ($q) => $q->whereId($request->company_id));
            });
        //paid and due filtering
        $invoices = $invoices->when($request->status, function ($q) use ($request) {
            $q->where('status', $request->status);
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
        // return $request;
        //Authorize the user
        abort_unless(access('invoices_create'), 403);

        DB::beginTransaction();
        try {
            //Store the data

            if (Invoice::where('quotation_id', $request->id)->doesntExist()) {
                if ($request->locked_at != null) {
                    $invoice = Invoice::create([
                        'quotation_id' => $request->id,
                        'company_id' => $request->company['id'],
                        'expected_delivery' => $request->requisition['expected_delivery'] ?? null,
                        'payment_mode' => $request->requisition['payment_mode'],
                        'payment_term' => $request->requisition['payment_term'],
                        'payment_partial_mode' => $request->requisition['payment_partial_mode'],
                        'next_payment' => $request->requisition['next_payment'],
                        'last_payment' => $request->requisition['next_payment'],
                        'created_by' => auth()->user()->id,
                        'remarks' => $request->requisition['remarks'],
                        'status' => "due",
                    ]);

                    // create unique id
                    // $id = $invoice->id;
                    // $data = Invoice::findOrFail($id);
                    // $data->update([
                    //     'invoice_number'   => 'IN' . date("Ym") . $id,
                    // ]);

                    $items = collect($request->part_items);



                    $items = $items->map(function ($dt) {

                        return [
                            'part_id' => $dt['part_id'],
                            'quantity' => $dt['quantity'],
                            'unit_value' => $dt['unit_value'], 
                            'total_value' => $dt['quantity'] * $dt['unit_value'],
                            'status' => $dt['status'],
                           'type' => $dt['type'],


                        ];
                    });

                    $total = $items->sum('total_value');

                    $com = Company::find($request->company['id']);
                    $com->update(['due_amount' => $com->due_amount + $total]);

                    $invoice->partItems()->createMany($items);
                    DB::commit();
                    return message('Invoice created successfully', 201, $invoice);
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
    public function show(Invoice $invoice)
    {
        //Authorize the user
        abort_unless(access('invoices_show'), 403);

        $invoice->load([
            'company',
            'quotation.requisition.machines:id,machine_model_id',
            'quotation.requisition.machines.model:id,name',
            'quotation.requisition',
            'partItems.part.aliases',
            'paymentHistory',
            'deliveryNote:id,invoice_id'
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

    public function returnParts(Request $request){

        $request->validate([
            'invoice_id' => 'required',
        ],[
            'invoice_id.required' => 'Please provide a valid invoice.'
        ]);

        try {
            DB::beginTransaction();
            $returnPart = ReturnPart::firstOrCreate(['invoice_id' => $request->input('invoice_id')]);
            $returnPart->tracking_number = 'RTP'. date("Ym") . $request->input('invoice_id');
            $returnPart->invoice_id = $request->input('invoice_id');
            $returnPart->grand_total = $request->input('grand_total');
            $returnPart->save();

            foreach($request->input('items') as $item){
                $returnPartItem = ReturnPartItem::firstOrCreate(['return_part_id' =>  $returnPart->id, 'part_id' => $item['id']]);
                $returnPartItem->return_part_id = $returnPart->id;
                $returnPartItem->part_id = $item['id'];
                $returnPartItem->quanity = $returnPartItem->quanity ? $returnPartItem->increment('quanity', $item['qnty']) : $item['qnty'];
                $returnPartItem->unit_price = $item['unit_value'];
                $returnPartItem->total = $returnPartItem->quanity * $returnPartItem->unit_price;
                $returnPartItem->save();
            }

            DB::commit();
            return message('Invoice parts returned successfully', 200);
                
        } catch (\Exception $e) {
            DB::rollback();
            if($e->getCode() == 23000)
                return message('Sorry, something went wrong', 400);
        }
    }
}
