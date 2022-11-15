<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class TransactionSummeryExport implements FromCollection, WithHeadings, WithColumnWidths
{
    use Exportable;

    protected $transactionSummnery;

    public function __construct($transactionSummnery)
    {
        $this->transactionSummnery = $transactionSummnery;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->transactionSummnery;
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

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 25,
            'D' => 15,
            'E' => 15,
            'F' => 10,
        ];
    }
}
