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
        
        $delivery_notes = DeliveryNote::with(
            'invoice',
            'invoice.company',
            'invoice.quotation.requisition.machines:id,machine_model_id',
            'invoice.quotation.requisition.machines.model:id,name',
            'partItems',
            'partItems.Part.aliases',
        );

        // Filtering
        $delivery_notes = $delivery_notes->when($request->start_date_format, function ($q) use($request) {
            $q->whereBetween('created_at',[$request->start_date_format,Carbon::parse($request->end_date_format)->endOfDay()]);
        });

        //Search the Delivery notes
        if ($request->q)
            $delivery_notes = $delivery_notes->where(function ($delivery_notes) use ($request) {
                //Search the data by company name and id 
                $delivery_notes = $delivery_notes->whereHas('invoice', fn ($q) => 
                $q->whereHas('company',fn($qc)=>
                    $qc = $qc->Where('name', 'LIKE', '%' . $request->q . '%')
                )   
            )->orWhere('created_at', 'LIKE', '%' . $request->q . '%');   
            });
           

            if ($request->rows == 'all')
                return DeliveryNote::collection($delivery_notes->get());
            $delivery_notes = $delivery_notes->paginate($request->get('rows', 10));
            return YearlySalesReportCollection::collection($delivery_notes);
    }

    public function MonthlySales(){
        
    }

}
