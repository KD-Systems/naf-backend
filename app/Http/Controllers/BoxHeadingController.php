<?php

namespace App\Http\Controllers;

use App\Http\Resources\BoxHeadingCollection;
use App\Http\Resources\BoxHeadingResource;
use App\Models\BoxHeading;
use Illuminate\Http\Request;

class BoxHeadingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $boxHeadings = BoxHeading::all();

        return BoxHeadingCollection::collection($boxHeadings);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BoxHeading  $boxHeading
     * @return \Illuminate\Http\Response
     */
    public function show(BoxHeading $boxHeading)
    {
        return BoxHeadingResource::make($boxHeading);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BoxHeading  $boxHeading
     * @return \Illuminate\Http\Response
     */
    public function edit(BoxHeading $boxHeading)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BoxHeading  $boxHeading
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BoxHeading $boxHeading)
    {
        $boxHeading->update($request->all());

        return message('Box heading updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BoxHeading  $boxHeading
     * @return \Illuminate\Http\Response
     */
    public function destroy(BoxHeading $boxHeading)
    {
        if($boxHeading->delete())
        return message('Box heading archived successfully');

        return message('Something went wrong', 400);
    }
}
