<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Invoice;

use App\Models\PartItem;
use App\Exports\SalesExport;
use App\Exports\StockExport;
use App\Models\DeliveryNote;
use App\Models\StockHistory;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Filesystem\Filesystem;
use App\Http\Resources\YearlySalesReportCollection;
use App\Http\Resources\StockHistoryCollection;

class ReportsController extends Controller
{
    // Sales Report
    public function sales(Request $request)
    {
        //Authorize the user
        abort_unless(access('sales_report_access'), 403);

        $invoices = Invoice::join('companies', 'companies.id', 'invoices.company_id')
        ->select('invoices.id', 'invoices.company_id', 'companies.name as company_name')
        ->selectRaw('sum(invoices.grand_total) as grand_total')
        ->selectRaw('sum(invoices.sub_total) as sub_total')
        ->selectRaw('group_concat(invoices.invoice_number) as invoice_numbers')
        ->selectRaw('group_concat(invoices.created_at) as dates')
        ->selectRaw('group_concat(invoices.id) as invoice_ids')
        ->groupBy('invoices.company_id');

        $invoices->when($request->q, function ($q) use ($request) {
            return $q->where('company_name', 'like', '%' . $request->q . '%');
        });

        // Filtering with year
        $invoices->when($request->year, function ($q) use ($request) {
            return $q->whereYear('invoices.created_at', $request->year);
        });

        // Filtering with month
        $invoices->when($request->month, function ($q) use ($request) {
            return $q->whereMonth('invoices.created_at', $request->month);
        });

        //Filter company
        $invoices->when($request->company_id, function ($q) use ($request) {
            return $q->where('invoices.company_id', $request->company_id);
        });

        $invoices = $invoices->paginate($request->get('rows', 10));

        return YearlySalesReportCollection::collection($invoices);
    }

    public function salesExport(Request $request)
    {
        //Authorize the user
        abort_unless(access('sales_report_export'), 403);

        $file = new Filesystem;
        $file->cleanDirectory('uploads/exported-orders');

        $invoices = Invoice::join('companies', 'companies.id', 'invoices.company_id')
        ->select('invoices.*', 'companies.name as company_name')
        ->selectRaw('(select sum(payment_histories.amount) from (select * from payment_histories where payment_histories.invoice_id = invoices.id and payment_histories.payment_mode = "cash") as payment_histories) as cash_amount')
        ->selectRaw('(select sum(payment_histories.amount) from (select * from payment_histories where payment_histories.invoice_id = invoices.id and payment_histories.payment_mode = "bank") as payment_histories) as bank_amount')
        ->selectRaw('(select sum(payment_histories.amount) from (select * from payment_histories where payment_histories.invoice_id = invoices.id and payment_histories.payment_mode = "check") as payment_histories) as check_amount')
        ->selectRaw('(select sum(payment_histories.amount) from (select * from payment_histories where payment_histories.invoice_id = invoices.id and payment_histories.payment_mode = "card") as payment_histories) as card_amount')
        ->selectRaw('(select sum(payment_histories.amount) from (select * from payment_histories where payment_histories.invoice_id = invoices.id and payment_histories.payment_mode = "advance") as payment_histories) as advance_amount')
        ->selectRaw('(select sum(payment_histories.amount) from (select * from payment_histories where payment_histories.invoice_id = invoices.id and payment_histories.payment_mode = "return") as payment_histories) as return_amount')
        ->groupBy('invoices.company_id');

        $invoices->when($request->q, function ($q) use ($request) {
            return $q->where('company_name', 'like', '%' . $request->q . '%');
        });

        // Filtering with year
        $invoices->when($request->year, function ($q) use ($request) {
            return $q->whereYear('invoices.created_at', $request->year);
        });

        // Filtering with month
        $invoices->when($request->month, function ($q) use ($request) {
            return $q->whereMonth('invoices.created_at', $request->month);
        });

        //Filter company
        $invoices->when($request->company_id, function ($q) use ($request) {
            return $q->where('invoices.company_id', $request->company_id);
        });

        $export = new SalesExport($invoices->get());
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
        $deliveryNotes = Invoice::with('partItems')->whereYear('created_at', Carbon::now()->year)
            // ->whereHas('partItems', function ($q) {
            //     $q->where('total_value', '>', 0);
            // })
            // ->whereHas('quotation.requisition', fn ($q) => $q->where('type', 'purchase_request'))
            // ->sum('grand_total')
            // ->sum('previous_due')
            // ->whereBetween('created_at', [now()->subMonths(7), now()])
            ->get();



        //getting month wise quantity
        $monthWise = [];
        foreach ($deliveryNotes as $key => $note) {
            $monthWise['monthly'][$note->created_at->format('M')] = isset($monthWise['monthly'][$note->created_at->format('M')]) ?
                $monthWise['monthly'][$note->created_at->format('M')] + $note->grand_total + $note->previous_due : $note->grand_total + $note->previous_due;
        }

        if (count($monthWise)) {
            $monthWise['total'] = array_sum($monthWise['monthly']);
        }

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
            ->map(function ($data) {
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
            ->orderBy('stock_histories.id', 'desc')
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
