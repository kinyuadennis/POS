<?php

namespace App\Exports\Concerns;

use PhpOffice\PhpSpreadsheet\Style\Fill;

trait ProductImportExportHeaderStyles
{
    /**
     * Header row styles for product import/export spreadsheets.
     */
    protected function productImportExportHeaderStyles(): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFDC2626'],
                ],
            ],
        ];
    }
}
