<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Invoice;

use App\Models\PartItem;
use App\Exports\SalesExport;
use App\Exports\StockExport;
use App\Models\DeliveryNote;
use App\Models\StockHistory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Filesystem\Filesystem;
use App\Http\Resources\YearlySalesReportCollection;
use App\Http\Resources\StockHistoryCollection;

class ReportsController extends Controller
{
    // Sales Report Start
    public function YearlySales(Request $request)
    {
        //Authorize the user
        abort_unless(access('sales_report_access'), 403);

        $soldItems = PartItem::join('delivery_notes', function ($join) {
            $join->on('delivery_notes.id', '=', 'part_items.model_id')
                ->where('part_items.model_type', DeliveryNote::class);
        })
            ->join('invoices', 'invoices.id', '=', 'delivery_notes.invoice_id')
            ->join('companies', 'companies.id', '=', 'invoices.company_id')
            ->join('parts', 'parts.id', '=', 'part_items.part_id')
            ->join('part_aliases', 'part_aliases.part_id', '=', 'part_items.part_id')
            ->select('part_items.id', 'part_items.created_at', 'part_items.quantity', 'part_aliases.name as part_name', 'part_aliases.part_number', 'companies.name as company_name');

        // return $soldItems->get();

        if ($request->q)
            $soldItems = $soldItems->where(function ($p) use ($request) {
                //Search the data by aliases name and part number
                $p = $p->orWhere('part_aliases.name', 'LIKE', '%' . $request->q . '%');
                $p = $p->orWhere('part_aliases.part_number', 'LIKE', '%' . $request->q . '%');
                $p = $p->orWhere('companies.name', 'LIKE', '%' . $request->q . '%');
            });

        // Filtering with month
        $soldItems = $soldItems->when($request->month, function ($q) use ($request) {
            $q->whereMonth('part_items.created_at', $request->month);
        });
        // Filtering with month
        $soldItems = $soldItems->when($request->year, function ($q) use ($request) {
            $q->whereYear('part_items.created_at', $request->year);
        });

        // Filtering with date
        $soldItems = $soldItems->when($request->start_date_format, function ($q) use ($request) {
            $q->whereBetween('part_items.created_at', [$request->start_date_format, Carbon::parse($request->end_date_format)->endOfDay()]);
        });

        //Filter company
        $soldItems = $soldItems->when($request->company_id, function ($q) use ($request) {
            $q->where('companies.id', $request->company_id);
        });

        if ($request->rows == 'all')
            return DeliveryNote::collection($soldItems->get());

        $soldItems = $soldItems->groupBy('part_items.id')
            ->paginate($request->get('rows', 10));

        return YearlySalesReportCollection::collection($soldItems);
    }

    public function salesExport()
    {
        //Authorize the user
        abort_unless(access('sales_report_export'), 403);

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
            ->select('part_aliases.name as part_name', 'part_aliases.part_number', 'companies.name as company_name', 'part_items.quantity', 'part_items.created_at')->get();
        $export = new SalesExport($soldItems);
        $path = 'exported-orders/sales-' . time() . '.xlsx';

        Excel::store($export, $path);

        return response()->json([
            'url' => url('uploads/' . $path)
        ]);
    }
    // Sales Report End

    //for dashboard
    public function MonthlySales()
    {

        $deliveryNotes = DeliveryNote::with('partItems')
            ->withSum('partItems', 'quantity')
            // ->whereBetween('created_at', [now()->subMonths(7), now()])
            ->get();

        //getting month wise quantity
        $monthWise = [];
        foreach ($deliveryNotes as $key => $note) {
            $monthWise['monthly'][$note->created_at->format('M')] = isset($monthWise['monthly'][$note->created_at->format('M')]) ?
                $monthWise['monthly'][$note->created_at->format('M')] + $note->part_items_sum_quantity : $note->part_items_sum_quantity;
        }

        $monthWise['total'] = array_sum($monthWise['monthly']);

        return $monthWise;
    }

    //for dashboard weekly sales report
    public function WeeklySales(Request $request)
    {
        Carbon::setWeekStartsAt(Carbon::SATURDAY);
        Carbon::setWeekEndsAt(Carbon::FRIDAY);

        $year =  $request->has('year') ? $request->year : now()->year;

        $deliveryNotes = DeliveryNote::withSum('partItems', 'quantity')
            ->whereYear('created_at', $year)
            ->get()
            ->whereNotNull('part_items_sum_quantity')
            ->groupBy(function ($item, $key) {
                return [Carbon::parse($item->created_at)->week];
            })
            ->map(function($data){
               return $data->sum('part_items_sum_quantity');
            })
            ->all();

        return response()->json([
            'weekly' => $deliveryNotes,
            'total' => array_sum($deliveryNotes)
        ]);
    }

    // Stock Report Start
    public function StockHistory(Request $request)
    {
        //Authorize the user
        abort_unless(access('stock_report_access'), 403);

        $stockHistory = StockHistory::join('part_stocks', 'part_stocks.id', '=', 'stock_histories.part_stock_id')
            ->join('box_headings', 'part_stocks.box_heading_id', '=', 'box_headings.id')
            ->join('warehouses', 'part_stocks.warehouse_id', '=', 'warehouses.id')
            ->join('part_aliases', 'part_aliases.part_id', '=', 'part_stocks.part_id')
            ->select('part_aliases.name as part_name', 'part_stocks.part_id as part_id', 'stock_histories.*', 'box_headings.name as box_heading_name', 'box_headings.id as box_heading_id', 'warehouses.name as warehouse_name', 'warehouses.id as warehouse_id')
            ->groupBy('stock_histories.id');

        if ($request->q)
            $stockHistory = $stockHistory->where(function ($p) use ($request) {
                //Search the data by aliases name and part number
                $p = $p->orWhere('part_aliases.name', 'LIKE', '%' . $request->q . '%');
            });

        if ($request->rows == 'all')
            return StockHistory::collection($stockHistory->get());

        $stockHistory = $stockHistory->paginate($request->get('rows', 10));
        return StockHistoryCollection::collection($stockHistory);
    }

    public function StockHistoryExport()
    {
        //Authorize the user
        abort_unless(access('stock_report_export'), 403);

        $file = new Filesystem;
        $file->cleanDirectory('uploads/exported-stock');

        $stockHistory = StockHistory::join('part_stocks', 'part_stocks.id', '=', 'stock_histories.part_stock_id')
            ->join('box_headings', 'part_stocks.box_heading_id', '=', 'box_headings.id')
            ->join('warehouses', 'part_stocks.warehouse_id', '=', 'warehouses.id')
            ->join('part_aliases', 'part_aliases.part_id', '=', 'part_stocks.part_id')
            ->select('part_aliases.name as part_name', 'box_headings.name as box_heading_name', 'warehouses.name as warehouse_name', 'stock_histories.prev_unit_value', 'stock_histories.current_unit_value')->get();

        $export = new StockExport($stockHistory);
        $path = 'exported-stock/stock-' . time() . '.xlsx';

        Excel::store($export, $path);

        return response()->json([
            'url' => url('uploads/' . $path)
        ]);
    }
    // Stock Report End

}
