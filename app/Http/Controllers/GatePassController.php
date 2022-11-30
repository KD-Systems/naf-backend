<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryNote;
use App\Http\Resources\GatePassCollection;
use App\Http\Resources\GatePassResource;

class GatePassController extends Controller
{

    public function GatePassDetails(Request $request)
    {
        //Authorize the user
        abort_unless(access('gate_pass_access'), 403);

        $delivery_notes = DeliveryNote::with(
            'invoice',
            'invoice.company',
            'invoice.quotation.requisition.machines:id,machine_model_id',
            'invoice.quotation.requisition.machines.model:id,name',
            'partItems',
            'partItems.Part.aliases',

        )->where('dn_number', 'LIKE', '%' . $request->q . '%')->first();

        return GatePassResource::make($delivery_notes);
    }
}
