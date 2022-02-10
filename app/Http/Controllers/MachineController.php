<?php

namespace App\Http\Controllers;

use App\Http\Resources\MachineCollection;
use App\Http\Resources\MachineResource;
use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Authorize the user
        abort_unless(access('machines_access'), 403);


        $machines = Machine::all();

        return MachineCollection::collection($machines);
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
        abort_unless(access('machines_create'), 403);

        $request->validate([
            'name' => 'required|unique:machines,name|string|max:255',
            'description' => 'nullable|string|max:155',
        ]);

        try {
            //Grab the data submitted
            $data = $request->only([
                'name',
                'description'
            ]);

            //Create the machine
            $machine = Machine::create($data);

            return message('Machine created successfully', 200, $machine);
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Machine  $machine
     * @return \Illuminate\Http\Response
     */
    public function show(Machine $machine)
    {
        //Authorize the user
        abort_unless(access('machines_show'), 403);

        return MachineResource::make($machine);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Machine  $machine
     * @return \Illuminate\Http\Response
     */
    public function edit(Machine $machine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Machine  $machine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Machine $machine)
    {
        //Authorize the user
        abort_unless(access('machines_edit'), 403);

        $request->validate([
            'name' => 'nullable|unique:machines,name,' . $machine->id . '|string|max:255',
            'description' => 'nullable|string|max:155',
        ]);

        try {
            //Grab the data submitted
            $data = $request->only([
                'name',
                'description'
            ]);

            //Update the machine
            $machine->update($data);

            return message('Machine updated successfully', 200, $machine);
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Machine  $machine
     * @return \Illuminate\Http\Response
     */
    public function destroy(Machine $machine)
    {
        //Authorize the user
        abort_unless(access('machines_delete'), 403);

        if ($machine->delete())
            return message('Machine archived successfully');

        return message('Something went wrong', 400);
    }
}
