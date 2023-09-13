<?php

namespace App\Http\Controllers;

use App\Exports\TransactionSummeryExport;
use App\Http\Resources\TransactionSummeryCollection;
use App\Http\Resources\TransactionSummeryDetailsCollection;
use App\Models\Invoice;
use App\Models\PaymentHistories;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TransactionSummeryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Authorize the user
        // abort_unless(access('transaction_summery_access'), 403);

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
        // )->whereHas('quotation.requisition', fn($q) => $q->where('type','purchase_request'))->latest();

        $invoices = $invoices->withCount(['paymentHistory as totalPaid' => function ($query) {
            $query->select(DB::raw("SUM(amount) as totalAmount"));
        }])->withCount(['partItems as totalAmount' => function ($query) {
            $query->select(DB::raw("SUM(total_value) as totalValue"));
        }]);

        //Search the invoice
        if ($request->q)
            $invoices = $invoices->where(function ($invoices) use ($request) {
                //Search the data invoice number and company name
                $invoices = $invoices->where('invoice_number', 'LIKE', '%' . $request->q . '%');
            });

        //filtering the invoice
        $invoices = $invoices->when($request->company_id, function ($invoices) use ($request) {
            //Search the data by company name
            $invoices = $invoices->whereHas('company', fn ($q) => $q->whereId($request->company_id));
        });

        $invoices = $invoices->when($request->type, function ($invoices) use ($request) {
            //filtering the data by company name and invoice number
            $invoices = $invoices->whereHas('quotation.requisition', fn ($q) => $q->where('type', $request->type));
        });

        // Filtering with date
        $invoices = $invoices->when($request->start_date_format, function ($invoices) use ($request) {
            $invoices->whereBetween('created_at', [$request->start_date_format, Carbon::parse($request->end_date_format)->endOfDay()]);
        });

        if ($request->rows == 'all')
            return Invoice::collection($invoices->get());


        // $invoicesData = $invoices->paginate($request->get('rows', 10));
        // $data = TransactionSummeryCollection::collection($invoicesData);

        $invoicesData = $invoices->get();
        $data = TransactionSummeryCollection::collection($invoicesData);
        $totalAmount = $data->sum('previous_due') + $data->sum('grand_total');
        $totalPaid = $data->sum('totalPaid');
        $totalDue = $totalAmount - $totalPaid;

        $invoicesData = $invoices->paginate($request->get('rows', 10));
        $data = TransactionSummeryCollection::collection($invoicesData);

        return [
            'total_amount' => $totalAmount,
            'total_paid' => $totalPaid,
            'total_due' => $totalDue,
            'data' => $data,
        ];
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // abort_unless(access('transaction_details'), 403);

        $payment_history = PaymentHistories::with(
            'invoice',
            'user'
        )->whereInvoiceId($id);

        $payment_history = $payment_history->when($request->type, function ($payment_history) use ($request) {
            //filtering the data by company name and invoice number
            $payment_history = $payment_history->where('payment_mode', $request->type);
        });

        if ($request->rows == 'all')
            return PaymentHistories::collection($payment_history->get());

        $payment_history = $payment_history->paginate($request->get('rows', 10));

        return TransactionSummeryDetailsCollection::collection($payment_history);
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

    public function TransactionExport(Request $request)
    {
        // abort_unless(access('transaction_details'), 403);

        $file = new Filesystem;
        $file->cleanDirectory('uploads/transaction-summery');
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
                //Search the data invoice number and company name
                $invoices = $invoices->where('invoice_number', 'LIKE', '%' . $request->q . '%');
            });

        //filtering the invoice
        $invoices = $invoices->when($request->company_id, function ($invoices) use ($request) {
            //Search the data by company name
            $invoices = $invoices->whereHas('company', fn ($q) => $q->whereId($request->company_id));
        });

        $invoices = $invoices->when($request->type, function ($invoices) use ($request) {
            //filtering the data by company name and invoice number
            $invoices = $invoices->whereHas('quotation.requisition', fn ($q) => $q->where('type', $request->type));
        });

        // Filtering with date
        $invoices = $invoices->when($request->start_date_format, function ($invoices) use ($request) {
            $invoices->whereBetween('created_at', [$request->start_date_format, Carbon::parse($request->end_date_format)->endOfDay()]);
        });

        $invoices = $invoices->get();

        $newCollection = new Collection();

        foreach ($invoices as $key => $data) {
            $newCollection->push((object)[
                'invoice_number' => $data->invoice_number,
                'company' => $data->company?->name,
                'type' => $data->quotation?->requisition?->type,
                'previous_due' => $data->previous_due,
                'totalAmount' => $data->totalAmount,
                'totalPaid' => $data->totalPaid,
                'due' => $data->previous_due ? $data->previous_due - $data->totalPaid : $data->totalAmount - $data->totalPaid,
            ]);
        }
        $export = new TransactionSummeryExport($newCollection);
        $path = 'transaction-summery/transaction-summery-' . time() . '.xlsx';

        // info($request->all());

        // info($newCollection);
        // return true;
        Excel::store($export, $path);

        return response()->json([
            'url' => url('uploads/' . $path)
        ]);
    }
}
