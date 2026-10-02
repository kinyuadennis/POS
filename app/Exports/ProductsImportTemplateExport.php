<?php

namespace App\Exports;

use App\Exports\Concerns\ProductImportExportHeaderStyles;
use App\Utils\ProductImportExportColumns;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsImportTemplateExport implements FromArray, ShouldAutoSize, WithStyles
{
    use ProductImportExportHeaderStyles;

    public function array(): array
    {
        return [ProductImportExportColumns::headers()];
    }

    public function styles(Worksheet $sheet)
    {
        return $this->productImportExportHeaderStyles();
    }
}
