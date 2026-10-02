<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class PurchaseSellCsvExport implements FromArray, WithHeadings, WithCustomCsvSettings, WithStrictNullComparison
{
    protected $rows;
    protected $meta;

    public function __construct(array $rows, array $meta)
    {
        $this->rows = $rows;
        $this->meta = $meta;
    }

    public function headings(): array
    {
        return ['Metric', 'Amount'];
    }

    public function array(): array
    {
        $precision = (int) session('business.currency_precision', 2);
        $out = [];
        $out[] = ['Date range', $this->meta['date_range'] ?? ''];
        $out[] = ['Location', $this->meta['location'] ?? ''];

        foreach ($this->rows as $row) {
            $out[] = [
                $row['label'] ?? '',
                round((float) ($row['value'] ?? 0), $precision),
            ];
        }

        return $out;
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
