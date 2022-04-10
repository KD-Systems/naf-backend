<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeliveryNotesResource;
use App\Http\Resources\DeliveryNotesCollection;
use App\Models\DeliveryNote;

use Illuminate\Http\Request;

class DeliveryNotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $delivery_notes = DeliveryNote::with('invoice','invoice.company');
        if ($request->rows == 'all')
            return DeliveryNote::collection($delivery_notes->get());
        $delivery_notes = $delivery_notes->paginate($request->get('rows', 10));

        return DeliveryNotesCollection::collection($delivery_notes);
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
        // return $request->all();
        try {
            //Store the data
            if (DeliveryNote::where('invoice_id', $request->id)->doesntExist()) {
                $user = DeliveryNote::create([
                    'invoice_id' => $request->id,
                    'remarks' => $request->remarks,
                ]);

                $id = \Illuminate\Support\Facades\DB::getPdo()->lastInsertId();

                $data = DeliveryNote::findOrFail($id);
                // $str = str_pad($id, 4, '0', STR_PAD_LEFT);

                $data->update([
                    'dn_number'   => 'DN'.date("Ym").$id,
                ]);

                return message('Delivery Note created successfully', 201, $data);
            } else {
                return message('Delivery Note already exists', 422);
            }
        } catch (\Throwable $th) {
            return message(
                $th->getMessage(),
                400
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(DeliveryNote $DeliveryNote)
    {
        return DeliveryNotesResource::make($DeliveryNote);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
