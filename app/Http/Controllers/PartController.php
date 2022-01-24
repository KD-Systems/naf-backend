<?php

namespace App\Http\Controllers;

use App\Http\Resources\PartCollection;
use App\Models\Part;
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
        $parts = Part::all();

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
            'part_heading_id' => 'required|exists:part_headings,id',
            'name' => 'required|unique:part_aliases,name|max:255',
            'part_number' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $data = $request->only('part_heading_id', 'name', 'part_number', 'description', 'remarks');
            $part = Part::create($data);
            $part->aliases()->create($data);
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
        //
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
