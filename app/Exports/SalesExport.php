<?php

namespace App\Exports;
use App\Models\DeliveryNote;

use Maatwebsite\Excel\Concerns\FromCollection;

class SalesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DeliveryNote::all();
    }
}
