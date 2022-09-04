<?php

namespace App\Http\Controllers;

use App\Http\Resources\PartStockAlertCollection;
use App\Http\Resources\RecentSaleCollection;
use App\Http\Resources\TopCustomerCollection;
use App\Http\Resources\TopSellingCollection;
use App\Models\PartStock;
use App\Models\StockHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function sellPurchase()
    {
        $stocks = StockHistory::with('stock')->get();
        $intotal = 0;
        foreach ($stocks as $key => $stock) {
            if ($stock->type == 'addition') {
                $price = $stock->stock->yen_price;
                $unit = $stock->current_unit_value - $stock->prev_unit_value;
                $total = $unit * $price;
                $intotal = $intotal + $total;
            }
        }
        $buy = $intotal;

        $intotal = 0;
        foreach ($stocks as $key => $stock) {
            if ($stock->type == 'deduction') {
                $price = $stock->stock->selling_price;
                $unit = $stock->prev_unit_value - $stock->current_unit_value;
                $total = $unit * $price;
                $intotal = $intotal + $total;
            }
        }
        $sell = $intotal;

        $intotal = 0;
        foreach ($stocks as $key => $stock) {
            if ($stock->type == 'deduction') {
                $profit_per_unit = $stock->stock->selling_price - $stock->stock->yen_price;
                $unit = $stock->prev_unit_value - $stock->current_unit_value;
                $total = $unit * $profit_per_unit;
                $intotal = $intotal + $total;
            }
        }

        $profit = $intotal;
        return response()->json(['sell' => $sell, 'buy' => $buy, 'profit' => $profit]);
    }

    public function TopSellingProductMonthly()
    {

        $stocks = StockHistory::selectRaw('part_stock_id, sum(prev_unit_value)- sum(current_unit_value) as totalSell')->where('type', 'deduction')->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->groupBy('part_stock_id')->orderBy('totalSell', 'DESC')->take(5)->get();

        foreach ($stocks as $key => $stock) {
            $stock->stock->part->aliases;
        }
        return TopSellingCollection::collection($stocks);
    }

    public function TopSellingProductYearly()
    {

        $stocks = StockHistory::selectRaw('part_stock_id, sum(prev_unit_value)- sum(current_unit_value) as totalSell')->where('type', 'deduction')->whereYear('created_at', Carbon::now()->year)->whereYear('created_at', Carbon::now()->year)->groupBy('part_stock_id')->orderBy('totalSell', 'DESC')->take(5)->get();

        foreach ($stocks as $key => $stock) {
            $stock->stock->part->aliases;
        }
        return TopSellingCollection::collection($stocks);
    }

    public function StockAlert()
    {
        $stock = PartStock::with(['warehouse', 'part.aliases'])->where('unit_value', '<', 10)->whereYear('created_at', Carbon::now()->year)->take(5)->get();
        return PartStockAlertCollection::collection($stock);
    }

    public function RecentSales()
    {
        $stocks = StockHistory::with('company')->where('type', 'deduction')->whereYear('created_at', Carbon::now()->year)->take(10)->orderBy('created_at', 'DESC')->get();
        foreach ($stocks as $key => $stock) {
            $stock->stock->part->aliases;
        }

        return RecentSaleCollection::collection($stocks);
    }

    public function TopCustomers(){
        $stocks = StockHistory::with('company')->whereYear('created_at', Carbon::now()->year)->where('type', 'deduction')->groupBy('company_id')->orderBy('created_at', 'ASC')->latest()->take(5)->get();
        foreach ($stocks as $key => $stock) {
            $stock->stock->part->aliases;
        }

        return TopCustomerCollection::collection($stocks);
    }
}
