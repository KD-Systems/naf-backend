<?php

namespace App\Http\Controllers;
use App\Http\Resources\PartCollection;
use App\Http\Resources\PartResource;
use App\Models\Part;
use App\Models\PartAlias;
use Illuminate\Http\Request;

class PartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parts = Part::with([
            'aliases:id,name,part_number,part_id,machine_id,part_heading_id',
            'aliases.machine:id,name',
            'aliases.partHeading:id,name'
        ])->get();

        return PartCollection::collection($parts);
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
            'image' => 'nullable|image|max:2048',
            'part_heading_id' => 'required|exists:part_headings,id',
            'machine_id' => 'required|exists:machines,id',
            'name' => 'required|unique:part_aliases,name|max:255',
            'part_number' => 'required|string|max:255|unique:part_aliases',
            'description' => 'nullable|string',
        ]);

        try {
            $data = $request->only([
                'machine_id',
                'part_heading_id',
                'name',
                'part_number',
                'description'
            ]);
            $part = Part::create($data);
            $part->aliases()->create($data);

            // if($request->hasFile('image'))

        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }

        return message('Part created successfully', 200, $part);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Part  $part
     * @return \Illuminate\Http\Response
     */
    public function show(Part $part)
    {
        $part->load('aliases', 'aliases.machine', 'aliases.partHeading');
        return PartResource::make($part);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Part  $part
     * @return \Illuminate\Http\Response
     */
    public function edit(Part $part)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Part  $part
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Part $part)
    {
        $request->validate([
            'description' => 'nullable|string',
            'remarks' => 'nullable|string'
        ]);

        try {
            $data = $request->only('description', 'remarks');
            $part->update($data);
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }

        return message('Part updated successfully', 200, $part);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Part  $part
     * @return \Illuminate\Http\Response
     */
    public function destroy(Part $part)
    {
        if ($part->delete())
            return message('Part deleted successfully');

        return message('Something went wrong', 400);
    }
}
