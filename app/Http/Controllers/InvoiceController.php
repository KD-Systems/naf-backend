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
use App\Models\AdvancePaymentHistory;
use App\Models\Company;
use App\Models\PartItem;
use App\Models\PartStock;
use App\Models\PaymentHistories;
use App\Models\Quotation;
use App\Models\Requisition;
use App\Models\ReturnPart;
use App\Models\ReturnPartItem;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\File;

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
            'returnPart',
            'paymentHistory',
            'deliveryNote',
            'quotation',
            'company:id,name,logo',
            'quotation.requisition',
            'partItems.part.aliases',
            'quotation.requisition.machines:id,machine_model_id',
            'quotation.requisition.machines.model:id,name',
            'user'
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

        $invoices = $invoices->paginate($request->get('rows', 1));

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
                        'sub_total' => $request->sub_total,
                        'vat' => $request->vat,
                        'discount' => $request->discount,
                        'grand_total' => $request->grand_total,
                    ]);

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

                    if ($request->requisition['type'] != "claim_report") {
                        $com = Company::find($request->company['id']);
                        $com->update(['due_amount' => $com->due_amount + $request->grand_total]);
                    }

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
            'deliveryNote:id,invoice_id',
            'returnPart.returnPartItems.alias',
            'user'
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
    public function destroy(Request $request, $id)
    {

        if ($request->type == "previous_due") {
            try {

                $invoice = Invoice::with('quotation.requisition')->find($id);
                $paymenHistories = PaymentHistories::where('invoice_id', $id)->get();
                $currentDue = $invoice->previous_due - $paymenHistories->sum('amount');
                $company = Company::find($invoice->company_id);
                $company =  $company->update([
                    'due_amount' => intval($company->due_amount) - intval($currentDue),
                ]);
                $quotation = $invoice->quotation;
                Quotation::find($quotation->id)->delete();
                $requisition = $invoice->quotation->requisition;
                Requisition::find($requisition->id)->delete();
                foreach ($paymenHistories as $paymenHistory) {
                    $imagePath = public_path('uploads/' . $paymenHistory->file);
                    if (File::exists($imagePath)) {
                        // Delete the file
                        unlink($imagePath);
                    }
                    $paymenHistory->delete();
                }
                $invoice->delete();
                return message('Invoice deleted successfully', 200);
            } catch (\Throwable $th) {
                // DB::rollback();
                return message(
                    $th->getMessage(),
                    400
                );
            }
        } else if ($request->type == "purchase_request") {
            try {
                $invoice = Invoice::with('quotation.requisition')->find($id);
                $paymenHistories = PaymentHistories::where('invoice_id', $id)->get();
                $currentDue = $invoice->grand_total - $paymenHistories->sum('amount');
                $company = Company::find($invoice->company_id);
                $company =  $company->update([
                    'due_amount' => intval($company->due_amount) - intval($currentDue),
                ]);
                PartItem::where('model_type', Invoice::class)->where('model_id', $id)->delete();
                foreach ($paymenHistories as $paymenHistory) {
                    $imagePath = public_path('uploads/' . $paymenHistory->file);
                    if (File::exists($imagePath)) {
                        // Delete the file
                        unlink($imagePath);
                    }
                    $paymenHistory->delete();
                }

                $invoice->delete();

                return message('Invoice deleted successfully', 200);
            } catch (\Throwable $th) {
                // DB::rollback();
                return message(
                    $th->getMessage(),
                    400
                );
            }
        } else {
            $invoice = Invoice::with('quotation.requisition')->find($id);
            $invoice->delete();
            PartItem::where('model_type', Invoice::class)->where('model_id', $id)->delete();

            return message('Invoice deleted successfully', 200);
        }
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

    public function returnParts(Request $request)
    {
        // return $request;

        $request->validate([
            'invoice_id' => 'required',
            'company_id' => 'required',
            'grand_total' => 'required',
        ], [
            'invoice_id.required' => 'Please provide a valid invoice.',
            'company_id.required' => 'Please provide a valid company.'
        ]);


// return $request;
        try {

            DB::beginTransaction();
            $returnPart = ReturnPart::firstOrCreate(['invoice_id' => $request->input('invoice_id')]);
            $returnPart->tracking_number = 'RTP' . date("Ym") . $request->input('invoice_id');
            $returnPart->invoice_id = $request->input('invoice_id');
            $returnPart->created_by = auth()->user()->id;
            $returnPart->type = $request->input('type');
            $returnPart->grand_total = $returnPart->grand_total !=null ? $returnPart->grand_total + $request->input('grand_total') : $request->input('grand_total');
            $returnPart->save();

            foreach ($request->input('items') as $item) {
                $returnPartItem = ReturnPartItem::firstOrCreate(['return_part_id' =>  $returnPart->id, 'part_id' => $item['id']]);
                $returnPartItem->return_part_id = $returnPart->id;
                $returnPartItem->part_id = $item['id'];
                $returnPartItem->quantity = $returnPartItem->quantity ? $returnPartItem->increment('quantity', $item['qnty']) : $item['qnty'];
                $returnPartItem->unit_price = $item['unit_value'];
                $returnPartItem->total = $returnPartItem->quantity * $returnPartItem->unit_price;
                $returnPartItem->save();

                // Increment Part Qunatity When Part Is Returned
                $partStock = PartStock::where(['part_id' => $returnPartItem->part_id])->latest()->first();
                $partStock->increment('unit_value', $returnPartItem->quantity);
            }

            // PaymentHistories::create([
            //     'invoice_id' => $returnPart->invoice_id,
            //     'payment_mode' => "return",
            //     'payment_date' => now(),
            //     'amount' => $returnPart->grand_total,
            // ]);

            if ($request->input('advanced')) {
                AdvancePaymentHistory::create([
                    'company_id' => $request->input('company_id'),
                    'amount' => $returnPart->grand_total,
                    'invoice_number' => Invoice::where('id', $returnPart->invoice_id)->value('invoice_number'),
                    'transaction_type' => 1,
                    'is_returned' => 1,
                    'remarks' => "You have returned parts and got back total " . $returnPart->grand_total . " Tk and " . $request->input('remarks') ?? ''
                ]);
            }

            DB::commit();
            return message('Invoice parts returned successfully', 200);
        } catch (\Exception $e) {
            DB::rollback();
            return message('Sorry, something went wrong', 400);
        }
    }

    /****Invoice attachment file functanality**********/

    public function uploadFiles(Request $request, Invoice $invoice)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|mimes:png,jpg,pdf,xlsx,xls,csv,doc,docx,txt,zip'
        ]);
        foreach ($request->file('files') as $file)
            $invoice->addMedia($file)
                ->preservingOriginal()
                ->toMediaCollection('invoice-files');

        return message('Files uploaded successfully');
    }

    public function getFiles(Invoice $invoice)
    {
        $file = $invoice->getMedia('invoice-files')->toArray();

        return ['data' => $file];
    }

    public function deleteFiles(Request $request, Invoice $invoice, Media $media)
    {
        $invoice->deleteMedia($media);
        return message('Files deleted successfully');
    }

    public function InvoiceUpdate(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        if ($invoice) {
            $invoice->update(['remarks' => $request->remarks]);
            return message('Information saved successfully');
        } else {
            return message('Invoice not found');
        }
    }
}
