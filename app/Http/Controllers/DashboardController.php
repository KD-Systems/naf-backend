<?php

namespace App\Http\Controllers;

use App\Models\StockHistory;
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

    public function TopSellingProduct(){
        return $stocks = StockHistory::selectRaw('part_stock_id, sum(prev_unit_value)- sum(current_unit_value) as totalSell')->where('type','deduction')->groupBy('part_stock_id')->get();


    }
}
