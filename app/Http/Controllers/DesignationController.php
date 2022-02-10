<?php

namespace App\Http\Controllers;

use App\Http\Resources\DesignationResource;
use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Authorize the user
        abort_unless(access('designations_access'), 403);

        $designations = Designation::with('employees.user')->get();
        return DesignationResource::collection($designations);
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
        abort_unless(access('designations_create'), 403);


        $request->validate([
            'name' => 'required|string',
        ]);
        $designation = new Designation();
        $designation->name = $request->name;
        $designation->description = $request->description;


        $designation->save();

        return response()->json(['message' => "Designation created successfully"], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function show(Designation $designation)
    {
        //Authorize the user
        abort_unless(access('designations_show'), 403);



        return DesignationResource::make($designation);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function edit(Designation $designation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Designation $designation)
    {
        //Authorize the user
        abort_unless(access('designations_edit'), 403);


        if (!$designation)
            return response()->json(['message' => 'Designation not found!'], 404);

        $request->validate([
            'name' => 'required|string',

        ]);

        $designation->update([
            'name' => $request->name,
            'description' => $request->description,

        ]);

        return response()->json(['message' => "Designation updated successfully"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Designation  $designation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Designation $designation)
    {
        //Authorize the user
        abort_unless(access('designations_delete'), 403);


        if ($designation->delete())
            return message('Designation deleted successfully');

        return message('Something went wrong', 400);
    }
}
