<?php

namespace App\Http\Controllers;

use App\Http\Resources\PartStockAlertCollection;
use App\Http\Resources\RecentSaleCollection;
use App\Http\Resources\TopCustomerCollection;
use App\Http\Resources\TopSellingCollection;
use App\Models\DeliveryNote;
use App\Models\PartItem;
use App\Models\PartStock;
use App\Models\StockHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function sellPurchase()
    {
        $stocks = StockHistory::with('stock')->whereYear('created_at', Carbon::now()->year)->get();
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
        // $stocks = StockHistory::with('company')->where('type', 'deduction')->whereYear('created_at', Carbon::now()->year)->take(10)->orderBy('created_at', 'DESC')->get();
        // foreach ($stocks as $key => $stock) {
        //     $stock->stock->part->aliases;
        // }

        $soldItems = PartItem::join('delivery_notes', function ($join) {
            $join->on('delivery_notes.id', '=', 'part_items.model_id')
                ->where('part_items.model_type', DeliveryNote::class);
        })
            ->join('invoices', 'invoices.id', '=', 'delivery_notes.invoice_id')
            ->join('companies', 'companies.id', '=', 'invoices.company_id')
            ->join('parts', 'parts.id', '=', 'part_items.part_id')
            ->join('part_aliases', 'part_aliases.part_id', '=', 'part_items.part_id')
            ->select('part_items.id', 'part_items.created_at', 'part_items.quantity', 'part_aliases.name as part_name', 'part_aliases.part_number', 'companies.name as company_name','parts.id as part_id')->latest();

            // return DeliveryNote::collection($soldItems->get());
            // return $soldItems;

        return RecentSaleCollection::collection($soldItems->take(10)->groupBy('part_items.id')->get());
    }

    public function TopCustomers(){

        $soldItems = PartItem::join('delivery_notes', function ($join) {
            $join->on('delivery_notes.id', '=', 'part_items.model_id')
                ->where('part_items.model_type', DeliveryNote::class);
        })
            ->join('invoices', 'invoices.id', '=', 'delivery_notes.invoice_id')
            ->join('companies', 'companies.id', '=', 'invoices.company_id')
            ->join('parts', 'parts.id', '=', 'part_items.part_id')
            ->join('part_aliases', 'part_aliases.part_id', '=', 'part_items.part_id')
            ->select('part_items.id', 'part_items.created_at', 'part_items.quantity', 'part_aliases.name as part_name', 'part_aliases.part_number', 'companies.name as company_name','parts.id as part_id')
            ->groupBy(['company_name'])->orderBy('part_items.quantity','DESC')->take(5)->get();

        return TopCustomerCollection::collection($soldItems);
    }
}
