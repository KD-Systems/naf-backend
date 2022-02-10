<?php

namespace App\Http\Controllers;

use App\Http\Resources\WarehouseResource;
use App\Models\PartAlias;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Authorize the user
        abort_unless(access('warehouses_access'), 403);


        $warehouses = Warehouse::with('partstocks')->get();

        // $part_ids = [];

        // foreach($warehouses as $item)
        // {
        //     $part_ids[] = $item->partstocks->pluck('part_id');
        // }

        // $part_ids = collect($part_ids)->flatten();

        // return PartAlias::whereIn('part_id', $part_ids)->get();

        return WarehouseResource::collection($warehouses);
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
        //Authorize the user
        abort_unless(access('warehouses_access'), 403);


        $request->validate([
            'name' => "required|unique:warehouses,name|string|max:155",
            'description' => 'nullable|string'
        ]);


        try {
            $data = $request->all();
            $warehouse = Warehouse::create($data);

            return message('Warehouse created successfully', 200, $warehouse);
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */

    public function show(Warehouse $warehouse)
    {
        //Authorize the user
        abort_unless(access('warehouses_show'), 403);


        $warehouse = $warehouse->load('partStocks.part.aliases.machine');
        return WarehouseResource::make($warehouse);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(Warehouse $warehouse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        //Authorize the user
        abort_unless(access('warehouses_edit'), 403);


        //Validate the submitted data
        $request->validate([
            'name' => 'required|unique:warehouses,name,' . $warehouse->id . '|string|max:155',
            'description' => 'nullable|string'
        ]);

        try {
            //Collect data in variable
            $data = $request->all();

            //Update the warehouse
            $warehouse->update($data);

            return message('Warehouse updated successfully');
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Warehouse $warehouse)
    {
        //Authorize the user
        abort_unless(access('warehouses_delete'), 403);


        if ($warehouse->delete())
            return message('Warehouse archived successfully');

        return message('Something went wrong', 400);
    }
}
