<?php

namespace App\Exports;

use DateTime;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromView
{
    protected $data;
    protected $filters;

    public function __construct($data, $filters)
    {
        $this->data = $data;
        $this->filters = $filters;
    }

    public function view(): View
    {
        if ($this->filters['month'])
            $this->filters['month'] = DateTime::createFromFormat('!m', $this->filters['month'])->format('F');

        if ($this->filters['company_id'])
            $this->filters['company'] = $this->data->first()->company_name ?? null;

        return view('exports.sales', [
            'data' => $this->data,
            'filters' => $this->filters
        ]);
    }
}
