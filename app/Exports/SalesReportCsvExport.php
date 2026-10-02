<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class SalesReportCsvExport implements FromArray, WithHeadings, WithCustomCsvSettings, WithStrictNullComparison
{
    protected $period_records;

    public function __construct(array $period_records)
    {
        $this->period_records = $period_records;
    }

    public function headings(): array
    {
        return ['Period', 'Orders', 'Items Sold', 'Total Sales'];
    }

    public function array(): array
    {
        $precision = (int) session('business.currency_precision', 2);
        $qty_precision = (int) session('business.quantity_precision', 2);
        $rows = [];

        foreach ($this->period_records as $rec) {
            $rows[] = [
                $rec['period_label'] ?? '',
                (int) ($rec['order_count'] ?? 0),
                round((float) ($rec['items_sold'] ?? 0), $qty_precision),
                round((float) ($rec['total_sales'] ?? 0), $precision),
            ];
        }

        return $rows;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => "\n",
            'use_bom' => true,
        ];
    }
}
