<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionSummeryCollection;
use App\Http\Resources\TransactionSummeryDetailsCollection;
use App\Models\Invoice;
use App\Models\PaymentHistories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        abort_unless(access('invoices_access'), 403);

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
                $invoices = $invoices->whereHas('company', fn ($q) => $q->where('name', 'LIKE', '%' . $request->q . '%'))->orWhere('invoice_number', 'LIKE', '%' . $request->q . '%');
            });

        //Search the invoice
        if ($request->company_id)
            $invoices = $invoices->where(function ($invoices) use ($request) {
                //Search the data by company name and invoice number
                $invoices = $invoices->whereHas('company', fn ($q) => $q->whereId($request->company_id));
            });

            // Filter data with the machine id
        // $invoices = $invoices->when($request->status == "due", function ($q) {
        //     $q->whereHas('partItems', function ($qe) {
        //         $qe->where('total_value','>=',5000);
        //     });
        // });

        if ($request->rows == 'all')
            return Invoice::collection($invoices->get());

        $invoices = $invoices->paginate($request->get('rows', 10));

        return TransactionSummeryCollection::collection($invoices);
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
    public function show(Request $request,$id)
    {
        $payment_history = PaymentHistories::with(
            'invoice',
        )->whereInvoiceId($id);


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

    
}
