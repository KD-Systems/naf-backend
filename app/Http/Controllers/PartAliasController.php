<?php

namespace App\Http\Controllers;

use App\Http\Resources\PartCollection;
use App\Models\Part;
use App\Models\PartAlias;
use Illuminate\Http\Request;

class PartAliasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\Part $part
     * @return \Illuminate\Http\Response
     */
    public function index(Part $part)
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
     * @param \App\Models\Part $part
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Part $part)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Part $part
     * @param  \App\Models\PartAlias  $partAlias
     * @return \Illuminate\Http\Response
     */
    public function show(PartAlias $partAlias)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PartAlias  $partAlias
     * @return \Illuminate\Http\Response
     */
    public function edit(PartAlias $partAlias)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PartAlias  $partAlias
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PartAlias $partAlias)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PartAlias  $partAlias
     * @return \Illuminate\Http\Response
     */
    public function destroy(PartAlias $partAlias)
    {
        //
    }
}
