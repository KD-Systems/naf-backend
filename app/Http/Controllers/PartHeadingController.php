<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\PartHeading;
use Illuminate\Http\Request;
use App\Http\Resources\PartHeadingCollection;
use App\Http\Resources\PartHeadingResource;

class PartHeadingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Machine  $machine
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $machine)
    {
        //Check if the machine param has single id and get that machine headings
        $machine = Machine::find($machine);
        if ($machine)
            $headings = $machine->headings()->withCount('parts')->get();

        //Check if the machine doesn't exists
        if (!$machine)
            $headings = PartHeading::withCount('parts')->get()->unique('name');

        return PartHeadingCollection::collection($headings);
    }

    /**
     * Get the part headings by filtering
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function filtered(Request $request)
    {

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
     * @param  \App\Models\Machine  $machine
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Machine $machine)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'common_heading' => 'nullable'
        ]);

        try {
            $data = $request->only('name', 'description', 'remarks');
            $heading = $machine->headings()->create($data);
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }

        return message('Part heading created successfully', 200, $heading);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Machine  $machine
     * @param  \App\Models\PartHeading  $partHeading
     * @return \Illuminate\Http\Response
     */
    public function show(Machine $machine, PartHeading $partHeading)
    {
        $partHeading->load('parts.aliases');

        return PartHeadingResource::make($partHeading);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PartHeading  $partHeading
     * @return \Illuminate\Http\Response
     */
    public function edit(PartHeading $partHeading)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Machine  $machine
     * @param  \App\Models\PartHeading  $partHeading
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Machine $machine, PartHeading $partHeading)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $data = $request->only('name', 'description', 'remarks');
            $partHeading->update($data);
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }

        return message('Part heading created successfully', 200, $partHeading);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PartHeading  $partHeading
     * @return \Illuminate\Http\Response
     */
    public function destroy(Machine $machine, PartHeading $partHeading)
    {
        if ($partHeading->delete())
            return message('Part heading deleted successfully');

        return message('Something went wrong', 400);
    }


}
