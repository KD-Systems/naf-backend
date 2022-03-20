<?php

namespace App\Http\Controllers;

use Milon\Barcode\DNS1D;
use App\Models\BoxHeading;
use Illuminate\Http\Request;
use App\Http\Resources\BoxHeadingResource;
use App\Http\Resources\BoxHeadingCollection;
use App\Http\Resources\BoxPartsCollection;

class BoxHeadingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $boxHeadings = BoxHeading::with('parts:id')->get();

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
        $request->validate([
            'name' => 'required|string|max:255|unique:box_headings,name',
            'description' => 'nullable|string'
        ]);

        //Grab the inputs
        $data = $request->only('name', 'description');

        //Generate unique ID for the BOX
        $lastBoxId = BoxHeading::latest()->value('id', 0);
        $data['unique_id'] = str_pad('2022' . $lastBoxId++, 6, 0, STR_PAD_LEFT);

        //Generate Bar Code for the BOX
        if ($data['unique_id']) {
            $barcode = new DNS1D;
            $data['barcode'] = $barcode->getBarcodePNG($data['unique_id'], 'I25', 2, 60, array(1, 1, 1), true);
        }

        //Create the box entry
        $box = BoxHeading::create($data);

        return message('Box created successfully');
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
     * Display the parts of the box
     *
     * @param  \App\Models\BoxHeading  $partStock
     * @return \Illuminate\Http\Response
     */
    public function parts(BoxHeading $box)
    {
        //Load the relational data
        $parts = $box->parts()
            ->with('aliases', 'machines')
            ->groupBy('id')
            ->get();

        return BoxPartsCollection::collection($parts);
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
        if ($boxHeading->delete())
            return message('Box heading archived successfully');

        return message('Something went wrong', 400);
    }
}
