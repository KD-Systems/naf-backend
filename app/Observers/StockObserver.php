<?php

namespace App\Observers;

use App\Models\PartStock;
use App\Models\StockHistory;

class StockObserver
{
    /**
     * Handle the PartStock "created" event.
     *
     * @param  \App\Models\PartStock  $partStock
     * @return void
     */
    public function created(PartStock $partStock)
    {
        //  info($partStock);
         $partPrevious =0;
        $partcurrent =$partStock->unit_value;
        if($partStock->isDirty('unit_value')){
            StockHistory::create([
                'part_stock_id' => $partStock->id,
                'prev_unit_value' => $partPrevious,
                'current_unit_value' => $partcurrent,
                'type' => $partPrevious > $partcurrent ? "deduction" : "addition",
                'remarks' => "New stock added",
            ]);
        }
    }

    /**
     * Handle the PartStock "updated" event.
     *
     * @param  \App\Models\PartStock  $partStock
     * @return void
     */
    public function updating(PartStock $partStock)
    {
        $partPrevious =$partStock->getOriginal('unit_value');
        $partcurrent =$partStock->unit_value;
        if($partStock->isDirty('unit_value')){
            StockHistory::create([
                'company_id' => request()->invoice['company']['id'] ?? 0,
                'part_stock_id' => $partStock->id,
                'prev_unit_value' => $partPrevious,
                'current_unit_value' => $partcurrent,
                'type' => $partPrevious > $partcurrent ? "deduction" : "addition",
                'remarks' => request('notes', request('invoice') ? 'Stock updated for an invoice: '.request('invoice')['invoice_number'] : 'Stock updated for unknown reason')
            ]);
        }
        // info($partStock);
    }

    public function updated(PartStock $partStock)
    {
        // if($partStock->wasChanged('unit_value')){
        //     // return $partStock;
        //     // return "something changed in updated";
        //     dd("something changed updated",$partStock);
        // }
    }

    /**
     * Handle the PartStock "deleted" event.
     *
     * @param  \App\Models\PartStock  $partStock
     * @return void
     */
    public function deleted(PartStock $partStock)
    {
        //
    }

    /**
     * Handle the PartStock "restored" event.
     *
     * @param  \App\Models\PartStock  $partStock
     * @return void
     */
    public function restored(PartStock $partStock)
    {
        //
    }

    /**
     * Handle the PartStock "force deleted" event.
     *
     * @param  \App\Models\PartStock  $partStock
     * @return void
     */
    public function forceDeleted(PartStock $partStock)
    {
        //
    }
}
