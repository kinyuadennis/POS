<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OpeningStockTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function __construct(protected array $rows)
    {
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'PRODUCT NAME',
            'SKU',
            'LOCATION',
            'OPENING STOCK',
            'CURRENT STOCK',
            'PURCHASE PRICE',
        ];
    }
}
