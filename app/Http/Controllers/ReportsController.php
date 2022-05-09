<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\PartItem;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

use App\Http\Resources\YearlySalesReportResource;
use App\Http\Resources\YearlySalesReportCollection;
use App\Models\Part;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function YearlySales(Request $request){ 
        // $date1 = '2022-05-07';
        // $date2 = '2022-05-08';
        $date1 = '';
        $date2 = '';
        // $deliveryNotes = DeliveryNote::all();
        // return $deliveryNotes;
        $delivery_notes = DeliveryNote::with(
            'invoice',
            'invoice.company',
            'invoice.quotation.requisition.machines:id,machine_model_id',
            'invoice.quotation.requisition.machines.model:id,name',
            'partItems',
            'partItems.Part.aliases',
        )->whereBetween('created_at',[$date1,Carbon::parse($date2)->endOfDay()]);
        //->whereBetween('created_at',[$date1,Carbon::parse($date2)->endOfDay()])
        
        // Filtering
        // $delivery_notes = $delivery_notes->when($date1, function ($q) use($date1,$date2) {
        //     $q->whereBetween('created_at',[$date1,Carbon::parse($date2)->endOfDay()]);
        // });

        //Search the Delivery notes
        if ($request->q)
            $delivery_notes = $delivery_notes->where(function ($delivery_notes) use ($request) {
                //Search the data by company name and id
                $delivery_notes = $delivery_notes->where('dn_number', 'LIKE', '%' . $request->q . '%');
            });
        if ($request->rows == 'all')
            return DeliveryNote::collection($delivery_notes->get());
        $delivery_notes = $delivery_notes->paginate($request->get('rows', 10));

        return YearlySalesReportCollection::collection($delivery_notes);
    }
}
