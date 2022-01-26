<?php

namespace App\Http\Controllers;

use App\Http\Resources\PartStockCollection;
use App\Models\Part;
use App\Models\PartStock;
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
            ->with('warehouse')
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
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'unit' => 'required|in:piece,millimetre,centimetre,metre,feet,inch,yard',
            'unit_value' => 'nullable|numeric',
            'shipment_date' => 'nullable|date',
            'shipment_invoice_no' => 'nullable|string|max:255',
            'shipment_details' => 'nullable|string',
            'yen_price' => 'nullable|numeric',
            'formula_price' => 'nullable|numeric',
            'selling_price' => 'nullable|numeric',
        ]);

        $data = $request->only([
            'warehouse_id',
            'unit',
            'unit_value',
            'shipment_date',
            'shipment_invoice_no',
            'shipment_details',
            'yen_price',
            'formula_price',
            'selling_price'
        ]);

        $stock = $part->stocks()->create($data);

        return $part;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Part $part
     * @param  \App\Models\PartStock  $partStock
     * @return \Illuminate\Http\Response
     */
    public function show(Part $part, PartStock $partStock)
    {
        //
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
    public function update(Request $request, Part $part, PartStock $partStock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Part $part
     * @param  \App\Models\PartStock  $partStock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Part $part, PartStock $partStock)
    {
        //
    }
}
