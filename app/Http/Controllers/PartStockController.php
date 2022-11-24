<?php

namespace App\Http\Controllers;

use App\Http\Resources\PartStockCollection;
use App\Http\Resources\PartStockResource;
use App\Models\Part;
use App\Models\PartStock;
use App\Models\StockHistory;
use Illuminate\Http\Request;

class PartStockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\Part $part
     * @return \Illuminate\Http\Response
     */
    public function index(Part $part)
    {
        $stocks = $part->stocks()
            ->with('warehouse', 'box')
            ->get();

        return PartStockCollection::collection($stocks);
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
     * @param \App\Models\Part $part
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Part $part)
    {
        // return $request;
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            // 'box_heading_id' => 'required|exists:part_headings,id', 
            'unit_value' => 'nullable|numeric',
            'shipment_date' => 'nullable|date',
            'shipment_invoice_no' => 'nullable|string|max:255',
            'shipment_details' => 'nullable|string',
            'yen_price' => 'nullable|numeric',
            'formula_price' => 'nullable|numeric',
            'selling_price' => 'nullable|numeric',
        ]);

        try {
            $data = $request->only([
                'warehouse_id',
                'box_heading_id',
                'unit_value',
                'stock_alert',
                'shipment_date',
                'shipment_invoice_no',
                'shipment_details',
                'yen_price',
                'formula_price',
                'selling_price',
                'notes'
            ]);

            $stock = $part->stocks()->create($data);
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }
        return message('New stock added successfully', 200, $stock);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Part $part
     * @param  \App\Models\PartStock  $partStock
     * @return \Illuminate\Http\Response
     */
    public function show(Part $part, PartStock $stock)
    {
        //Load the relational data
        $stock->load('part', 'warehouse', 'box');

        return PartStockResource::make($stock);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PartStock  $partStock
     * @return \Illuminate\Http\Response
     */
    public function edit(PartStock $partStock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Models\Part $part
     * @param  \App\Models\PartStock  $partStock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Part $part, PartStock $stock)
    {
        // return $request;
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'box_heading_id' => 'required|exists:box_headings,id',
            'unit_value' => 'nullable|numeric',
            'shipment_date' => 'nullable|date',
            'shipment_invoice_no' => 'nullable|string|max:255',
            'shipment_details' => 'nullable|string',
            'yen_price' => 'nullable|numeric',
            'formula_price' => 'nullable|numeric',
            'selling_price' => 'nullable|numeric',
        ]);

        try {
            $data = $request->only([
                'box_heading_id',
                'warehouse_id',
                'unit_value',
                'stock_alert',
                'shipment_date',
                'shipment_invoice_no',
                'shipment_details',
                'yen_price',
                'formula_price',
                'selling_price',
                'notes'
            ]);

            $stock->update(array_merge($data));

            //Check if the last stock and updating stock are same
            if ($stock->part->stocks->last() == $stock){
                $stock->part()->update($request->only([
                    'yen_price',
                    'formula_price',
                    'selling_price'
                ]));
            }
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }

        return message('Stock updated successfully', 200, $stock);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Part $part
     * @param  \App\Models\PartStock  $partStock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Part $part, PartStock $stock)
    {
        if ($stock->delete())
            return message('Warehouse archived successfully');

        return message('Something went wrong', 400);
    }


}
