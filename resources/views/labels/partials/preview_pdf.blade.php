<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #000000;
        }
        @page {
            margin: 0;
            size: {{ $page_width_mm }}mm {{ $page_height_mm }}mm;
        }
        .label-page {
            width: {{ $page_width_mm }}mm;
            height: {{ $page_height_mm }}mm;
            margin: 0;
            padding: {{ $margin_top_mm }}mm {{ $margin_left_mm }}mm;
            overflow: hidden;
            page-break-after: always;
            page-break-inside: avoid;
        }
        .label-page:last-child {
            page-break-after: auto;
        }
        .label-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
        }
        .label-cell {
            width: {{ $label_width_mm }}mm;
            height: {{ $label_height_mm }}mm;
            text-align: center;
            vertical-align: middle;
            padding: 0;
            margin: 0;
            overflow: hidden;
        }
        .label-sticker {
            width: {{ $label_width_mm }}mm;
            height: {{ $label_height_mm }}mm;
            overflow: hidden;
            text-align: center;
            padding: 0.4mm;
        }
        .label-business-name,
        .label-product-name,
        .label-variation,
        .label-custom-field,
        .label-price,
        .label-date,
        .label-sku {
            width: 100%;
            margin: 0;
            padding: 0;
            line-height: 1.15;
        }
        .label-business-name { font-weight: bold; }
        .label-product-name { font-weight: bold; }
        .label-price { font-weight: bold; }
        .label-sku { font-family: DejaVu Sans Mono, monospace; }
        .label-barcode-img {
            max-width: 94%;
            height: auto;
            margin: 0.4mm auto 0 auto;
        }
    </style>
</head>
<body>
@foreach($product_details_page_wise as $page_products)
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
</body>
</html>
