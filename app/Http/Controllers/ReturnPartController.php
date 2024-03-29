<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReturnPartCollection;
use App\Http\Resources\ReturnPartResource;
use App\Models\AdvancePaymentHistory;
use App\Models\Invoice;
use App\Models\PartStock;
use App\Models\ReturnPart;
use App\Models\ReturnPartItem;
use Illuminate\Http\Request;

class ReturnPartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // return $request;
        $returnPart = ReturnPart::with('returnPartItems', 'invoice')->latest();

        // search
        if ($request->q) {
            $returnPart = $returnPart->where(function ($returnPart) use ($request) {
                $returnPart->whereHas('invoice', fn ($q) => $q->where('invoice_number', 'LIKE', '%' . $request->q . '%'))
                    ->orWhere('tracking_number', 'LIKE', '%' . $request->q . '%');
            });
        }

        $returnPart->when($request->type, function ($q) use ($request) {
            $q->where('type', $request->type);
        });

        if ($request->rows == 'all')
            return ReturnPart::collection($returnPart->get());

        $returnPart = $returnPart->paginate($request->get('rows', 5));

        return ReturnPartCollection::collection($returnPart);
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
    public function show(ReturnPart $returnPart)
    {
        $returnPart = $returnPart->load(['returnPartItems', 'invoice']);
        return ReturnPartResource::make($returnPart);
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
        $returnPart = ReturnPart::with('invoice')->find($id);
        $invoice_number =  $returnPart->invoice->invoice_number;

        if ($returnPart) {
            $returnPartItems = ReturnPartItem::where('return_part_id', $id)->get();
            foreach ($returnPartItems as $item) {
                $partStock = PartStock::where(['part_id' => $item->part_id])->latest()->first();
                if ($partStock) { //cause when returning value is incrementing so we deleting value is decrementing
                    $partStock->decrement('unit_value', $item->quantity);
                    $item->delete();
                } else {
                    return message('Part Stock not found', 404);
                }
            }
            $advance = AdvancePaymentHistory::where('invoice_number', $invoice_number)->first();
            if ($advance) {
                $advance->delete();
            }
            $invoice = Invoice::where('id', $returnPart->invoice_id)->first();
            if ($invoice) {
                $invoice->update(['grand_total' => $invoice->grand_total + $returnPart->grand_total]);
            }
            $returnPart->delete();
            return message('Information Deleted successfully', 200, $returnPart);
        }
        return message('Information not found', 404);
    }
}
