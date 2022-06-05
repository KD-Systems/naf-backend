<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryNotesCollection;
use App\Models\DeliveryNote;
use Illuminate\Http\Request;

class ClientDeliveryNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $delivery_notes = DeliveryNote::with(
            'invoice',
            'invoice.company',
            'invoice.quotation.requisition.machines:id,machine_model_id',
            'invoice.quotation.requisition.machines.model:id,name',
            'partItems',
            'partItems.Part.aliases',

        );
        //Search the Delivery notes
        if ($request->q)
            $delivery_notes = $delivery_notes->where(function ($delivery_notes) use ($request) {
                //Search the data by company name and id
                $delivery_notes = $delivery_notes->where('dn_number', 'LIKE', '%' . $request->q . '%');
            });
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
