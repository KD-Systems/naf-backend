<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\PartItem;
use App\Exports\SalesExport;
use App\Models\DeliveryNote;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\YearlySalesReportCollection;
use Illuminate\Filesystem\Filesystem;

class ReportsController extends Controller
{
    public function YearlySales(Request $request)
    {

        $soldItems = PartItem::join('delivery_notes', function ($join) {
            $join->on('delivery_notes.id', '=', 'part_items.model_id')
                ->where('part_items.model_type', DeliveryNote::class);
        })
            ->join('invoices', 'invoices.id', '=', 'delivery_notes.invoice_id')
            ->join('companies', 'companies.id', '=', 'invoices.company_id')
            ->join('parts', 'parts.id', '=', 'part_items.part_id')
            ->join('part_aliases', 'part_aliases.part_id', '=', 'part_items.part_id')
            ->select('part_items.id', 'part_items.created_at','part_items.quantity', 'part_aliases.name as part_name', 'part_aliases.part_number', 'companies.name as company_name');

        if ($request->q)
            $soldItems = $soldItems->where(function ($p) use ($request) {
                //Search the data by aliases name and part number
                $p = $p->orWhere('part_aliases.name', 'LIKE', '%' . $request->q . '%');
                $p = $p->orWhere('part_aliases.part_number', 'LIKE', '%' . $request->q . '%');
                $p = $p->orWhere('companies.name', 'LIKE', '%' . $request->q . '%');
            });



        // Filtering
        $soldItems = $soldItems->when($request->start_date_format, function ($q) use ($request) {
            $q->whereBetween('created_at', [$request->start_date_format, Carbon::parse($request->end_date_format)->endOfDay()]);
        });

        if ($request->rows == 'all')
            return DeliveryNote::collection($soldItems->get());

        $soldItems = $soldItems->groupBy('part_items.id')
            ->paginate($request->get('rows', 10));

        return YearlySalesReportCollection::collection($soldItems);
    }

    public function salesExport()
    {
        $file = new Filesystem;
        $file->cleanDirectory('uploads/exported-orders');
        $soldItems = PartItem::join('delivery_notes', function ($join) {
            $join->on('delivery_notes.id', '=', 'part_items.model_id')
                ->where('part_items.model_type', DeliveryNote::class);
        })
            ->join('invoices', 'invoices.id', '=', 'delivery_notes.invoice_id')
            ->join('companies', 'companies.id', '=', 'invoices.company_id')
            ->join('parts', 'parts.id', '=', 'part_items.part_id')
            ->join('part_aliases', 'part_aliases.part_id', '=', 'part_items.part_id')
            ->select('part_aliases.name as part_name','part_aliases.part_number', 'companies.name as company_name','part_items.quantity', 'part_items.created_at')->get();
        $export = new SalesExport($soldItems);
        $path = 'exported-orders/sales-' . time() . '.xlsx';

        Excel::store($export, $path);

        return response()->json([
            'url' => url('uploads/' . $path)
        ]);
    }
}
