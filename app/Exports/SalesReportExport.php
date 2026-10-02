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
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class SalesReportExport implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    protected $period_records;
    protected $meta;
    protected $header_row = 11;

    public function __construct(array $period_records, array $meta)
    {
        $this->period_records = $period_records;
        $this->meta = $meta;
    }

    public function array(): array
    {
        $rows = [];
        $rows[] = ['SALES ANALYTICS — PERIOD PERFORMANCE'];
        $rows[] = ['Generated', $this->meta['generated_at'] ?? ''];
        $rows[] = [''];
        $rows[] = ['Date range', $this->meta['date_range'] ?? ''];
        $rows[] = ['Grouping', $this->meta['grouping'] ?? ''];
        $rows[] = ['Location', $this->meta['location'] ?? 'All locations'];
        $rows[] = ['Customer', $this->meta['customer'] ?? 'All customers'];
        $rows[] = ['Cashier / Staff', $this->meta['cashier'] ?? 'All cashiers'];
        $rows[] = [''];
        $rows[] = ['Period', 'Orders', 'Items sold', 'Total sales'];
        $this->header_row = count($rows);

        $total_orders = 0;
        $total_items = 0;
        $total_sales = 0;

        foreach ($this->period_records as $rec) {
            $orders = (int) ($rec['order_count'] ?? 0);
            $items = (float) ($rec['items_sold'] ?? 0);
            $sales = (float) ($rec['total_sales'] ?? 0);

            $total_orders += $orders;
            $total_items += $items;
            $total_sales += $sales;

            $rows[] = [
                $rec['period_label'] ?? '',
                $orders,
                $items,
                $sales,
            ];
        }

        $rows[] = ['Total', $total_orders, $total_items, $total_sales];

        return $rows;
    }

    public function title(): string
    {
        return 'Period Performance';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 38,
            'B' => 14,
            'C' => 16,
            'D' => 20,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $data_count = count($this->period_records);
                $header_row = $this->header_row;
                $first_data = $header_row + 1;
                $last_data = $header_row + max($data_count, 0);
                $total_row = $header_row + $data_count + 1;
                $last_col = 'D';

                $sheet->mergeCells('A1:D1');
                $sheet->getRowDimension(1)->setRowHeight(24);
                $sheet->getRowDimension($header_row)->setRowHeight(22);

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1F2937']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle('A2:A8')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '6B7280']],
                ]);
                $sheet->getStyle('B2:B8')->applyFromArray([
                    'font' => ['color' => ['rgb' => '111827']],
                ]);

                $header_range = "A{$header_row}:{$last_col}{$header_row}";
                $sheet->getStyle($header_range)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF1E4B6E'],
                        ],
                    ],
                ]);
                $sheet->getStyle($header_range)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF1E4B6E');
                foreach (range('A', $last_col) as $col) {
                    $sheet->getStyle("{$col}{$header_row}")->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FF1E4B6E');
                }
                $sheet->getStyle("A{$header_row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                if ($data_count > 0) {
                    $sheet->getStyle("A{$first_data}:{$last_col}{$last_data}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'E5E7EB'],
                            ],
                        ],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getStyle("A{$first_data}:A{$last_data}")
                        ->getAlignment()->setWrapText(true);
                    $sheet->getStyle("B{$first_data}:{$last_col}{$last_data}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("B{$first_data}:B{$last_data}")
                        ->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("C{$first_data}:C{$last_data}")
                        ->getNumberFormat()->setFormatCode($this->quantityFormat());
                    $sheet->getStyle("D{$first_data}:D{$last_data}")
                        ->getNumberFormat()->setFormatCode($this->currencyFormat());
                }

                $sheet->getStyle("A{$total_row}:{$last_col}{$total_row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '111827']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F3F4F6'],
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '1E4B6E'],
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E5E7EB'],
                        ],
                    ],
                ]);
                $sheet->getStyle("B{$total_row}:{$last_col}{$total_row}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("B{$total_row}")
                    ->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("C{$total_row}")
                    ->getNumberFormat()->setFormatCode($this->quantityFormat());
                $sheet->getStyle("D{$total_row}")
                    ->getNumberFormat()->setFormatCode($this->currencyFormat());

                $sheet->freezePane('A' . ($header_row + 1));
                if ($data_count > 0) {
                    $sheet->setAutoFilter("A{$header_row}:{$last_col}{$last_data}");
                }
                $sheet->setShowGridlines(false);
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToPage(true);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($header_row, $header_row);
                $sheet->getHeaderFooter()->setOddFooter('&LPeriod Performance&RPage &P of &N');
            },
        ];
    }

    private function currencyFormat(): string
    {
        $symbol = str_replace('"', '', (string) session('currency.symbol', ''));
        $precision = (int) session('business.currency_precision', 2);
        $decimals = $precision > 0 ? '.' . str_repeat('0', $precision) : '';
        $num = '#,##0' . $decimals;

        if ($symbol === '') {
            return $num;
        }

        $escaped = str_replace(['\\', '"'], ['\\\\', '""'], $symbol);
        $placement = session('business.currency_symbol_placement', 'before');

        return $placement === 'after'
            ? $num . '" ' . $escaped . '"'
            : '"' . $escaped . ' "' . $num;
    }

    private function quantityFormat(): string
    {
        $precision = (int) session('business.quantity_precision', 2);

        return $precision > 0 ? '#,##0.' . str_repeat('0', $precision) : '#,##0';
    }
}
