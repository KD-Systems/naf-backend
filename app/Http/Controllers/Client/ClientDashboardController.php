<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientPaymentHistoryDashboardCollection;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\PartStockAlertCollection;
use App\Http\Resources\RecentSaleCollection;
use App\Http\Resources\TopCustomerCollection;
use App\Http\Resources\TopSellingCollection;
use App\Http\Resources\YearlySalesReportCollection;
use App\Models\AdvancePaymentHistory;
use App\Models\DeliveryNote;
use App\Models\Invoice;
use App\Models\PartItem;
use App\Models\PartStock;
use App\Models\StockHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientDashboardController extends Controller
{
    public function companyDashboard()
    {
        $company = auth()->user()->details?->company;
        $due = $company->due_amount;
        $advanceAmount = AdvancePaymentHistory::with('company')->whereCompanyId($company->id)->latest()->first();
        $advance = $advanceAmount?->amount ?? 0;

        $soldItems = PartItem::join('delivery_notes', function ($join) {
            $join->on('delivery_notes.id', '=', 'part_items.model_id')
                ->where('part_items.model_type', DeliveryNote::class);
        })
            ->join('invoices', 'invoices.id', '=', 'delivery_notes.invoice_id')
            ->join('companies', 'companies.id', '=', 'invoices.company_id')
            ->where('companies.id', $company->id)
            ->join('parts', 'parts.id', '=', 'part_items.part_id')
            ->join('part_aliases', 'part_aliases.part_id', '=', 'part_items.part_id')
            ->select('part_items.id', 'part_items.created_at', 'part_items.quantity', 'part_items.total_value', 'part_aliases.name as part_name', 'part_aliases.part_number', 'companies.name as company_name');
        $sales = $soldItems->groupBy('part_items.id')
            ->sum('total_value');


        $stocks = StockHistory::with(['stock' => function ($query) {
            $query->withTrashed();
        }])->selectRaw('part_stock_id, sum(prev_unit_value)- sum(current_unit_value) as totalSell')->where('type', 'deduction')->where('remarks', '!=', 'Stock updated for unknown reason')->where('company_id', $company->id)->groupBy('part_stock_id')->orderBy('totalSell', 'DESC')->take(5)->get();

        foreach ($stocks as $key => $stock) {
            $stock->stock?->part?->aliases;
        }
        $top_product = TopSellingCollection::collection($stocks);

        $deliveryNotes = Invoice::with('partItems')->where('company_id', $company->id)->whereYear('created_at', Carbon::now()->year)->get();

        $monthWise = [];
        foreach ($deliveryNotes as $key => $note) {
            $monthWise['monthly'][$note->created_at->format('M')] = isset($monthWise['monthly'][$note->created_at->format('M')]) ?
                $monthWise['monthly'][$note->created_at->format('M')] + $note->grand_total + $note->previous_due : $note->grand_total + $note->previous_due;
        }

        if (count($monthWise)) {
            $monthWise['total'] = array_sum($monthWise['monthly']);
        }

        return response()->json([
            'due' => $due,
            'advance' => $advance,
            'sales' => $sales,
            'top_product' => $top_product,
            'monthWise' => $monthWise
        ]);
    }
}
