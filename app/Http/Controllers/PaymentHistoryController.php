<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentHistories;
use App\Http\Resources\PaymentHistoryCollection;
use App\Http\Resources\PaymentHistoryResource;
use App\Models\AdvancePaymentHistory;
use App\Models\Company;
use App\Models\Invoice;
use Carbon\Carbon;

class PaymentHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // return $request;
        $payment_history = PaymentHistories::with(
            'invoice',
        )->whereInvoiceId($request->id);


        if ($request->rows == 'all')
            return PaymentHistories::collection($payment_history->get());

        $payment_history = $payment_history->paginate($request->get('rows', 10));

        return PaymentHistoryCollection::collection($payment_history);
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
        $request->validate([
            'payment_date' => 'required',
            'amount' => 'required|numeric|gt:0',
            'transaction_details' => 'required',
            'payment_mode' => 'required',
            'file' => 'required|mimes:png,jpg,jpeg,csv,txt,xlx,xls,pdf|max:2048'

        ]);
        
        try {

            if ($request->hasFile('file')){
                $file = $request->file('file')->store('payment-history/');
            }

            //Store the data
            $payment_history = PaymentHistories::create([
                'invoice_id' => $request->invoice_id,
                'payment_mode' => $request->payment_mode,
                'payment_date' => Carbon::parse($request->payment_date)->format('Y-m-d'),
                'amount' => $request->amount,
                'transaction_details' => $request->transaction_details,
                'file' => $file,
                'created_by'=>auth()->user()->name,

            ]);
            if($request->payment_mode === "advance"){
                $invoice =  Invoice::find($request->invoice_id);
                $com = Company::find($invoice->company_id);
                $com->update(['due_amount'=> $com->due_amount-$request->amount]);
                $advanceMoney = AdvancePaymentHistory::create([
                    'company_id'=>$invoice->company_id,
                    'amount'=>$request->amount,
                    'invoice_number'=>$invoice->invoice_number,
                    'transaction_type'=>false,
                    'created_by'=>auth()->user()->name,
                ]);
            }else{
                $invoice =  Invoice::find($request->invoice_id);
                $com = Company::find($invoice->company_id);
                $com->update(['due_amount'=> $com->due_amount-$request->amount]);
            }
            


        } catch (\Throwable $th) {
            return message($th->getMessage());
        }

        return message("Payment History created successfully", 200, $payment_history);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentHistories $paymentHistory)
    {

        $paymentHistory = $paymentHistory->load([
            'invoice',
        ]);


        return PaymentHistoryResource::make($paymentHistory);
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
