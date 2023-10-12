<?php

namespace App\Exports;;

use App\Models\PartStock;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ExportStockAlertParts implements WithColumnWidths, FromCollection, WithMapping, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return PartStock::with(['warehouse', 'part.aliases'])
            ->whereRaw('unit_value < stock_alert')
            ->orderBy('updated_at', 'DESC')->get();
    }

    /**
     * @var Shortenerurl $url
     */
    public function map($data): array
    {
        return [
            $data->part->aliases[0]->name ?? '-',
            $data->part->aliases[0]->part_number ?? '-',
            $data->warehouse->name ?? '-',
            $data->unit_value ?? 0,
        ];
    }

    public function headings(): array
    {
        return [
            "Product Name",
            "Part Number",
            "Ware House",
            "Remaining"
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 25,
            'C' => 35,
            'D' => 10

        ];
    }
}
