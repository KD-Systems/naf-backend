<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Part;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use App\Models\PartAlias;
use App\Imports\PartsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\PartResource;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\PartCollection;

class PartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Authorize the user
        abort_unless(access('parts_access'), 403);

        $parts = Part::leftJoin('part_aliases', 'part_aliases.part_id', '=', 'parts.id')
            ->leftJoin('machines', 'part_aliases.machine_id', '=', 'machines.id')
            ->leftJoin('part_headings', 'part_headings.id', 'part_aliases.part_heading_id');

        //Search the employees
        if ($request->q)
            $parts = $parts->where(function ($p) use ($request) {
                $p = $p->where('parts.unique_id', 'LIKE', '%' . $request->q . '%');

                //Search the by aliases name and part number
                $p = $p->orWhere('part_aliases.name', 'LIKE', '%' . $request->q . '%');
                $p = $p->orWhere('part_aliases.part_number', 'LIKE', '%' . $request->q . '%');

                //Search the data by machine name
                $p = $p->orWhere('machines.name', 'LIKE', '%' . $request->q . '%');

                // //Search the data by part headings name
                $p = $p->orWhere('part_headings.name', 'LIKE', '%' . $request->q . '%');
            });

        //Filter data with the warehouse
        $parts = $parts->when($request->warehouse_id, function ($q) {
            $q->whereHas('stocks', function ($qe) {
                $qe->where('warehouse_id', request()->warehouse_id);
            });
        });

        //Select the fields  and group them
        $parts = $parts->select([
            'parts.id',
            'parts.image',
            'part_aliases.name as name',
            'part_headings.name as heading_name',
            'part_aliases.part_number as part_number',
            'machines.name as machine_name',
        ])->groupBy('parts.id');

        //Ordering the collection
        $order = json_decode($request->get('order'));
        if (isset($order->column)) {

            //Order by name and part number
            if (in_array($order->column, ['name', 'part_number']))
                $parts = $parts->orderBy($order->column, $order->direction);

            //Order by machine name
            if ($order->column == 'machine')
                $parts = $parts->orderBy('machine_name', $order->direction);

            //Order by part heading
            if ($order->column == 'heading')
                $parts = $parts->orderBy('heading_name', $order->direction);

            //Order by part number
            if ($order->column == 'part_number')
                $parts = $parts->orderBy('part_number', $order->direction);
        }

        //Paginate the collection
        $parts = $parts->paginate($request->get('rows', 10));

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

        //Authorize the user
        abort_unless(access('parts_create'), 403);


        $request->validate([
            // 'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'part_heading_id.*' => 'required|exists:part_headings,id',
            'machine_id.*' => 'required|exists:machines,id',
            'name' => 'required|unique:part_aliases,name|max:255',
            'part_number.*' => 'required|string|max:255|unique:part_aliases',
            'description' => 'nullable|string',
        ]);

        try {
            $arms = explode(',', $request->arm);

            if (!count($arms))
                $this->insertPart($request);

            foreach ($arms as $key => $arm) {
                $this->insertPart($request, $arm);
            }

            return message('Part created successfully', 200);



            //Disable the logging during update of barcode and unique ID
            activity()->disableLogging();
        } catch (\Throwable $th) {
            return message($th->getMessage(), 400);
        }
    }

    public function insertPart($request, $arm = null)
    {
        DB::transaction(function () use ($request) {
            $newParts =   json_decode($request->parts);
            $data = $request->only([
                'description',
            ]);
            $data['arm'] = $arm ?? $request->arm;

            //Check if the request has an image
            if ($request->hasFile('image'))
                $data['image'] = $request->file('image')->store('part-images');

            foreach ($newParts as $key => $value) {

                $part = Part::create($data);
                $data['unique_id'] = str_pad('2022' . $part->id, 6, 0, STR_PAD_LEFT);
                $barcode = new DNS1D;
                $data['barcode'] =  $barcode->getBarcodePNG($data['unique_id'], 'I25', 2, 60, array(1, 1, 1), true);
                $part->update($data);

                $part->aliases()->create(collect($value)->toArray() + [
                    'name' => $request->name
                ]);
            }
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Part  $part
     * @return \Illuminate\Http\Response
     */
    public function show(Part $part)
    {
        //Authorize the user
        abort_unless(access('parts_show'), 403);

        $part->load('aliases', 'aliases.machine', 'aliases.partHeading', 'stocks.warehouse');

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
        //Authorize the user
        abort_unless(access('parts_edit'), 403);

        $request->validate([
            'description' => 'nullable|string',
            'remarks' => 'nullable|string'
        ]);

        try {
            $data = $request->only('description', 'remarks');
            //Check if the request has an image
            if ($request->hasFile('image'))
                $data['image'] = $request->file('image')->store('part-images');

            if (!$part->unique_id) {
                $data['unique_id'] = str_pad('2022' . $part->id, 6, 0, STR_PAD_LEFT);
            }

            if ($part->unique_id && !$part->barcode) {
                $barcode = new DNS1D;
                $data['barcode'] = $barcode->getBarcodePNG($data['unique_id'], 'I25', 2, 60, array(1, 1, 1), true);
            }

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
        //Authorize the user
        abort_unless(access('parts_delete'), 403);

        if ($part->delete())
            return message('Part deleted successfully');

        return message('Something went wrong', 400);
    }

    /**
     * Import the parts and the related data along with them
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        // $barcode = new DNS1D;
        // $barcode_data = $barcode->getBarcodePNG(20222391, 'I25', 2, 60, array(1, 1, 1), true);
        // echo '<img src="data:image/png;base64,' . $barcode_data . '" alt="barcode"   />';
        // return false;
        Excel::import(new PartsImport, $request->file('file'));

        return message('Parts imported succesfully');
    }

    // public function barcode()
    // {
    //     $barcode = DNS1D::getBarcodePNG('4445645656', 'C39+');
    //     return $barcode;
    // }
}
