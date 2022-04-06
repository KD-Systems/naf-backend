<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceCollection;
use App\Http\Resources\InvoiceResource;
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
            'company:id,name',
            'quotation.requisition',
            'quotation.partItems.part.aliases',
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

            if (Invoice::where('quotation_id',$request->id)->doesntExist()) {
                $data = Invoice::create([
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

                    $id = \Illuminate\Support\Facades\DB::getPdo()->lastInsertId();

                    $data = Invoice::findOrFail($id);
                    $str = str_pad($id, 4, '0', STR_PAD_LEFT);

                    $data->update([
                        'invoice_number'   =>'IN-' .date("F-Y-").$str,
                    ]);

                return message('Invoice created successfully', 201, $data);
            }else{
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
            'quotation.requisition.machines.machineModel:id,name',
            'quotation.requisition',
            'quotation.partItems.part.aliases'
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
}
