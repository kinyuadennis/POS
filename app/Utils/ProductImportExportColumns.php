<?php

namespace App\Utils;

/**
 * Shared column layout for product import, export, and template download.
 *
 * Import accepts CSV, XLS, and XLSX via Maatwebsite Excel (positional columns).
 */
class ProductImportExportColumns
{
    public const COL_NAME = 0;

    public const COL_BRAND = 1;

    public const COL_UNIT = 2;

    public const COL_CATEGORY = 3;

    public const COL_SKU = 4;

    public const COL_RETAIL_PRICE = 5;

    public const COL_SELLING_PRICE = 5;

    public const COL_WHOLESALE_PRICE = 6;

    public const COL_OPENING_STOCK = 7;

    public const COL_CURRENT_STOCK = 8;

    public const COL_OPENING_STOCK_LOCATION = 9;

    public const COL_PRODUCT_LOCATIONS = 10;

    public const COL_MANAGE_STOCK = 11;

    public const COL_ALERT_QUANTITY = 12;

    public const COL_PURCHASE_PRICE = 13;

    public const COLUMN_COUNT = 14;

    public static function headers(): array
    {
        return [
            'NAME',
            'BRAND',
            'UNIT',
            'CATEGORY',
            'SKU',
            'RETAIL PRICE',
            'WHOLESALE PRICE',
            'OPENING STOCK',
            'CURRENT STOCK',
            'OPENING STOCK LOCATION',
            'PRODUCT LOCATIONS',
            'MANAGE STOCK (1=yes 0=No)',
            'ALERT QUANTITY',
            'PURCHASE PRICE',
        ];
    }
}
