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
    public function index()
    {
        $delivery_notes = DeliveryNote::with('invoice')->get();
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
        try {
            //Store the data
            $user = DeliveryNote::create([
                'invoice_id' => $request->invoice_id,
                'dn_number' => $request->dn_number,
                'remarks' => $request->remarks,
            ]);

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
