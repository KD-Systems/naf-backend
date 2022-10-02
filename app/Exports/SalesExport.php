<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class SalesExport implements FromCollection,WithHeadings
{
    use Exportable;

    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->sales;
    }

    public function headings(): array
    {
        return [
            "Part Name",
            "Part Number",
            "Company",
            "Quantity",
            "total",
            "Created At"
        ];
    }
}
