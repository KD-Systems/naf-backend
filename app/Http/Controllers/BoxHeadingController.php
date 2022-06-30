<?php

namespace App\Http\Controllers;

use Milon\Barcode\DNS1D;
use App\Models\BoxHeading;
use Illuminate\Http\Request;
use App\Http\Resources\BoxHeadingResource;
use App\Http\Resources\BoxHeadingCollection;
use App\Http\Resources\BoxPartsCollection;
use App\Rules\UniqueBox;

class BoxHeadingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Authorize the user
        abort_unless(access('box_heading_access'), 403);

        $boxHeadings = BoxHeading::with('parts:id')->paginate($request->get('rows', 10));

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
        //Authorize the user
        abort_unless(access('box_heading_create'), 403);

        $request->validate([
            'name' => ['required', 'string', 'max:255', new UniqueBox],
            'description' => 'nullable|string'
        ]);

        //Grab the inputs
        $data = $request->only('name', 'description');

        //Check if the box is extended one, then add the position number
        if ($request->has('extended')) :
            //Find out the last box with the same name
            $lastBoxName = BoxHeading::where('name', 'like', '%' . $data['name'] . '%')
                ->orderBy('name', 'desc')
                ->value('name');

            //Grab the position number of the box
            $position = intval(preg_replace("/" . $data['name'] . "/i", '', $lastBoxName));
            $position <= 1 && $position++;

            //Increment the box position if name found and attach with the current box name
            if ($lastBoxName)
                $data['name'] = $data['name'] . ' ' . (++$position);
        endif;

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
        //Authorize the user
        abort_unless(access('box_heading_show'), 403);

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
        //Authorize the user
        abort_unless(access('box_heading_parts_access'), 403);

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
        //Authorize the user
        abort_unless(access('box_heading_edit'), 403);

        $boxHeading->update($request->only('description'));

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
        //Authorize the user
        abort_unless(access('box_heading_delete'), 403);

        if ($boxHeading->delete())
            return message('Box heading archived successfully');

        return message('Something went wrong', 400);
    }
}
