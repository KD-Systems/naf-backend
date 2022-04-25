<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeliveryNotesResource;
use App\Http\Resources\DeliveryNotesCollection;
use App\Models\DeliveryNote;
use App\Models\PartItem;

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
        $delivery_notes = DeliveryNote::with(
            'invoice',
            'invoice.company',
            'invoice.quotation.requisition.machines:id,machine_model_id',
            'invoice.quotation.requisition.machines.model:id,name',
            'invoice.quotation.partItems.part.aliases'
        );
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
        return $request->all();
        // // $items = collect($request->invoice['part_items']);
        // //     return $items['unit_value'];
        try {
<<<<<<< HEAD
        //     // if (DeliveryNote::where('invoice_id', $request->id)->doesntExist()) {
                $deliveryNote = DeliveryNote::create([
                    'invoice_id' => $request->invoice['id'],
                    // 'remarks' => $request->remarks,
=======
            //Store the data
            if (DeliveryNote::where('invoice_id', $request->invoice['id'])->doesntExist()) {
                $deliveryNote = DeliveryNote::create([
                    'invoice_id' =>  $request->invoice['id'],
                    'remarks' => $request->remarks,
>>>>>>> main
                ]);

                $id = \Illuminate\Support\Facades\DB::getPdo()->lastInsertId();

                $data = DeliveryNote::findOrFail($id);
                // $str = str_pad($id, 4, '0', STR_PAD_LEFT); 

                $data->update([
                    'dn_number'   => 'DN'.date("Ym").$id,
                ]);
<<<<<<< HEAD
                // $item = collect($request->invoice['part_items']);
                // // return $item;
                // $item = $item->map(function ($dt) {
                //     return [
                //         'unit_value' => $dt['unit_value'],
                //         'total_value' => $dt['quantity'] * $dt['unit_value']
                //     ];
                // });
                // // return $item;
                $items = collect($request->part_items);
            // return $items;
            $items = $items->map(function ($dt) {
                return [
                    'part_id' => $dt['id'],
                    'quantity' => $dt['quantity'],
                    'unit_value' => $dt['unit_value'],
                    'total_value' => $dt['quantity'] * $dt['unit_value']
                ];
            });

        //    $partItems = $deliveryNote->partItems()->createMany($items);

        //    foreach ($partItems as $partItem) {
        //     $itemId= $partItem->id;
        //   }
        //   $data1 = PartItem::findOrFail($itemId);
        // //   return $data1->unit_value;
        //   $data1->update([
        //     'unit_value' => $item[0]['unit_value'],
        //     'total_value' => $item[0]['total_value']
        // ]);
           
=======
;
                $items = collect($request->part_items);
                $items = $items->map(function ($dt) {
                    return [
                        'part_id' => $dt['id'],
                        'quantity' => $dt['quantity'],
                    ];
                });


                $deliveryNote->partItems()->createMany($items);


>>>>>>> main

                return message('Delivery Note created successfully', 201, $data);
            // } 
            // else {
            //     return message('Delivery Note already exists', 422);
            // }
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
        $DeliveryNote->load(
            'invoice.quotation.requisition.machines:id,machine_model_id',
            'invoice.quotation.requisition.machines.model:id,name',
            'invoice.quotation.partItems.part.aliases'
        );
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
