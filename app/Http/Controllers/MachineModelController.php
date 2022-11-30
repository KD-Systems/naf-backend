<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\MachineModel;
use Illuminate\Http\Request;
use App\Http\Resources\MachineModelResource;
use App\Http\Resources\MachineModelCollection;

class MachineModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\Machine $machine
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Machine $machine)
    {
        //Authorize the user
        abort_unless(access('machines_model_access'), 403);

        $models = $machine->models;

        return MachineModelCollection::collection($models);
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
     * @param \App\Models\Machine $machine
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Machine $machine)
    {
        //Authorize the user
        abort_unless(access('machines_model_access'), 403);

        $request->validate([
            'name' => 'required|max:255|string|unique:machine_models,name',
            'space' => 'nullable|string',
        ]);

        try {
            $data = $request->only('name', 'space', 'description');
            $model = $machine->models()->create($data);
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }

        return message('Machine model created successfully', 200, $model);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MachineModel  $model
     * @return \Illuminate\Http\Response
     */
    public function show(Machine $machine, MachineModel $model)
    {
        return MachineModelResource::make($model);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MachineModel  $model
     * @return \Illuminate\Http\Response
     */
    public function edit(MachineModel $model)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Models\Machine $machine
     * @param  \App\Models\MachineModel  $model
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Machine $machine, MachineModel $model)
    {
        $request->validate([
            'name' => 'required|max:255|string|unique:machine_models,name,' . $model->id,
            'space' => 'nullable|string',
        ]);

        try {
            $data = $request->only('name', 'space', 'description', 'remarks');
            $model->update($data);
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }

        return message('Machine model updated successfully', 200, $model);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MachineModel  $model
     * @return \Illuminate\Http\Response
     */
    public function destroy(Machine $machine, MachineModel $model)
    {
        if ($model->delete())
            return message('Machine model archived successfully');

        return message('Something went wrong', 400);
    }
}
