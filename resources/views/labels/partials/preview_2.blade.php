@php
    $is_continuous = !empty($barcode_details->is_continuous);
    // Fill most of an 80mm thermal roll (printable area ~72mm) so labels are larger.
    $roll_width_mm = $is_continuous ? max($page_width_mm, 72) : $page_width_mm;
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('barcode.print_labels') }} - {{ $business_name }}</title>
    <style type="text/css">
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        @if($is_continuous)
            /*
             * Same pattern as thermal receipts: printer roll width, height follows content.
             * Never set a short second @page length (e.g. 50mm x 25mm) — that is landscape.
             */
            @page {
                size: auto;
                margin: 0mm;
                padding: 0;
            }

            html, body {
                width: 100%;
                max-width: none;
                height: auto;
                min-height: 0;
                margin: 0;
                padding: 0;
                background: #ffffff;
                color: #000000;
                font-family: 'Courier New', Courier, monospace;
                text-align: center;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .labels-root {
                width: {{ $roll_width_mm }}mm;
                max-width: {{ $roll_width_mm }}mm;
                height: auto;
                margin: 0 auto;
                padding: 0;
                display: inline-block;
                text-align: center;
            }

            .label-sticker {
                width: {{ $roll_width_mm }}mm;
                max-width: {{ $roll_width_mm }}mm;
                height: auto;
                min-height: 0;
                max-height: none;
                margin: 0 auto;
                padding: 2.5mm 2mm 3mm 2mm;
                overflow: visible;
                text-align: center;
                line-height: 1.2;
                page-break-inside: avoid;
                break-inside: avoid;
                page-break-after: avoid;
                break-after: avoid;
            }

            .label-sticker + .label-sticker {
                border-top: 1px dashed #000000;
            }

            .label-business-name,
            .label-product-name,
            .label-variation,
            .label-custom-field,
            .label-price,
            .label-date,
            .label-sku {
                width: 100%;
                margin: 0 0 1mm 0;
                line-height: 1.25;
                word-break: break-word;
            }

            .label-business-name { font-weight: 700; font-size: 18px !important; }
            .label-product-name { font-weight: 700; font-size: 16px !important; }
            .label-variation,
            .label-custom-field,
            .label-date { font-size: 13px !important; }
            .label-price { font-weight: 700; font-size: 18px !important; }
            .label-sku {
                font-family: monospace;
                letter-spacing: 0.4px;
                font-size: 14px !important;
            }

            .label-barcode-img {
                display: block;
                width: 100%;
                max-width: 100%;
                height: auto;
                max-height: 22mm;
                margin: 1.5mm auto 1mm auto;
            }

            @media print {
                @page {
                    size: auto;
                    margin: 0mm !important;
                    padding: 0 !important;
                }

                html, body {
                    width: 100% !important;
                    max-width: none !important;
                    height: auto !important;
                    min-height: 0 !important;
                    max-height: none !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    text-align: center !important;
                    overflow: visible !important;
                    page-break-after: avoid !important;
                    break-after: avoid !important;
                }

                .labels-root {
                    width: {{ $roll_width_mm }}mm !important;
                    max-width: {{ $roll_width_mm }}mm !important;
                    height: auto !important;
                    min-height: 0 !important;
                    margin: 0 auto !important;
                    padding: 0 !important;
                    display: inline-block !important;
                    vertical-align: top !important;
                    text-align: center !important;
                    overflow: visible !important;
                    page-break-after: avoid !important;
                    break-after: avoid !important;
                }

                .label-sticker {
                    width: {{ $roll_width_mm }}mm !important;
                    margin-left: auto !important;
                    margin-right: auto !important;
                    height: auto !important;
                    min-height: 0 !important;
                    max-height: none !important;
                    text-align: center !important;
                    page-break-after: avoid !important;
                    break-after: avoid !important;
                    box-shadow: none !important;
                    border-radius: 0 !important;
                }

                .no-print,
                .print-bar {
                    display: none !important;
                    height: 0 !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
            }

            @media screen {
                html, body {
                    width: 100% !important;
                    max-width: none !important;
                    background: #f1f5f9;
                }

                body {
                    padding: 20px;
                }

                .labels-root {
                    margin: 0 auto;
                    background: #ffffff;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }

                .label-sticker {
                    border-left: 1px dashed #94a3b8;
                    border-right: 1px dashed #94a3b8;
                }

                .print-bar {
                    position: sticky;
                    top: 10px;
                    z-index: 9999;
                    background: #0f172a;
                    color: #ffffff;
                    padding: 10px 20px;
                    border-radius: 8px;
                    display: flex;
                    gap: 12px;
                    align-items: center;
                    flex-wrap: wrap;
                    width: auto;
                    max-width: 920px;
                    margin: 0 auto 16px auto;
                }

                .print-bar span { font-size: 13px; color: #94a3b8; }
                .print-bar strong { color: #f8fafc; }
                .btn-print, .btn-close-window {
                    border: none;
                    padding: 8px 16px;
                    border-radius: 6px;
                    font-weight: 600;
                    font-size: 14px;
                    cursor: pointer;
                    color: #ffffff;
                }
                .btn-print { background: #2563eb; }
                .btn-close-window { background: #475569; font-weight: 500; }
            }
        @else
            @page {
                size: {{ $page_width_mm }}mm {{ $page_height_mm }}mm;
                margin: 0;
                padding: 0;
            }

            html {
                width: {{ $page_width_mm }}mm;
                max-width: {{ $page_width_mm }}mm;
                margin: 0;
                padding: 0;
                background: #ffffff;
            }

            body {
                width: {{ $page_width_mm }}mm;
                max-width: {{ $page_width_mm }}mm;
                margin: 0;
                padding: 0;
                background: #ffffff;
                color: #000000;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .labels-root {
                width: {{ $page_width_mm }}mm;
                max-width: {{ $page_width_mm }}mm;
                margin: 0;
                padding: 0;
            }

            .label-page {
                width: {{ $page_width_mm }}mm;
                height: {{ $page_height_mm }}mm;
                max-width: {{ $page_width_mm }}mm;
                max-height: {{ $page_height_mm }}mm;
                margin: 0;
                padding: {{ $margin_top_mm }}mm {{ $margin_left_mm }}mm;
                overflow: hidden;
                page-break-after: always;
                break-after: page;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .label-page:last-of-type {
                page-break-after: auto;
                break-after: auto;
            }

            .label-table {
                width: 100%;
                height: 100%;
                border-collapse: collapse;
                border-spacing: 0;
                margin: 0;
                padding: 0;
                table-layout: fixed;
            }

            .label-cell {
                width: {{ $label_width_mm }}mm;
                height: {{ $label_height_mm }}mm;
                padding: 0;
                margin: 0;
                vertical-align: middle;
                text-align: center;
                border: none;
                overflow: hidden;
            }

            .label-sticker {
                width: {{ $label_width_mm }}mm;
                height: {{ $label_height_mm }}mm;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
                padding: 0.4mm 0.8mm;
                line-height: 1.12;
                word-break: break-word;
            }

            .label-business-name,
            .label-product-name,
            .label-variation,
            .label-custom-field,
            .label-price,
            .label-date,
            .label-sku {
                width: 100%;
                line-height: 1.12;
            }

            .label-business-name { font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .label-product-name {
                font-weight: 600;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .label-variation, .label-custom-field, .label-price, .label-date, .label-sku {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .label-price { font-weight: 700; }
            .label-sku { font-family: monospace; }

            .label-barcode-img {
                max-width: 94%;
                height: auto;
                max-height: {{ max(4.5, min(9, $label_height_mm * 0.28)) }}mm;
                display: block;
                margin: 0.3mm auto 0 auto;
            }

            @media print {
                @page {
                    size: {{ $page_width_mm }}mm {{ $page_height_mm }}mm;
                    margin: 0 !important;
                }

                html, body, .labels-root {
                    width: {{ $page_width_mm }}mm !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }

                .label-page {
                    margin: 0 !important;
                    border: none !important;
                    box-shadow: none !important;
                }

                .no-print, .print-bar { display: none !important; }
            }

            @media screen {
                html, body {
                    width: 100% !important;
                    max-width: none !important;
                    background: #f1f5f9;
                }
                body { padding: 20px; }
                .labels-root {
                    width: auto;
                    max-width: none;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 16px;
                }
                .label-page {
                    background: #ffffff;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                    border: 1px dashed #94a3b8;
                }
                .label-sticker { border: 1px dotted #cbd5e1; }
                .print-bar {
                    position: sticky;
                    top: 10px;
                    z-index: 9999;
                    background: #0f172a;
                    color: #ffffff;
                    padding: 10px 20px;
                    border-radius: 8px;
                    display: flex;
                    gap: 12px;
                    align-items: center;
                    flex-wrap: wrap;
                    width: auto;
                    max-width: 920px;
                    margin: 0 auto 16px auto;
                }
                .print-bar span { font-size: 13px; color: #94a3b8; }
                .print-bar strong { color: #f8fafc; }
                .btn-print, .btn-close-window {
                    border: none;
                    padding: 8px 16px;
                    border-radius: 6px;
                    font-weight: 600;
                    font-size: 14px;
                    cursor: pointer;
                    color: #ffffff;
                }
                .btn-print { background: #2563eb; }
                .btn-close-window { background: #475569; font-weight: 500; }
            }
        @endif
    </style>
</head>
<body>
    <div class="print-bar no-print">
        <span>
            <strong>{{ $barcode_details->name }}</strong>
            @if($is_continuous)
                &nbsp;{{ number_format($roll_width_mm, 1) }}mm roll, portrait, height follows content
            @else
                &nbsp;{{ number_format($page_width_mm, 1) }}mm &times; {{ number_format($page_height_mm, 1) }}mm
            @endif
        </span>
        <button type="button" class="btn-print" onclick="window.print()">Print</button>
        <button type="button" class="btn-close-window" onclick="window.close()">Close</button>
    </div>

    <div class="labels-root">
        @if($is_continuous)
            @foreach($product_details_page_wise as $page_products)
                @foreach($page_products as $page_product)
                    <div class="label-sticker">
                        @include('labels.partials.sticker_content', ['page_product' => $page_product])
                    </div>
                @endforeach
            @endforeach
        @else
            @foreach($product_details_page_wise as $page => $page_products)
                <div class="label-page">
                    <table class="label-table">
                        @foreach($page_products as $page_product)
                            @if($loop->index % $barcode_details->stickers_in_one_row == 0)
                                <tr>
                            @endif
                                <td class="label-cell">
                                    <div class="label-sticker">
                                        @include('labels.partials.sticker_content', ['page_product' => $page_product])
                                    </div>
                                </td>
                            @if($loop->iteration % $barcode_details->stickers_in_one_row == 0 || $loop->last)
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
            @endforeach
        @endif
    </div>

    <script type="text/javascript">
        function waitForLabelImages() {
            var images = Array.prototype.slice.call(document.images || []);
            if (!images.length) {
                return Promise.resolve();
            }
            return Promise.all(images.map(function(img) {
                if (img.complete) {
                    return Promise.resolve();
                }
                return new Promise(function(resolve) {
                    img.onload = resolve;
                    img.onerror = resolve;
                });
            }));
        }

        window.addEventListener('DOMContentLoaded', function() {
            waitForLabelImages().then(function() {
                setTimeout(function() {
                    window.print();
                }, 250);
            });
        });
    </script>
</body>
</html>
