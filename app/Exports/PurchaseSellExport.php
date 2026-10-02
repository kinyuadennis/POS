<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PurchaseSellExport implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    protected $rows;
    protected $meta;
    protected $header_row = 7;

    public function __construct(array $rows, array $meta)
    {
        $this->rows = $rows;
        $this->meta = $meta;
    }

    public function array(): array
    {
        $out = [];
        $out[] = ['PURCHASE & SALE REPORT'];
        $out[] = ['Generated', $this->meta['generated_at'] ?? ''];
        $out[] = [''];
        $out[] = ['Date range', $this->meta['date_range'] ?? ''];
        $out[] = ['Location', $this->meta['location'] ?? ''];
        $out[] = [''];
        $out[] = ['Metric', 'Amount'];
        $this->header_row = count($out);

        foreach ($this->rows as $row) {
            $out[] = [
                $row['label'] ?? '',
                (float) ($row['value'] ?? 0),
            ];
        }

        return $out;
    }

    public function title(): string
    {
        return 'Purchase & Sale';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 36,
            'B' => 22,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $header_row = $this->header_row;
                $first_data = $header_row + 1;
                $last_data = $header_row + count($this->rows);

                $sheet->mergeCells('A1:B1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1F2937']],
                ]);
                $sheet->getStyle('A2:A5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '6B7280']],
                ]);

                $sheet->getStyle("A{$header_row}:B{$header_row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("A{$header_row}:B{$header_row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF1E4B6E');

                if (count($this->rows) > 0) {
                    $sheet->getStyle("A{$first_data}:B{$last_data}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'E5E7EB'],
                            ],
                        ],
                    ]);
                    $sheet->getStyle("B{$first_data}:B{$last_data}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("B{$first_data}:B{$last_data}")
                        ->getNumberFormat()->setFormatCode($this->currencyFormat());
                }

                $sheet->setShowGridlines(false);
            },
        ];
    }

    private function currencyFormat(): string
    {
        $symbol = str_replace('"', '', (string) session('currency.symbol', ''));
        $precision = (int) session('business.currency_precision', 2);
        $decimals = $precision > 0 ? '.'.str_repeat('0', $precision) : '';
        $num = '#,##0'.$decimals;

        if ($symbol === '') {
            return $num;
        }

        $escaped = str_replace(['\\', '"'], ['\\\\', '""'], $symbol);
        $placement = session('business.currency_symbol_placement', 'before');

        return $placement === 'after'
            ? $num.'" '.$escaped.'"'
            : '"'.$escaped.' "'.$num;
    }
}
