<?php

namespace App\Http\Controllers;

use App\Http\Resources\WareHouseResource;
use App\Models\WareHouse;
use Illuminate\Http\Request;

class WareHouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wareHouses = WareHouse::all();

        return WareHouseResource::collection($wareHouses);
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

        $request->validate([
            'name' => "required|unique:ware_houses,name|string|max:155",
            'description' => 'nullable|string'
        ]);


        try {

            $data = $request->all();

            WareHouse::create($data);
            return message('WareHouse created successfully');
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WareHouse  $wareHouse
     * @return \Illuminate\Http\Response
     */

    public function show(WareHouse $wareHouse)
    {

        return WareHouseResource::make($wareHouse);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WareHouse  $wareHouse
     * @return \Illuminate\Http\Response
     */
    public function edit(WareHouse $wareHouse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WareHouse  $wareHouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WareHouse $wareHouse)
    {
        //Validate the submitted data
        $request->validate([
            'name' => 'required|unique:ware_houses,name,' . $wareHouse->id . '|string|max:155',
            'description' => 'nullable|string'
        ]);

        try {
            //Collect data in variable
            $data = $request->all();

            //Update the wareHouse
            $wareHouse->update($data);

            return message('WareHouse updated successfully');
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WareHouse  $wareHouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(WareHouse $wareHouse)
    {
        if ($wareHouse->delete())
            return message('WareHouse archived successfully');

        return message('Something went wrong', 400);
    }
}
