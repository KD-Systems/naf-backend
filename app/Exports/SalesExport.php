<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class SalesExport implements FromCollection, WithHeadings, WithColumnWidths
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
            "Date",
            'Invoice No',
            "Company",
            "total",
            // "Part Name",
            // "Part Number",
            // "Quantity",
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 10,
            'C' => 35,
            'D' => 10,
        ];
    }
}
