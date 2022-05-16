<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryNote;
use App\Http\Resources\GatePassCollection;
use App\Http\Resources\GatePassResource;

class GatePassController extends Controller
{
    public function GatePassDetails(Request $request){

        $delivery_notes = DeliveryNote::with(
            'invoice',
            'invoice.company',
            'invoice.quotation.requisition.machines:id,machine_model_id',
            'invoice.quotation.requisition.machines.model:id,name',
            'partItems',
            'partItems.Part.aliases',

        )->where('dn_number', 'LIKE', '%' . $request->q . '%')->first();


        //Search the Delivery notes
        // if ($request->q)
        //     $delivery_notes = $delivery_notes->where(function ($delivery_notes) use ($request) {
        //         //Search the data by company name and id
        //         $delivery_notes = $delivery_notes->where('dn_number', 'LIKE', '%' . $request->q . '%');
        //     });

            // return $delivery_notes;
        // if ($request->rows == 'all')
        //     return GatePassCollection::collection($delivery_notes->get());
        // $delivery_notes = $delivery_notes->paginate($request->get('rows', 10));

        // return GatePassCollection::collection($delivery_notes);
        return GatePassResource::make($delivery_notes);
    }
}
