<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientPaymentHistoryDashboardCollection;
use App\Http\Resources\PartStockAlertCollection;
use App\Http\Resources\RecentSaleCollection;
use App\Http\Resources\TopCustomerCollection;
use App\Http\Resources\TopSellingCollection;
use App\Models\DeliveryNote;
use App\Models\Invoice;
use App\Models\PartItem;
use App\Models\PartStock;
use App\Models\PaymentHistories;
use App\Models\StockHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function sellPurchase()
    {
        $stocks = StockHistory::with('stock')->whereYear('created_at', Carbon::now()->year)->get();
        $buy = 0;
        $sell = 0;
        $profit = 0;
        foreach ($stocks as $key => $stock) {
            if ($stock->type == 'addition' || $stock->remarks == 'Stock updated for unknown reason') {
                $price = $stock->stock?->yen_price;
                $unit = $stock->current_unit_value - $stock->prev_unit_value;
                $total = $unit * $price;
                $buy = $buy + $total;
            } else {
                $price = $stock->stock?->selling_price;
                $unit = $stock->prev_unit_value - $stock->current_unit_value;
                $total = $unit * $price;
                $sell = $sell + $total;

                $profit_per_unit = $stock->stock?->selling_price - $stock->stock?->yen_price;
                $unit = $stock->prev_unit_value - $stock->current_unit_value;
                $total = $unit * $profit_per_unit;
                $profit = $profit + $total;
            }
        }

        return response()->json(['sell' => $sell, 'buy' => $buy, 'profit' => $profit]);
    }

    public function TopSellingProductMonthly()
    {

        $stocks = StockHistory::selectRaw('part_stock_id, sum(prev_unit_value)- sum(current_unit_value) as totalSell')->where('type', 'deduction')->where('remarks', '!=', 'Stock updated for unknown reason')->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->groupBy('part_stock_id')->orderBy('totalSell', 'DESC')->take(5)->get();

        foreach ($stocks as $key => $stock) {
            $stock->stock?->part?->aliases;
        }
        return TopSellingCollection::collection($stocks);
    }

    public function TopSellingProductYearly()
    {

        $stocks = StockHistory::selectRaw('part_stock_id, sum(prev_unit_value)- sum(current_unit_value) as totalSell')->where('type', 'deduction')->where('remarks', '!=', 'Stock updated for unknown reason')->whereYear('created_at', Carbon::now()->year)->whereYear('created_at', Carbon::now()->year)->groupBy('part_stock_id')->orderBy('totalSell', 'DESC')->take(5)->get();

        foreach ($stocks as $key => $stock) {
            $stock->stock?->part?->aliases;
        }
        return TopSellingCollection::collection($stocks);
    }

    public function StockAlert()
    {
        $stock = PartStock::with(['warehouse', 'part.aliases'])->whereRaw('unit_value < stock_alert')->whereYear('created_at', Carbon::now()->year)->orderBy('updated_at', 'DESC')->get();
        return PartStockAlertCollection::collection($stock);
    }

    public function RecentSales()
    {

        $soldItems = PartItem::join('delivery_notes', function ($join) {
            $join->on('delivery_notes.id', '=', 'part_items.model_id')
                ->where('part_items.model_type', DeliveryNote::class);
        })
            ->join('invoices', 'invoices.id', '=', 'delivery_notes.invoice_id')
            ->join('companies', 'companies.id', '=', 'invoices.company_id')
            ->join('parts', 'parts.id', '=', 'part_items.part_id')
            ->join('part_aliases', 'part_aliases.part_id', '=', 'part_items.part_id')
            ->select('part_items.id', 'part_items.created_at', 'part_items.quantity', 'part_aliases.name as part_name', 'part_aliases.part_number', 'companies.name as company_name', 'parts.id as part_id')->latest();

        return RecentSaleCollection::collection($soldItems->take(10)->groupBy('part_items.id')->get());
    }

    public function TopCustomers()
    {

        $stocks = StockHistory::with('company')->selectRaw('company_id, sum(prev_unit_value) -sum(current_unit_value) as totalSell')->where('type', 'deduction')->where('remarks', '!=', 'Stock updated for unknown reason')->whereYear('created_at', Carbon::now()->year)->groupBy('company_id')->orderBy('totalSell', 'DESC')->take(5)->get();

        return TopCustomerCollection::collection($stocks);
    }

    /////////////////////////////// customer dashboard ////////////////////////////////

    public function CustomerPayment()
    {

        $company = auth()->user()->details?->company;

        $paymentHistory = Invoice::with('quotation.requisition')->withCount(['paymentHistory as totalPaid' => function ($query) {
            $query->select(DB::raw("SUM(amount) as totalAmount"));
        }])->withCount(['partItems as totalAmount' => function ($query) {
            $query->select(DB::raw("SUM(total_value) as totalValue"));
        }])->where('company_id', $company->id)->get();

        $data = ClientPaymentHistoryDashboardCollection::collection($paymentHistory);
        $totalAmount = $data->sum('previous_due') + $data->sum('totalAmount');
        $totalPaid = $data->sum('totalPaid');
        $totalDue = $totalAmount - $totalPaid;

        return [
            'total_amount' => $totalAmount,
            'total_paid' => $totalPaid,
            'total_due' => $totalDue,
            'data' => $data,
        ];
    }
}
