<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromCollection, WithHeadings, WithColumnWidths, ShouldAutoSize, WithStyles
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
        $total = $this->sales->sum('total_value' ?? 0);
        $salesWithTotal = $this->sales->push(['', '', 'Total', $total]);
        return $salesWithTotal;
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
    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        return [
            $lastRow => ['font' => ['bold' => true]],
        ];
    }
}
