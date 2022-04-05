<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceCollection;
use App\Models\Invoice;
use Illuminate\Http\Request;

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
            'quotation.requisition',
            'quotation.requisition.machines:id,machine_model_id',
            'quotation.requisition.machines.machineModel:id,name',
        );

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


        try {
            //Store the data
            $data = Invoice::create([
                'quotation_id' => $request->id,
                'company_id' => $request->company['id'],
                'invoice_number' => 'Eos'.mt_rand(0000001,9999999),
                'expected_delivery' => $request->requisition['expected_delivery'],
                'payment_mode' => $request->requisition['payment_mode'],
                'payment_term' => $request->requisition['payment_term'],
                'payment_partial_mode' => $request->requisition['payment_partial_mode'],
                'next_payment' => $request->requisition['next_payment'],
                'last_payment' => $request->requisition['next_payment'],
                'remarks' => $request->requisition['remarks'],
            ]);


            return message('Invoice created successfully', 200, $data);
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
    public function show($id)
    {
        //
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
