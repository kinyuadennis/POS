@extends('layouts.app')
@section('title', __('sale.products'))

@section('content')

@php
    $theme_color = session('business.theme_color', 'blue');
    $color_map = [
        'blue' => '#3c8dbc',
        'purple' => '#605ca8',
        'green' => '#00a65a',
        'red' => '#dd4b39',
        'yellow' => '#f39c12',
        'orange' => '#ff851b',
        'sky' => '#00c0ef',
        'primary' => '#3c8dbc'
    ];
    $primary_hex = isset($color_map[$theme_color]) ? $color_map[$theme_color] : '#3c8dbc';
@endphp

<style>
    /* Prevent font and text resizing flash */
    .content, .content label, .content span, .content input, .content select {
        font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif !important;
    }

    /* Skeleton Loader CSS */
    .skeleton-row td {
        padding: 14px 16px !important;
        vertical-align: middle !important;
    }
    .skeleton-block {
        height: 14px;
        background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s infinite;
        border-radius: 6px;
        display: inline-block;
    }
    @keyframes skeleton-loading {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }

    /* Box primary border color */
    .box.box-primary {
        border-top-color: {{ $primary_hex }} !important;
    }

    /* Tab header styles */
    .nav-tabs-custom > .nav-tabs > li {
        border-top: none !important;
        margin-bottom: -2px !important;
    }
    .nav-tabs-custom > .nav-tabs > li > a {
        border-radius: 8px 8px 0 0 !important;
        padding: 10px 18px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #64748b !important;
        border: 1px solid transparent !important;
        transition: all 0.15s ease-in-out !important;
    }
    .nav-tabs-custom > .nav-tabs > li > a:hover {
        color: {{ $primary_hex }} !important;
        background-color: rgba(255, 255, 255, 0.4) !important;
    }
    .nav-tabs-custom > .nav-tabs > li.active {
        border-top: none !important;
    }
    .nav-tabs-custom > .nav-tabs > li.active > a,
    .nav-tabs-custom > .nav-tabs > li.active > a:hover,
    .nav-tabs-custom > .nav-tabs > li.active > a:focus {
        background-color: #f3f4f6 !important;
        color: {{ $primary_hex }} !important;
        font-weight: 700 !important;
        border: 1px solid #cbd5e1 !important;
        border-top: 3px solid {{ $primary_hex }} !important;
        border-bottom: none !important;
        border-bottom-color: transparent !important;
    }

    /* Button custom theme styling */
    .product-theme-btn {
        background: {{ $primary_hex }} !important;
        border-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        transition: all 0.2s ease-in-out !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }
    .product-theme-btn:hover {
        background: {{ $primary_hex }} !important;
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
        color: #ffffff !important;
    }

    /* Hide default Datatables filter, length, buttons, and processing overlays */
    #product_table_wrapper .dataTables_filter,
    #product_table_wrapper .dataTables_length,
    #product_table_wrapper .dt-buttons,
    #stock_report_table_wrapper .dataTables_filter,
    #stock_report_table_wrapper .dataTables_length,
    #stock_report_table_wrapper .dt-buttons {
        display: none !important;
    }
    .dataTables_processing,
    #product_table_processing,
    #stock_report_table_processing {
        display: none !important;
    }

    /* Table text wrapping and cell alignment */
    #product_table th,
    #product_table th *,
    #product_table td,
    #stock_report_table th,
    #stock_report_table th *,
    #stock_report_table td {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    /* Make table not look boxed or in a container */
    .nav-tabs-custom {
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
    }
    .nav-tabs-custom > .tab-content {
        background: transparent !important;
        padding: 0 !important;
        border: none !important;
    }
    .nav-tabs-custom > .nav-tabs {
        border-bottom: 2px solid #cbd5e1 !important;
        background: transparent !important;
        margin-bottom: 20px !important;
        display: flex !important;
        gap: 8px !important;
    }

    /* Table wrappers — border, radius, shadow, and horizontal scroll */
    #product_table_wrapper,
    #stock_report_table_wrapper {
        border: 1px solid #e2e8f0 !important;
        border-radius: 14px !important;
        overflow-x: auto !important;
        overflow-y: hidden !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 16px rgba(0,0,0,0.04) !important;
        background: #ffffff !important;
        width: 100% !important;
    }
    /* Horizontal top scrollbar container */
    .product-table-top-scroll-wrapper {
        overflow-x: auto !important;
        overflow-y: hidden !important;
        width: 100% !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-bottom: 1px solid #f1f5f9 !important;
        border-top-left-radius: 14px !important;
        border-top-right-radius: 14px !important;
        margin-bottom: 0 !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04) !important;
        display: none;
    }
    .product-table-top-scroll-wrapper::-webkit-scrollbar,
    #product_table_wrapper::-webkit-scrollbar,
    #stock_report_table_wrapper::-webkit-scrollbar {
        height: 6px !important;
    }
    .product-table-top-scroll-wrapper::-webkit-scrollbar-track,
    #product_table_wrapper::-webkit-scrollbar-track,
    #stock_report_table_wrapper::-webkit-scrollbar-track {
        background: #f1f5f9 !important;
        border-top-left-radius: 14px !important;
        border-top-right-radius: 14px !important;
    }
    .product-table-top-scroll-wrapper::-webkit-scrollbar-thumb,
    #product_table_wrapper::-webkit-scrollbar-thumb,
    #stock_report_table_wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1 !important;
        border-radius: 4px !important;
    }
    .product-table-top-scroll-wrapper::-webkit-scrollbar-thumb:hover {
        background: #94a3b8 !important;
    }
    .product-table-top-scroll-inner {
        height: 1px !important;
    }
    #product_table_wrapper.has-top-scroll,
    #stock_report_table_wrapper.has-top-scroll {
        border-top: none !important;
        border-top-left-radius: 0 !important;
        border-top-right-radius: 0 !important;
    }

    /* Reset table element — wrapper owns the border & radius */
    #product_table, #stock_report_table {
        background-color: #ffffff !important;
        border: none !important;
        border-radius: 0 !important;
        border-collapse: collapse !important;
        border-spacing: 0 !important;
        overflow: visible !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }
    #product_table th, #stock_report_table th {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%) !important;
        color: #64748b !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 11px !important;
        letter-spacing: 0.06em !important;
        border-bottom: 2px solid #e2e8f0 !important;
        border-right: 1px solid #e2e8f0 !important;
        border-top: none !important;
        padding: 14px 16px !important;
        user-select: none !important;
    }
    #product_table th:last-child, #stock_report_table th:last-child {
        border-right: none !important;
    }
    /* Sort indicator refinement & prevent text collision */
    #product_table th.sorting,
    #product_table th.sorting_asc,
    #product_table th.sorting_desc,
    #stock_report_table th.sorting,
    #stock_report_table th.sorting_asc,
    #stock_report_table th.sorting_desc {
        padding-right: 32px !important;
        position: relative !important;
    }
    #product_table th.sorting:after,
    #product_table th.sorting_asc:after,
    #product_table th.sorting_desc:after,
    #stock_report_table th.sorting:after,
    #stock_report_table th.sorting_asc:after,
    #stock_report_table th.sorting_desc:after {
        opacity: 0.5 !important;
        right: 12px !important;
    }
    #product_table th.sorting:before,
    #product_table th.sorting_asc:before,
    #product_table th.sorting_desc:before,
    #stock_report_table th.sorting:before,
    #stock_report_table th.sorting_asc:before,
    #stock_report_table th.sorting_desc:before {
        opacity: 0.5 !important;
        right: 18px !important;
    }
    #product_table td, #stock_report_table td {
        border-bottom: 1px solid #e8edf3 !important;
        border-right: 1px solid #f1f5f9 !important;
        border-top: none !important;
        color: #334155 !important;
        font-size: 13px !important;
        padding: 11px 16px !important;
        vertical-align: middle !important;
    }
    #product_table td:last-child, #stock_report_table td:last-child {
        border-right: none !important;
    }
    /* Fix DataTables scrollX container — remove extra border/shadow that causes gap */
    #product_table_wrapper .dataTables_scroll,
    #stock_report_table_wrapper .dataTables_scroll {
        border: none !important;
        box-shadow: none !important;
    }
    /* Alternating row tint */
    #product_table tbody tr:nth-child(even),
    #stock_report_table tbody tr:nth-child(even) {
        background-color: #fafbfd !important;
    }
    /* Hover row highlight */
    #product_table tbody tr:hover,
    #stock_report_table tbody tr:hover {
        background-color: #f0f7ff !important;
        transition: background-color 0.15s ease !important;
        cursor: pointer !important;
    }
    /* Lighter selected row highlight when checkbox is selected */
    #product_table tbody tr.selected,
    #product_table tbody tr.selected td,
    #stock_report_table tbody tr.selected,
    #stock_report_table tbody tr.selected td {
        background-color: #e0f2fe !important;
        color: #1e293b !important;
    }
    /* Bulk actions dropdown toggle state */
    #bulk_actions_dropdown_group .dropdown-menu,
    #colvis_dropdown_group .dropdown-menu {
        display: none;
    }
    #bulk_actions_dropdown_group.open .dropdown-menu,
    #colvis_dropdown_group.open .dropdown-menu {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Column visibility controls styling */
    .colvis-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 6px;
        cursor: pointer;
        user-select: none;
        transition: background-color 0.15s ease;
        font-size: 13px;
        color: #334155;
        font-weight: 500;
        margin: 0 !important;
    }
    .colvis-item:hover {
        background-color: #f1f5f9;
    }
    .colvis-item input[type="checkbox"] {
        cursor: pointer;
        width: 16px;
        height: 16px;
        accent-color: {{ $primary_hex }};
        margin: 0 !important;
    }

    /* Clean, modern Search Section & Actions Toolbar Layout */
    .product-search-section {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 16px 20px !important;
        margin-bottom: 18px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }

    .product-search-top-row {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        margin-bottom: 12px !important;
        width: 100% !important;
    }

    .product-search-input-wrap {
        flex: 1 1 auto !important;
        min-width: 0 !important;
    }

    .product-search-buttons {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        flex-shrink: 0 !important;
    }

    .product-toolbar-row {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 10px !important;
        width: 100% !important;
        flex-wrap: wrap !important;
        padding-top: 4px !important;
    }

    .product-toolbar-left {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        flex-wrap: wrap !important;
    }

    .product-toolbar-right {
        display: inline-flex !important;
        align-items: center !important;
    }

    /* Responsive adjustments for product management and toolbar */
    @media (max-width: 768px) {
        .manage-products-content {
            padding: 12px 10px !important;
        }
        .product-search-section {
            padding: 12px 14px !important;
            margin-bottom: 14px !important;
        }
        .product-search-top-row {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 10px !important;
            margin-bottom: 12px !important;
        }
        .product-search-input-wrap {
            width: 100% !important;
        }
        .product-search-buttons {
            width: 100% !important;
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
        }
        .product-search-buttons button,
        .product-search-buttons a {
            width: 100% !important;
            justify-content: center !important;
            height: 38px !important;
            padding: 0 12px !important;
            font-size: 13px !important;
        }
        .product-toolbar-row {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 10px !important;
        }
        .product-toolbar-left {
            width: 100% !important;
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
            align-items: center !important;
        }
        .product-toolbar-right {
            width: 100% !important;
        }
        .product-toolbar-right .btn-group,
        .product-toolbar-right #bulk_actions_toggle {
            width: 100% !important;
            justify-content: center !important;
        }
        #colvis_toggle,
        #bulk_actions_toggle {
            padding: 6px 12px !important;
            font-size: 12px !important;
            height: 35px !important;
        }
        .colvis-dropdown-menu {
            position: absolute !important;
            left: 0 !important;
            right: auto !important;
            max-width: calc(100vw - 32px) !important;
            width: 280px !important;
        }
    }

    @media (max-width: 480px) {
        .product-toolbar-left > div,
        .product-toolbar-left .btn-group {
            width: 100% !important;
        }
        .product-toolbar-left .btn,
        .product-toolbar-left label {
            width: 100% !important;
            justify-content: center !important;
        }
        .colvis-dropdown-menu {
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
    }

    #product_table tbody tr:last-child td,
    #stock_report_table tbody tr:last-child td {
        border-bottom: none !important;
    }
    /* SKU column monospace styling */
    #product_table td:last-of-type,
    #product_table td[data-col="sku"] {
        font-family: 'SFMono-Regular', 'Consolas', 'Liberation Mono', monospace !important;
        color: #7c3aed !important;
        font-size: 12px !important;
    }
    /* Action dropdown buttons polish */
    #product_table .btn-group .dropdown-toggle,
    #stock_report_table .btn-group .dropdown-toggle {
        border-radius: 8px !important;
        font-size: 12px !important;
        padding: 5px 12px !important;
        font-weight: 600 !important;
        letter-spacing: 0.2px !important;
    }
    #product_table .dropdown-menu,
    #stock_report_table .dropdown-menu {
        border-radius: 10px !important;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12) !important;
        border: 1px solid #e2e8f0 !important;
        padding: 6px !important;
        min-width: 180px !important;
    }
    #product_table .dropdown-menu li a,
    #stock_report_table .dropdown-menu li a {
        border-radius: 6px !important;
        padding: 8px 14px !important;
        font-size: 13px !important;
        color: #334155 !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        transition: background 0.12s ease !important;
    }
    #product_table .dropdown-menu li a:hover,
    #stock_report_table .dropdown-menu li a:hover {
        background: #f0f7ff !important;
        color: #1d4ed8 !important;
        text-decoration: none !important;
    }
    /* Product action buttons horizontal flex styling */
    .contact-actions-wrapper {
        display: inline-flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        gap: 5px !important;
        align-items: center !important;
    }
    .contact-actions-wrapper a.tw-dw-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 30px !important;
        height: 30px !important;
        padding: 0 !important;
        border-radius: 6px !important;
        transition: all 0.15s ease-in-out !important;
        font-size: 13px !important;
        border: 1px solid #cbd5e1 !important;
        background-color: #ffffff !important;
        text-decoration: none !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-primary {
        color: {{ $primary_hex }} !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-primary:hover {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        border-color: {{ $primary_hex }} !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-info {
        color: #0ea5e9 !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-info:hover {
        background-color: #0ea5e9 !important;
        color: #ffffff !important;
        border-color: #0ea5e9 !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-warning {
        color: #f59e0b !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-warning:hover {
        background-color: #f59e0b !important;
        color: #ffffff !important;
        border-color: #f59e0b !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-error {
        color: #ef4444 !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-error:hover {
        background-color: #ef4444 !important;
        color: #ffffff !important;
        border-color: #ef4444 !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-accent {
        color: #8b5cf6 !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-accent:hover {
        background-color: #8b5cf6 !important;
        color: #ffffff !important;
        border-color: #8b5cf6 !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-success {
        color: #10b981 !important;
    }
    .contact-actions-wrapper a.tw-dw-btn-success:hover {
        background-color: #10b981 !important;
        color: #ffffff !important;
        border-color: #10b981 !important;
    }
    /* DataTables info + pagination bar */
    #product_table_wrapper .dataTables_info,
    #stock_report_table_wrapper .dataTables_info {
        font-size: 12px !important;
        color: #94a3b8 !important;
        padding: 10px 4px !important;
    }
    #product_table_wrapper .dataTables_paginate,
    #stock_report_table_wrapper .dataTables_paginate {
        padding: 8px 4px !important;
    }
    #product_table_wrapper .dataTables_paginate .paginate_button,
    #stock_report_table_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        padding: 5px 12px !important;
        border: none !important;
        transition: background 0.15s ease !important;
    }
    #product_table_wrapper .dataTables_paginate .paginate_button.current,
    #stock_report_table_wrapper .dataTables_paginate .paginate_button.current {
        background: {{ $primary_hex }} !important;
        color: #fff !important;
        border: none !important;
    }
    #product_table_wrapper .dataTables_paginate .paginate_button:hover:not(.current),
    #stock_report_table_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
        background: #f0f7ff !important;
        color: #1d4ed8 !important;
        border: none !important;
    }


    /* Search section container */
    .product-search-section {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 20px !important;
        margin-bottom: 20px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }

    /* Custom theme styling for iCheck checkboxes */
    .icheckbox_square-blue {
        border-radius: 4px !important;
        border: 1px solid #cbd5e1 !important;
        transition: all 0.2s ease !important;
        background-image: none !important;
        background-color: #ffffff !important;
        margin: 0 !important;
    }
    .icheckbox_square-blue.hover,
    .icheckbox_square-blue:hover {
        border-color: {{ $primary_hex }} !important;
    }
    .icheckbox_square-blue.checked {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%23ffffff'%3E%3Cpath fill-rule='evenodd' d='M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z' clip-rule='evenodd'/%3E%3C/svg%3E") !important;
        background-color: {{ $primary_hex }} !important;
        background-position: center !important;
        background-size: 80% !important;
        border: 1px solid {{ $primary_hex }} !important;
    }

    /* Custom theme styling for iCheck radio buttons */
    .iradio_square-blue {
        border-radius: 50% !important;
        border: 1px solid #cbd5e1 !important;
        transition: all 0.2s ease !important;
        background-image: none !important;
        background-color: #ffffff !important;
        margin: 0 !important;
        width: 22px !important;
        height: 22px !important;
    }
    .iradio_square-blue.hover,
    .iradio_square-blue:hover {
        border-color: {{ $primary_hex }} !important;
    }
    .iradio_square-blue.checked {
        border-color: {{ $primary_hex }} !important;
        background-color: #ffffff !important;
    }
    .iradio_square-blue.checked::after {
        content: '' !important;
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        width: 10px !important;
        height: 10px !important;
        background-color: {{ $primary_hex }} !important;
        border-radius: 50% !important;
    }

    /* Style checkbox wrapper and label using flex layout to prevent text jumps and misalignments */
    .checkbox, .radio {
        display: inline-flex !important;
        align-items: center !important;
        margin: 8px 0 !important;
        padding: 0 !important;
        gap: 6px !important;
        min-height: auto !important;
    }
    .checkbox label, .radio label {
        padding-left: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        margin: 0 !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        color: #333333 !important;
    }

    /* Style native checkbox/radio elements during load to prevent size and positioning jumps */
    input[type="checkbox"].input-icheck,
    input[type="radio"].input-icheck {
        appearance: none !important;
        -webkit-appearance: none !important;
        width: 22px !important;
        height: 22px !important;
        border: 1px solid #cbd5e1 !important;
        outline: none !important;
        background-color: #ffffff !important;
        cursor: pointer !important;
        display: inline-block !important;
        vertical-align: middle !important;
        margin: 0 !important;
        position: static !important;
        margin-left: 0 !important;
        margin-top: 0 !important;
    }
    input[type="checkbox"].input-icheck {
        border-radius: 4px !important;
    }
    input[type="radio"].input-icheck {
        border-radius: 50% !important;
    }

    /* Style checked native elements during load to prevent flash of unchecked states */
    input[type="checkbox"].input-icheck:checked {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%23ffffff'%3E%3Cpath fill-rule='evenodd' d='M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z' clip-rule='evenodd'/%3E%3C/svg%3E") !important;
        background-color: {{ $primary_hex }} !important;
    input[type="radio"].input-icheck:checked {
        border-color: {{ $primary_hex }} !important;
        background-image: radial-gradient(circle, {{ $primary_hex }} 40%, transparent 50%) !important;
    }

    /* Content Header styling */
    .content-wrapper > .content-header,
    section.content-header,
    .content-header {
        background-color: #ffffff !important;
        background: #ffffff !important;
        padding: 20px 24px !important;
        border-bottom: 1px solid #cbd5e1 !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
    }

    .manage-products-content {
        background-color: #f3f4f6 !important;
        padding: 24px !important;
        min-height: calc(100vh - 100px);
    }
</style>

    <!-- Content Header (Page header) -->
    <section class="content-header tw-bg-white tw-flex tw-justify-between tw-items-center" style="background-color: #ffffff !important; background: #ffffff !important; padding: 20px 24px !important; border-bottom: 1px solid #cbd5e1 !important; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;">
        <div>
            <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang('sale.products')</h1>
            <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">@lang('lang_v1.manage_products')</p>
        </div>
    </section>

    <!-- Main content -->
    <section class="content manage-products-content">

        @can('product.view')
            <div class="row">
                <div class="col-md-12">
                    <!-- Custom Tabs -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#product_list_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-cubes"
                                        aria-hidden="true"></i> @lang('lang_v1.all_products')</a>
                            </li>
                            @can('stock_report.view')
                                <li>
                                    <a href="#product_stock_report" class="product_stock_report" data-toggle="tab"
                                        aria-expanded="true"><i class="fa fa-hourglass-half" aria-hidden="true"></i>
                                     @lang('report.stock_report')</a>
                                </li>
                            @endcan
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="product_list_tab">
                                <!-- Custom Search & Action Section for Product List -->
                                <div class="product-search-section">
                                    <!-- Top Row: Search Input + Filter & Add Buttons -->
                                    <div class="product-search-top-row">
                                        <!-- Search Input -->
                                        <div class="product-search-input-wrap">
                                            <div class="tw-relative" style="position: relative; width: 100%;">
                                                <span style="position: absolute; left: 14px; top: 12px; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                    </svg>
                                                </span>
                                                <input type="text" id="custom_product_search" 
                                                       placeholder="Search by name, SKU, brand, category, type..." 
                                                       class="form-control"
                                                       style="padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px; box-sizing: border-box;">
                                            </div>
                                        </div>

                                        <!-- Filter and Add Buttons on the Right -->
                                        <div class="product-search-buttons">
                                            <!-- Filter Button -->
                                            <button type="button" class="tw-dw-btn product-theme-btn tw-text-white tw-border-none"
                                                data-toggle="collapse" data-target="#collapseFilterSection" aria-expanded="false" aria-controls="collapseFilterSection"
                                                style="height: 42px; display: inline-flex; align-items: center; gap: 6px; padding: 0 16px; border-radius: 8px; font-weight: 700; cursor: pointer;">
                                                <i class="fa fa-filter"></i> Filters
                                            </button>

                                            <!-- Add Button -->
                                            @can('product.create')
                                                <a class="tw-dw-btn product-theme-btn tw-text-white tw-border-none"
                                                    href="{{ action([\App\Http\Controllers\ProductController::class, 'create']) }}"
                                                    style="height: 42px; display: inline-flex; align-items: center; gap: 6px; padding: 0 16px; border-radius: 8px; text-decoration: none; font-weight: 700; cursor: pointer;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                                    @lang('messages.add')
                                                </a>
                                            @endcan
                                        </div>
                                    </div>

                                    <!-- Bottom Row: Show Entries, Export, Column Visibility, and Mass Actions -->
                                    <div class="product-toolbar-row" style="display: flex; align-items: center; justify-content: space-between; gap: 12px; width: 100%; flex-wrap: wrap;">
                                        <!-- Left Side: Show entries + Export + ColVis -->
                                        <div class="product-toolbar-left" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; max-width: 100%;">
                                            <!-- Show entries length selector -->
                                            <div class="product-toolbar-item" style="display: inline-block;">
                                                <label style="display: flex; align-items: center; gap: 6px; font-weight: 500; color: #475569; font-size: 13px; margin: 0; white-space: nowrap;">
                                                    @lang('lang_v1.show') 
                                                    <select id="custom_product_length" class="form-control" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 10px; background-color: #ffffff; outline: none; height: 35px; display: inline-block; width: auto; font-size: 13px;">
                                                        <option value="25">25</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                        <option value="250">250</option>
                                                        <option value="-1">@lang('lang_v1.all')</option>
                                                    </select>
                                                    @lang('lang_v1.entries')
                                                </label>
                                            </div>

                                            <!-- Export Button -->
                                            @if ($is_admin)
                                                <div class="product-toolbar-item" style="display: inline-block;">
                                                    <a class="btn btn-default btn-sm"
                                                        id="download_product_excel"
                                                        href="{{ action([\App\Http\Controllers\ProductController::class, 'downloadExcel']) }}"
                                                        style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-weight: 600; padding: 7px 14px; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); display: inline-flex; align-items: center; gap: 6px; cursor: pointer; height: 35px; text-decoration: none; font-size: 13px; white-space: nowrap;">
                                                        <i class="fa fa-share-square"></i> @lang('lang_v1.download_excel')
                                                    </a>
                                                </div>
                                            @endif

                                            <!-- Hide Not for Sale Filter Button -->
                                            <div class="product-toolbar-item" style="display: inline-block;">
                                                <button type="button" id="btn_toggle_hide_not_for_sale" class="btn btn-default btn-sm"
                                                    style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-weight: 600; padding: 7px 14px; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); display: inline-flex; align-items: center; gap: 6px; cursor: pointer; height: 35px; font-size: 13px; white-space: nowrap; transition: all 0.2s ease-in-out;">
                                                    <i class="fa fa-eye-slash" id="icon_hide_not_for_sale" style="color: #64748b;"></i>
                                                    <span id="text_hide_not_for_sale">Hide Not for Sale</span>
                                                </button>
                                                <input type="hidden" id="hide_not_for_sale" value="0">
                                            </div>

                                            <!-- Hide/Unhide Columns Dropdown -->
                                            <div class="btn-group product-toolbar-item" id="colvis_dropdown_group" style="position: relative; display: inline-block; max-width: 100%;">
                                                <button type="button" id="colvis_toggle" class="btn btn-default btn-sm" aria-haspopup="true" aria-expanded="false"
                                                    style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-weight: 600; padding: 7px 14px; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); display: inline-flex; align-items: center; gap: 6px; cursor: pointer; height: 35px; font-size: 13px; white-space: nowrap; max-width: 100%;">
                                                    <i class="fa fa-columns" style="color: #64748b;"></i> <span>Hide/Unhide Columns</span> <span class="caret"></span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-left colvis-dropdown-menu" style="min-width: 240px; width: max-content; max-width: min(320px, calc(100vw - 32px)); max-height: min(420px, 65vh); overflow-y: auto; padding: 12px 14px; border-radius: 10px; border: 1px solid #cbd5e1; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); background: #ffffff; z-index: 1050; box-sizing: border-box;">
                                                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 8px; margin-bottom: 8px; border-bottom: 1px solid #f1f5f9;">
                                                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Table Columns</span>
                                                        <div style="display: flex; gap: 8px; align-items: center;">
                                                            <button type="button" id="colvis_show_all" style="background: none; border: none; padding: 0; font-size: 11px; color: {{ $primary_hex }}; font-weight: 700; cursor: pointer;">Show All</button>
                                                            <span style="color: #cbd5e1; font-size: 11px;">|</span>
                                                            <button type="button" id="colvis_reset" style="background: none; border: none; padding: 0; font-size: 11px; color: #64748b; font-weight: 600; cursor: pointer;">Reset</button>
                                                        </div>
                                                    </div>
                                                    <div id="colvis_items_list" style="display: flex; flex-direction: column; gap: 2px;">
                                                        <!-- Rendered column checkboxes -->
                                                    </div>
                                                    <!-- Additional Export-Only Fields -->
                                                    <div style="margin-top: 10px; padding-top: 8px; border-top: 1px solid #f1f5f9;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; padding: 0 4px;">
                                                            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Additional Export Fields</span>
                                                            <span style="font-size: 10px; color: #94a3b8; font-weight: 600;">(Excel Only)</span>
                                                        </div>
                                                        <div id="colvis_export_items_list" style="display: flex; flex-direction: column; gap: 2px;">
                                                            <!-- Rendered export-only checkboxes -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right Side: Mass Actions Dropdown -->
                                        <div class="product-toolbar-right" style="display: inline-block;">
                                            <div class="btn-group" id="bulk_actions_dropdown_group" style="position: relative; display: inline-block; max-width: 100%;">
                                                <button type="button" id="bulk_actions_toggle" class="btn btn-default btn-sm" aria-haspopup="true" aria-expanded="false" style="background-color: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-weight: 600; padding: 7px 14px; border-radius: 8px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); display: inline-flex; align-items: center; gap: 6px; cursor: pointer; height: 35px; font-size: 13px; white-space: nowrap;">
                                                    <i class="fa fa-cogs"></i> Bulk Actions <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right" style="border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); padding: 8px; min-width: 180px; z-index: 1050; max-width: min(280px, calc(100vw - 32px));">
                                                    @can('product.update')
                                                        @if(config('constants.enable_product_bulk_edit'))
                                                            <li><a href="#" id="bulk-action-edit" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-edit" style="margin-right: 8px;"></i> @lang('lang_v1.bulk_edit')</a></li>
                                                        @endif
                                                        <li><a href="#" class="bulk-action-location" data-type="add" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-plus-circle" style="margin-right: 8px;"></i> @lang('lang_v1.add_to_location')</a></li>
                                                        <li><a href="#" class="bulk-action-location" data-type="remove" style="padding: 8px 16px; color: #334155; border-radius: 4px; display: block;"><i class="fa fa-minus-circle" style="margin-right: 8px;"></i> @lang('lang_v1.remove_from_location')</a></li>
                                                        <li><a href="#" id="bulk-action-deactivate" style="padding: 8px 16px; color: #d97706; border-radius: 4px; display: block;"><i class="fa fa-ban" style="margin-right: 8px;"></i> @lang('lang_v1.deactivate_selected')</a></li>
                                                        <li><a href="#" id="bulk-action-not-for-selling" style="padding: 8px 16px; color: #c2410c; border-radius: 4px; display: block;"><i class="fa fa-eye-slash" style="margin-right: 8px;"></i> @lang('lang_v1.mark_not_for_selling_selected')</a></li>
                                                        <li><a href="#" id="bulk-action-for-selling" style="padding: 8px 16px; color: #15803d; border-radius: 4px; display: block;"><i class="fa fa-eye" style="margin-right: 8px;"></i> @lang('lang_v1.mark_for_selling_selected')</a></li>
                                                    @endcan
                                                    @can('product.delete')
                                                        <li role="separator" class="divider" style="margin: 6px 0;"></li>
                                                        <li><a href="#" id="bulk-action-delete" style="padding: 8px 16px; color: #dc2626; border-radius: 4px; display: block; font-weight: 600;"><i class="fa fa-trash" style="margin-right: 8px;"></i> @lang('lang_v1.delete_selected')</a></li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="collapse" id="collapseFilterSection" style="margin-bottom: 20px;">
                                    <div style="background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('type', __('product.product_type') . ':') !!}
                                                    {!! Form::select(
                                                        'type',
                                                        ['single' => __('lang_v1.single'), 'variable' => __('lang_v1.variable'), 'combo' => __('lang_v1.combo')],
                                                        null,
                                                        [
                                                            'class' => 'form-control select2',
                                                            'style' => 'width:100%',
                                                            'id' => 'product_list_filter_type',
                                                            'placeholder' => __('lang_v1.all'),
                                                        ],
                                                    ) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('category_id', __('product.category') . ':') !!}
                                                    {!! Form::select('category_id', $categories, null, [
                                                        'class' => 'form-control select2',
                                                        'style' => 'width:100%',
                                                        'id' => 'product_list_filter_category_id',
                                                        'placeholder' => __('lang_v1.all'),
                                                    ]) !!}
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('unit_id', __('product.unit') . ':') !!}
                                                    {!! Form::select('unit_id', $units, null, [
                                                        'class' => 'form-control select2',
                                                        'style' => 'width:100%',
                                                        'id' => 'product_list_filter_unit_id',
                                                        'placeholder' => __('lang_v1.all'),
                                                    ]) !!}
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {!! Form::label('brand_id', __('product.brand') . ':') !!}
                                                    {!! Form::select('brand_id', $brands, null, [
                                                        'class' => 'form-control select2',
                                                        'style' => 'width:100%',
                                                        'id' => 'product_list_filter_brand_id',
                                                        'placeholder' => __('lang_v1.all'),
                                                    ]) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3" id="location_filter">
                                                <div class="form-group">
                                                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                                                    {!! Form::select('location_id', $business_locations, $default_location_id ?? null, [
                                                        'class' => 'form-control select2',
                                                        'style' => 'width:100%',
                                                        'placeholder' => __('lang_v1.all'),
                                                        'data-location-filter' => '1',
                                                    ]) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <br>
                                                <div class="form-group">
                                                    {!! Form::select(
                                                        'active_state',
                                                        ['active' => __('business.is_active'), 'inactive' => __('lang_v1.inactive')],
                                                        null,
                                                        [
                                                            'class' => 'form-control select2',
                                                            'style' => 'width:100%',
                                                            'id' => 'active_state',
                                                            'placeholder' => __('lang_v1.all'),
                                                        ],
                                                    ) !!}
                                                </div>
                                            </div>

                                            <!-- include module filter -->
                                            @if (!empty($pos_module_data))
                                                @foreach ($pos_module_data as $key => $value)
                                                    @if (!empty($value['view_path']))
                                                        @includeIf($value['view_path'], ['view_data' => $value['view_data']])
                                                    @endif
                                                @endforeach
                                            @endif

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <br>
                                                    <label>
                                                        {!! Form::checkbox('hide_not_for_sale_cb', 1, false, ['class' => 'input-icheck', 'id' => 'hide_not_for_sale_cb']) !!} <strong>Hide Not for Sale</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <br>
                                                    <label>
                                                        {!! Form::checkbox('not_for_selling', 1, false, ['class' => 'input-icheck', 'id' => 'not_for_selling']) !!} <strong>@lang('lang_v1.not_for_selling')</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            @if ($is_woocommerce)
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <br>
                                                        <label>
                                                            {!! Form::checkbox('woocommerce_enabled', 1, false, ['class' => 'input-icheck', 'id' => 'woocommerce_enabled']) !!} {{ __('lang_v1.woocommerce_enabled') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @include('product.partials.product_list')
                            </div>
                            @can('stock_report.view')
                                <div class="tab-pane" id="product_stock_report">
                                    <!-- Custom Search & Action Section for Stock Report -->
                                    <div class="product-search-section">
                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; flex-wrap: wrap; width: 100%;">
                                            <!-- Search Input on the Left -->
                                            <div style="flex-grow: 1; min-width: 250px;">
                                                <div style="position: relative; width: 100%;">
                                                    <span style="position: absolute; left: 14px; top: 12px; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                        </svg>
                                                    </span>
                                                    <input type="text" id="custom_stock_search" 
                                                           placeholder="Search by name, SKU, category, location..." 
                                                           class="form-control"
                                                           style="padding-left: 42px; border-radius: 8px; height: 42px; width: 100%; border: 1px solid #cbd5e1; outline: none; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); font-size: 14px; box-sizing: border-box;">
                                                </div>
                                            </div>

                                            <!-- Length Selector on the Right -->
                                            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                                <!-- Entries Select -->
                                                <label style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: #475569; font-size: 13px; margin: 0; white-space: nowrap;">
                                                    @lang('lang_v1.show') 
                                                    <select id="custom_stock_length" class="form-control" style="border-radius: 8px; border: 1px solid #cbd5e1; padding: 6px 12px; background-color: #ffffff; outline: none; height: auto; display: inline-block; width: auto;">
                                                        <option value="25">25</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                        <option value="250">250</option>
                                                        <option value="-1">@lang('lang_v1.all')</option>
                                                    </select>
                                                    @lang('lang_v1.entries')
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @include('report.partials.stock_report_table')
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @endcan
        <input type="hidden" id="is_rack_enabled" value="{{ $rack_enabled }}">

        <div class="modal fade product_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

        <div class="modal fade" id="view_product_modal" tabindex="-1" role="dialog"
            aria-labelledby="gridSystemModalLabel">
        </div>

        <div class="modal fade" id="opening_stock_modal" tabindex="-1" role="dialog"
            aria-labelledby="gridSystemModalLabel">
        </div>

        @if ($is_woocommerce)
            @include('product.partials.toggle_woocommerce_sync_modal')
        @endif
        @include('product.partials.edit_product_location_modal')

    </section>
    <!-- /.content -->

@endsection
@section('javascript')
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        // Global getSelectedRows function with multi-selector fallback
        function getSelectedRows() {
            var selected_rows = [];
            $('#product_table tbody input.row-select').each(function () {
                if (this.checked || $(this).is(':checked') || $(this).closest('tr').hasClass('selected')) {
                    var val = $(this).val();
                    if (val && $.inArray(val, selected_rows) === -1) {
                        selected_rows.push(val);
                    }
                }
            });
            if (selected_rows.length === 0) {
                $('.row-select:checked').each(function () {
                    var val = $(this).val();
                    if (val && $.inArray(val, selected_rows) === -1) {
                        selected_rows.push(val);
                    }
                });
            }
            return selected_rows; 
        }

        function initTopScrollbarSync(tableId, topScrollId, topInnerId) {
            var $table = $('#' + tableId);
            var $topScroll = $('#' + topScrollId);
            var $topInner = $('#' + topInnerId);

            if (!$table.length || !$topScroll.length) return;

            var isSyncingTop = false;
            var isSyncingBottom = false;

            function updateSync() {
                var $wrapper = $table.closest('.dataTables_wrapper');
                if (!$wrapper.length) return;

                if (!$wrapper.prev().is($topScroll)) {
                    $topScroll.insertBefore($wrapper);
                }

                var scrollWidth = $wrapper[0].scrollWidth || $table.outerWidth();
                var clientWidth = $wrapper[0].clientWidth;

                if (scrollWidth > clientWidth + 2) {
                    $topInner.css('width', scrollWidth + 'px');
                    $topScroll.show();
                    $wrapper.addClass('has-top-scroll');
                    $topScroll.scrollLeft($wrapper.scrollLeft());
                } else {
                    $topScroll.hide();
                    $wrapper.removeClass('has-top-scroll');
                }

                if (!$wrapper.data('top-scroll-bound')) {
                    $wrapper.data('top-scroll-bound', true);
                    $wrapper.on('scroll', function() {
                        if (isSyncingBottom) {
                            isSyncingBottom = false;
                            return;
                        }
                        isSyncingTop = true;
                        $topScroll.scrollLeft($(this).scrollLeft());
                    });
                }
            }

            $topScroll.off('scroll.sync').on('scroll.sync', function() {
                if (isSyncingTop) {
                    isSyncingTop = false;
                    return;
                }
                var $wrapper = $table.closest('.dataTables_wrapper');
                if ($wrapper.length) {
                    isSyncingBottom = true;
                    $wrapper.scrollLeft($(this).scrollLeft());
                }
            });

            $table.on('draw.dt column-visibility.dt', function() {
                setTimeout(updateSync, 60);
            });

            $(window).on('resize', function() {
                updateSync();
            });

            $('a[data-toggle="tab"]').on('shown.bs.tab', function() {
                setTimeout(updateSync, 60);
            });

            setTimeout(updateSync, 100);
            setTimeout(updateSync, 500);
        }

        $(document).ready(function() {

            product_table = $('#product_table').DataTable({
                processing: true,
                serverSide: true,
                searchDelay: 400,
                autoWidth: false,
                fixedHeader: false,
                aaSorting: [
                    [2, 'asc']
                ],
                "ajax": {
                    "url": "/products",
                    "data": function(d) {
                        d.type = $('#product_list_filter_type').val();
                        d.category_id = $('#product_list_filter_category_id').val();
                        d.brand_id = $('#product_list_filter_brand_id').val();
                        d.unit_id = $('#product_list_filter_unit_id').val();
                        d.tax_id = $('#product_list_filter_tax_id').val();
                        d.active_state = $('#active_state').val();
                        d.not_for_selling = $('#not_for_selling').is(':checked');
                        d.hide_not_for_sale = $('#hide_not_for_sale').val() === '1' || $('#hide_not_for_sale_cb').is(':checked');
                        d.location_id = $('#location_id').val();
                        if ($('#repair_model_id').length == 1) {
                            d.repair_model_id = $('#repair_model_id').val();
                        }

                        if ($('#woocommerce_enabled').length == 1 && $('#woocommerce_enabled').is(
                                ':checked')) {
                            d.woocommerce_enabled = 1;
                        }

                        d = __datatable_ajax_callback(d);
                    }
                },
                columnDefs: [{
                    "targets": [0, 1, 13],
                    "orderable": false,
                    "searchable": false
                }],
                columns: [{
                        data: 'mass_delete',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'product',
                        name: 'products.name'
                    },
                    {
                        data: 'wholesale_price',
                        name: 'max_wholesale_price',
                        searchable: false
                    },
                    {
                        data: 'selling_price',
                        name: 'max_price',
                        searchable: false
                    },
                    {
                        data: 'purchase_price',
                        name: 'max_purchase_price',
                        searchable: false
                    },
                    {
                        data: 'opening_stock',
                        searchable: false
                    },
                    {
                        data: 'current_stock',
                        searchable: false
                    },
                    {
                        data: 'type',
                        name: 'products.type'
                    },
                    {
                        data: 'category',
                        name: 'c1.name'
                    },
                    {
                        data: 'brand',
                        name: 'brands.name'
                    },
                    {
                        data: 'product_locations',
                        name: 'product_locations',
                        searchable: false
                    },
                    {
                        data: 'sku',
                        name: 'products.sku'
                    },
                    {
                        data: 'image',
                        name: 'products.image',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'product_custom_field1',
                        name: 'products.product_custom_field1',
                        visible: $('#cf_1').text().length > 0
                    },
                    {
                        data: 'product_custom_field2',
                        name: 'products.product_custom_field2',
                        visible: $('#cf_2').text().length > 0
                    },
                    {
                        data: 'product_custom_field3',
                        name: 'products.product_custom_field3',
                        visible: $('#cf_3').text().length > 0
                    },
                    {
                        data: 'product_custom_field4',
                        name: 'products.product_custom_field4',
                        visible: $('#cf_4').text().length > 0
                    },
                    {
                        data: 'product_custom_field5',
                        name: 'products.product_custom_field5',
                        visible: $('#cf_5').text().length > 0
                    },
                    {
                        data: 'product_custom_field6',
                        name: 'products.product_custom_field6',
                        visible: $('#cf_6').text().length > 0
                    },
                    {
                        data: 'product_custom_field7',
                        name: 'products.product_custom_field7',
                        visible: $('#cf_7').text().length > 0
                    },
                ],
                createdRow: function(row, data, dataIndex) {
                    if ($('input#is_rack_enabled').val() == 1) {
                        var target_col = 0;
                        @can('product.delete')
                            target_col = 1;
                        @endcan
                        $(row).find('td:eq(' + target_col + ') div').prepend(
                            '<i style="margin:auto;" class="fa fa-plus-circle text-success cursor-pointer no-print rack-details" title="' +
                            LANG.details + '"></i>&nbsp;&nbsp;');
                    }
                    $(row).find('td:eq(0)').attr('class', 'selectable_td');
                },
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#product_table'));
                    $('#product_table [data-toggle="tooltip"]').tooltip();
                },
            });
            initTopScrollbarSync('product_table', 'product_table_top_scroll', 'product_table_top_scroll_inner');
            // ── Column Visibility Controls ──────────────────────────────
            var toggleableColumns = [
                { idx: 1, label: 'Action' },
                { idx: 2, label: 'Product Name' },
                { idx: 3, label: 'Wholesale Price' },
                { idx: 4, label: 'Retail Price' },
                { idx: 5, label: 'Purchase Price' },
                { idx: 6, label: 'Opening Stock' },
                { idx: 7, label: 'Current Stock' },
                { idx: 8, label: 'Product Type' },
                { idx: 9, label: 'Category' },
                { idx: 10, label: 'Brand' },
                { idx: 11, label: 'Business Location' },
                { idx: 12, label: 'SKU' },
                { idx: 13, label: 'Image' }
            ];

            for (var cf = 1; cf <= 7; cf++) {
                var cfText = $('#cf_' + cf).text().trim();
                if (cfText.length > 0) {
                    toggleableColumns.push({ idx: 13 + cf, label: cfText });
                }
            }

            var savedColVis = {};
            try {
                var stored = localStorage.getItem('vawa_products_table_colvis');
                if (stored) {
                    savedColVis = JSON.parse(stored);
                }
            } catch(e) {}

            // Populate ColVis Menu (Table Columns) immediately
            var $colvisList = $('#colvis_items_list');
            $colvisList.empty();
            toggleableColumns.forEach(function(col) {
                var isVisible = true; // initially all are visible during skeleton
                if (typeof savedColVis[col.idx] !== 'undefined') {
                    isVisible = savedColVis[col.idx];
                }
                var itemHtml = '<label class="colvis-item" data-col-idx="' + col.idx + '">' +
                    '<input type="checkbox" class="colvis-checkbox" data-col-idx="' + col.idx + '"' + (isVisible ? ' checked' : '') + '> ' +
                    '<span>' + col.label + '</span>' +
                '</label>';
                $colvisList.append(itemHtml);
            });

            // Apply saved visibility ONLY after the first loading phase finishes
            var colVisApplied = false;
            product_table.on('draw.dt', function() {
                if (!colVisApplied) {
                    colVisApplied = true;
                    toggleableColumns.forEach(function(col) {
                        if (typeof savedColVis[col.idx] !== 'undefined') {
                            product_table.column(col.idx).visible(savedColVis[col.idx], false);
                        }
                    });
                    product_table.columns.adjust();
                }
            });

            // Populate Additional Export-Only Fields in ColVis Menu
            var exportOnlyColumns = [
                { key: 'unit', label: 'Unit' },
                { key: 'opening_stock_location', label: 'Opening Stock Location' },
                { key: 'manage_stock', label: 'Manage Stock' },
                { key: 'alert_quantity', label: 'Alert Quantity' }
            ];

            var savedExportColVis = {};
            try {
                var exportStored = localStorage.getItem('vawa_products_export_extra_colvis');
                if (exportStored) {
                    savedExportColVis = JSON.parse(exportStored);
                }
            } catch(e) {}

            var $colvisExportList = $('#colvis_export_items_list');
            if ($colvisExportList.length) {
                $colvisExportList.empty();
                exportOnlyColumns.forEach(function(item) {
                    var isChecked = savedExportColVis[item.key] === true;
                    var itemHtml = '<label class="colvis-item" data-export-key="' + item.key + '">' +
                        '<input type="checkbox" class="colvis-export-checkbox" data-export-key="' + item.key + '"' + (isChecked ? ' checked' : '') + '> ' +
                        '<span>' + item.label + '</span>' +
                    '</label>';
                    $colvisExportList.append(itemHtml);
                });
            }

            // Export checkbox change handler
            $(document).on('change', '.colvis-export-checkbox', function() {
                var key = $(this).data('export-key');
                var isChecked = $(this).is(':checked');
                savedExportColVis[key] = isChecked;
                try {
                    localStorage.setItem('vawa_products_export_extra_colvis', JSON.stringify(savedExportColVis));
                } catch(e) {}
            });

            // Toggle ColVis Dropdown with smart viewport boundary positioning
            $(document).on('click', '#colvis_toggle', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $('#bulk_actions_dropdown_group').removeClass('open');
                var $group = $('#colvis_dropdown_group');
                var willOpen = !$group.hasClass('open');
                $group.toggleClass('open');
                if (willOpen) {
                    var $menu = $group.find('.colvis-dropdown-menu');
                    var menuWidth = $menu.outerWidth() || 260;
                    var offset = $group.offset();
                    var winWidth = $(window).width();
                    if (offset && (offset.left + menuWidth > winWidth - 16)) {
                        $menu.css({ 'left': 'auto', 'right': '0' });
                    } else {
                        $menu.css({ 'left': '0', 'right': 'auto' });
                    }
                }
            });

            // Prevent dropdown from closing when clicking inside menu
            $(document).on('click', '.colvis-dropdown-menu', function(e) {
                e.stopPropagation();
            });

            // Close dropdowns on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#colvis_dropdown_group').length) {
                    $('#colvis_dropdown_group').removeClass('open');
                }
            });

            // Checkbox change handler
            $(document).on('change', '.colvis-checkbox', function() {
                var colIdx = parseInt($(this).data('col-idx'));
                var isVisible = $(this).is(':checked');
                product_table.column(colIdx).visible(isVisible, false);
                product_table.columns.adjust().draw(false);

                savedColVis[colIdx] = isVisible;
                try {
                    localStorage.setItem('vawa_products_table_colvis', JSON.stringify(savedColVis));
                } catch(e) {}
            });

            // Show All columns
            $(document).on('click', '#colvis_show_all', function(e) {
                e.preventDefault();
                toggleableColumns.forEach(function(col) {
                    product_table.column(col.idx).visible(true, false);
                    savedColVis[col.idx] = true;
                });
                $('.colvis-checkbox').prop('checked', true);
                product_table.columns.adjust().draw(false);
                try {
                    localStorage.setItem('vawa_products_table_colvis', JSON.stringify(savedColVis));
                } catch(e) {}
            });

            // Reset columns to default
            $(document).on('click', '#colvis_reset', function(e) {
                e.preventDefault();
                savedColVis = {};
                savedExportColVis = {};
                try {
                    localStorage.removeItem('vawa_products_table_colvis');
                    localStorage.removeItem('vawa_products_export_extra_colvis');
                } catch(e) {}
                toggleableColumns.forEach(function(col) {
                    var defaultVisible = true;
                    if (col.idx > 13) {
                        defaultVisible = $('#cf_' + (col.idx - 13)).text().trim().length > 0;
                    }
                    product_table.column(col.idx).visible(defaultVisible, false);
                    $('.colvis-checkbox[data-col-idx="' + col.idx + '"]').prop('checked', defaultVisible);
                });
                $('.colvis-export-checkbox').prop('checked', false);
                product_table.columns.adjust().draw(false);
            });
            // ─────────────────────────────────────────────────────────────

            // Array to track the ids of the details displayed rows
            var detailRows = [];

            // ── Search & length bindings (bound at page load, NOT inside tab events) ──
            var product_search_timer;
            $('#custom_product_search').on('keyup input', function() {
                var val = $(this).val();
                clearTimeout(product_search_timer);
                product_search_timer = setTimeout(function() {
                    product_table.search(val).draw();
                }, 400);
            });

            $('#custom_product_length').on('change', function() {
                product_table.page.len($(this).val()).draw();
            });

            // Re-apply custom filter values to search input on tab change
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                var target = $(e.target).attr('href');
                if (target == '#product_list_tab') {
                    $('#custom_product_search').val(product_table.search());
                } else if (target == '#product_stock_report') {
                    if (typeof stock_report_table !== 'undefined') {
                        $('#custom_stock_search').val(stock_report_table.search());
                    }
                }
            });

            var stock_search_timer;
            $('#custom_stock_search').on('keyup input', function() {
                var val = this.value;
                clearTimeout(stock_search_timer);
                stock_search_timer = setTimeout(function() {
                    if (typeof stock_report_table !== 'undefined' && stock_report_table !== null) {
                        stock_report_table.search(val).draw();
                    }
                }, 400);
            });
            $('#custom_stock_length').on('change', function() {
                if (typeof stock_report_table !== 'undefined' && stock_report_table !== null) {
                    stock_report_table.page.len(this.value).draw();
                }
            });
            // ──────────────────────────────────────────────────────────────────────────

            $('#product_table tbody').on('click', 'tr i.rack-details', function() {
                var i = $(this);
                var tr = $(this).closest('tr');
                var row = product_table.row(tr);
                var idx = $.inArray(tr.attr('id'), detailRows);

                if (row.child.isShown()) {
                    i.addClass('fa-plus-circle text-success');
                    i.removeClass('fa-minus-circle text-danger');

                    row.child.hide();

                    // Remove from the 'open' array
                    detailRows.splice(idx, 1);
                } else {
                    i.removeClass('fa-plus-circle text-success');
                    i.addClass('fa-minus-circle text-danger');

                    row.child(get_product_details(row.data())).show();

                    // Add to the 'open' array
                    if (idx === -1) {
                        detailRows.push(tr.attr('id'));
                    }
                }
            });

            $('#opening_stock_modal').on('hidden.bs.modal', function(e) {
                product_table.ajax.reload();
            });

            $('table#product_table tbody').on('click', 'a.delete-product', function(e) {
                e.preventDefault();
                swal({
                    title: (typeof LANG !== 'undefined' && LANG.sure) ? LANG.sure : 'Are you sure?',
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var href = $(this).attr('href');
                        $.ajax({
                            method: "DELETE",
                            url: href,
                            dataType: "json",
                            success: function(result) {
                                if (result.success == true) {
                                    toastr.success(result.msg);
                                    product_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            }
                        });
                    }
                });
            });



            $('table#product_table tbody').on('click', 'a.activate-product', function(e) {
                e.preventDefault();
                var href = $(this).attr('href');
                $.ajax({
                    method: "get",
                    url: href,
                    dataType: "json",
                    success: function(result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            product_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            });

            $(document).on('change',
                '#product_list_filter_type, #product_list_filter_category_id, #product_list_filter_brand_id, #product_list_filter_unit_id, #product_list_filter_tax_id, #location_id, #active_state, #repair_model_id',
                function() {
                    if ($("#product_list_tab").hasClass('active')) {
                        product_table.ajax.reload();
                    }

                    if ($("#product_stock_report").hasClass('active')) {
                        stock_report_table.ajax.reload();
                    }
                });

            $(document).on('ifChanged', '#not_for_selling, #woocommerce_enabled', function() {
                if ($("#product_list_tab").hasClass('active')) {
                    product_table.ajax.reload();
                }

                if ($("#product_stock_report").hasClass('active')) {
                    stock_report_table.ajax.reload();
                }
            });

            function setHideNotForSaleState(active) {
                $('#hide_not_for_sale').val(active ? '1' : '0');
                if (active) {
                    $('#btn_toggle_hide_not_for_sale').css({
                        'background-color': '{{ $primary_hex }}',
                        'border-color': '{{ $primary_hex }}',
                        'color': '#ffffff'
                    });
                    $('#icon_hide_not_for_sale').attr('class', 'fa fa-check').css('color', '#ffffff');
                    $('#text_hide_not_for_sale').text('Hiding Not for Sale');
                    if ($('#hide_not_for_sale_cb').length && !$('#hide_not_for_sale_cb').is(':checked')) {
                        $('#hide_not_for_sale_cb').iCheck('check');
                    }
                } else {
                    $('#btn_toggle_hide_not_for_sale').css({
                        'background-color': '#ffffff',
                        'border-color': '#cbd5e1',
                        'color': '#334155'
                    });
                    $('#icon_hide_not_for_sale').attr('class', 'fa fa-eye-slash').css('color', '#64748b');
                    $('#text_hide_not_for_sale').text('Hide Not for Sale');
                    if ($('#hide_not_for_sale_cb').length && $('#hide_not_for_sale_cb').is(':checked')) {
                        $('#hide_not_for_sale_cb').iCheck('uncheck');
                    }
                }
            }

            $(document).on('click', '#btn_toggle_hide_not_for_sale', function(e) {
                e.preventDefault();
                var current = $('#hide_not_for_sale').val() === '1';
                setHideNotForSaleState(!current);
                if ($("#product_list_tab").hasClass('active')) {
                    product_table.ajax.reload();
                }
                if ($("#product_stock_report").hasClass('active')) {
                    stock_report_table.ajax.reload();
                }
            });

            $(document).on('ifChanged', '#hide_not_for_sale_cb', function() {
                var isChecked = $(this).is(':checked');
                if ((isChecked && $('#hide_not_for_sale').val() !== '1') || (!isChecked && $('#hide_not_for_sale').val() === '1')) {
                    setHideNotForSaleState(isChecked);
                    if ($("#product_list_tab").hasClass('active')) {
                        product_table.ajax.reload();
                    }
                    if ($("#product_stock_report").hasClass('active')) {
                        stock_report_table.ajax.reload();
                    }
                }
            });

            $(document).on('click', '#download_product_excel', function(e) {
                e.preventDefault();
                var base_url = "{{ action([\App\Http\Controllers\ProductController::class, 'downloadExcel']) }}";
                var params = {};
                if ($('#hide_not_for_sale').val() === '1' || ($('#hide_not_for_sale_cb').length && $('#hide_not_for_sale_cb').is(':checked'))) {
                    params.hide_not_for_sale = 1;
                }
                if ($('#product_list_filter_type').val()) {
                    params.type = $('#product_list_filter_type').val();
                }
                if ($('#product_list_filter_category_id').val()) {
                    params.category_id = $('#product_list_filter_category_id').val();
                }
                if ($('#product_list_filter_brand_id').val()) {
                    params.brand_id = $('#product_list_filter_brand_id').val();
                }
                if ($('#product_list_filter_unit_id').val()) {
                    params.unit_id = $('#product_list_filter_unit_id').val();
                }
                if ($('#product_list_filter_tax_id').val()) {
                    params.tax_id = $('#product_list_filter_tax_id').val();
                }
                if ($('#active_state').val()) {
                    params.active_state = $('#active_state').val();
                }
                if ($('#location_id').val()) {
                    params.location_id = $('#location_id').val();
                }

                // Include hidden columns from hide/unhide columns toggle
                var hidden_cols = [];
                var col_key_map = {
                    2: 'name',
                    3: 'wholesale_price',
                    4: 'retail_price',
                    5: 'purchase_price',
                    6: 'opening_stock',
                    7: 'current_stock',
                    8: 'product_type',
                    9: 'category',
                    10: 'brand',
                    11: 'product_locations',
                    12: 'sku'
                };

                if (typeof product_table !== 'undefined' && typeof toggleableColumns !== 'undefined') {
                    toggleableColumns.forEach(function(col) {
                        if (!product_table.column(col.idx).visible()) {
                            if (col_key_map[col.idx]) {
                                hidden_cols.push(col_key_map[col.idx]);
                            }
                            hidden_cols.push(col.idx);
                        }
                    });
                }

                // Check additional export-only columns: if NOT checked, add to hidden_cols
                if (typeof exportOnlyColumns !== 'undefined') {
                    exportOnlyColumns.forEach(function(item) {
                        var isChecked = $('.colvis-export-checkbox[data-export-key="' + item.key + '"]').is(':checked');
                        if (!isChecked) {
                            hidden_cols.push(item.key);
                        }
                    });
                } else {
                    hidden_cols.push('unit', 'opening_stock_location', 'manage_stock', 'alert_quantity');
                }

                if (hidden_cols.length > 0) {
                    params.hidden_cols = hidden_cols.join(',');
                }

                var qs = $.param(params);
                window.location.href = qs ? base_url + '?' + qs : base_url;
            });

        // Rich alert for empty bulk action selection
        function showNoRowSelectedAlert() {
            swal({
                title: (typeof LANG !== 'undefined' && LANG.no_row_selected) ? LANG.no_row_selected : 'No Products Selected',
                text: 'Please select at least one product using the table checkboxes before performing this action.',
                icon: 'warning',
                button: {
                    text: 'OK',
                    className: 'tw-dw-btn tw-dw-btn-primary'
                }
            });
        }

        // Reliable Toggle for Bulk Actions Dropdown
        $(document).on('click', '#bulk_actions_toggle', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#bulk_actions_dropdown_group').toggleClass('open');
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#bulk_actions_dropdown_group').length) {
                $('#bulk_actions_dropdown_group').removeClass('open');
            }
        });

        // Bulk action: Edit
        $(document).on('click', '#bulk-action-edit, #edit-selected', function(e) {
            e.preventDefault();
            $('#bulk_actions_dropdown_group').removeClass('open');
            var selected_rows = getSelectedRows();
            if (selected_rows.length > 0) {
                $('input#selected_products_for_edit').val(selected_rows.join(','));
                if ($('form#bulk_edit_form').length > 0) {
                    $('form#bulk_edit_form')[0].submit();
                }
            } else {
                showNoRowSelectedAlert();
            }
        });

        // Bulk action: Deactivate
        $(document).on('click', '#bulk-action-deactivate, #deactivate-selected', function(e) {
            e.preventDefault();
            $('#bulk_actions_dropdown_group').removeClass('open');
            var selected_rows = getSelectedRows();
            if (selected_rows.length > 0) {
                swal({
                    title: (typeof LANG !== 'undefined' && LANG.sure) ? LANG.sure : 'Are you sure?',
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDeactivate) => {
                    if (willDeactivate) {
                        var form = $('form#mass_deactivate_form');
                        if (form.length > 0) {
                            form.find('input#selected_products').val(selected_rows.join(','));
                            $.ajax({
                                method: 'POST',
                                url: form.attr('action'),
                                dataType: 'json',
                                data: form.serialize(),
                                success: function(result) {
                                    if (result.success == true || result.success == 1) {
                                        toastr.success(result.msg || 'Products deactivated successfully');
                                        if (typeof product_table !== 'undefined') {
                                            product_table.ajax.reload();
                                        }
                                        form.find('input#selected_products').val('');
                                    } else {
                                        toastr.error(result.msg || 'Error deactivating products');
                                    }
                                },
                                error: function() {
                                    toastr.error('An error occurred');
                                }
                            });
                        }
                    }
                });
            } else {
                showNoRowSelectedAlert();
            }
        });

        function submitMassNotForSelling(not_for_selling) {
            var selected_rows = getSelectedRows();
            if (selected_rows.length > 0) {
                swal({
                    title: (typeof LANG !== 'undefined' && LANG.sure) ? LANG.sure : 'Are you sure?',
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((confirmed) => {
                    if (confirmed) {
                        var form = $('form#mass_not_for_selling_form');
                        if (form.length > 0) {
                            form.find('input#selected_products_nfs').val(selected_rows.join(','));
                            form.find('input#not_for_selling_flag').val(not_for_selling);
                            $.ajax({
                                method: 'POST',
                                url: form.attr('action'),
                                dataType: 'json',
                                data: form.serialize(),
                                success: function(result) {
                                    if (result.success == true || result.success == 1) {
                                        toastr.success(result.msg);
                                        if (typeof product_table !== 'undefined') {
                                            product_table.ajax.reload();
                                        }
                                        form.find('input#selected_products_nfs').val('');
                                    } else {
                                        toastr.error(result.msg || 'Error updating products');
                                    }
                                },
                                error: function() {
                                    toastr.error('An error occurred');
                                }
                            });
                        }
                    }
                });
            } else {
                showNoRowSelectedAlert();
            }
        }

        $(document).on('click', '#bulk-action-not-for-selling', function(e) {
            e.preventDefault();
            $('#bulk_actions_dropdown_group').removeClass('open');
            submitMassNotForSelling(1);
        });

        $(document).on('click', '#bulk-action-for-selling', function(e) {
            e.preventDefault();
            $('#bulk_actions_dropdown_group').removeClass('open');
            submitMassNotForSelling(0);
        });

        // Bulk action: Delete
        $(document).on('click', '#bulk-action-delete, #delete-selected', function(e) {
            e.preventDefault();
            $('#bulk_actions_dropdown_group').removeClass('open');
            var selected_rows = getSelectedRows();
            if (selected_rows.length > 0) {
                swal({
                    title: (typeof LANG !== 'undefined' && LANG.sure) ? LANG.sure : 'Are you sure?',
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        var form = $('form#mass_delete_form');
                        if (form.length > 0) {
                            form.find('input#selected_rows').val(selected_rows.join(','));
                            form[0].submit();
                        }
                    }
                });
            } else {
                showNoRowSelectedAlert();
            }
        });

        // Bulk action: Location modal
        $(document).on('click', '.bulk-action-location, .update_product_location', function(e) {
            e.preventDefault();
            $('#bulk_actions_dropdown_group').removeClass('open');
            var selected_rows = getSelectedRows();

            if (selected_rows.length > 0) {
                $('input#selected_products').val(selected_rows.join(','));
                var type = $(this).data('type');
                var modal = $('#edit_product_location_modal');
                if (type == 'add') {
                    modal.find('.remove_from_location_title').addClass('hide');
                    modal.find('.add_to_location_title').removeClass('hide');
                } else if (type == 'remove') {
                    modal.find('.add_to_location_title').addClass('hide');
                    modal.find('.remove_from_location_title').removeClass('hide');
                }

                modal.find('#products_to_update_location').val(selected_rows.join(','));
                modal.find('#update_type').val(type);
                modal.modal('show');
                modal.find('#product_location').select2({
                    dropdownParent: modal
                });
                modal.find('#product_location').val('').change();
            } else {
                $('input#selected_products').val('');
                showNoRowSelectedAlert();
            }
        });

            $(document).on('submit', 'form#edit_product_location_form', function(e) {
                e.preventDefault();
                var form = $(this);
                var data = form.serialize();

                $.ajax({
                    method: $(this).attr('method'),
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    beforeSend: function(xhr) {
                        __disable_submit_button(form.find('button[type="submit"]'));
                    },
                    success: function(result) {
                        if (result.success == true) {
                            $('div#edit_product_location_modal').modal('hide');
                            toastr.success(result.msg);
                            product_table.ajax.reload();
                            $('form#edit_product_location_form')
                                .find('button[type="submit"]')
                                .attr('disabled', false);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });

            $('#product_location').select2({
                dropdownParent: $('#product_location').closest('.modal')
            });

            @if ($is_woocommerce)
                $(document).on('click', '.toggle_woocomerce_sync', function(e) {
                    e.preventDefault();
                    var selected_rows = getSelectedRows();
                    if (selected_rows.length > 0) {
                        $('#woocommerce_sync_modal').modal('show');
                        $("input#woocommerce_products_sync").val(selected_rows);
                    } else {
                        $('input#selected_products').val('');
                        swal('@lang('lang_v1.no_row_selected')');
                    }
                });

                $(document).on('submit', 'form#toggle_woocommerce_sync_form', function(e) {
                    e.preventDefault();
                    var url = $('form#toggle_woocommerce_sync_form').attr('action');
                    var method = $('form#toggle_woocommerce_sync_form').attr('method');
                    var data = $('form#toggle_woocommerce_sync_form').serialize();
                    var ladda = Ladda.create(document.querySelector('.ladda-button'));
                    ladda.start();
                    $.ajax({
                        method: method,
                        dataType: "json",
                        url: url,
                        data: data,
                        success: function(result) {
                            ladda.stop();
                            if (result.success) {
                                $("input#woocommerce_products_sync").val('');
                                $('#woocommerce_sync_modal').modal('hide');
                                toastr.success(result.msg);
                                product_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                });
            @endif
        });

        $(document).on('shown.bs.modal', 'div.view_product_modal, div.view_modal, #view_product_modal',
            function() {
                var div = $(this).find('#view_product_stock_details');
                if (div.length) {
                    $.ajax({
                        url: "{{ action([\App\Http\Controllers\ReportController::class, 'getStockReport']) }}" +
                            '?for=view_product&product_id=' + div.data('product_id'),
                        dataType: 'html',
                        success: function(result) {
                            div.html(result);
                            __currency_convert_recursively(div);
                        },
                    });
                }
                __currency_convert_recursively($(this));
            });
        var data_table_initailized = false;
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            if ($(e.target).attr('href') == '#product_stock_report') {
                if (!data_table_initailized) {
                    //Stock report table
                    var stock_report_cols = [{
                            data: 'action',
                            name: 'action',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'sku',
                            name: 'variations.sub_sku'
                        },
                        {
                            data: 'product',
                            name: 'p.name'
                        },
                        {
                            data: 'variation',
                            name: 'variation',
                            searchable: false
                        },
                        {
                            data: 'category_name',
                            name: 'c.name'
                        },
                        {
                            data: 'location_name',
                            name: 'l.name'
                        },
                        {
                            data: 'unit_price',
                            name: 'variations.sell_price_inc_tax'
                        },
                        {
                            data: 'stock',
                            name: 'stock',
                            searchable: false
                        },
                    ];
                    if ($('th.stock_price').length) {
                        stock_report_cols.push({
                            data: 'stock_price',
                            name: 'stock_price',
                            searchable: false
                        });
                        stock_report_cols.push({
                            data: 'stock_value_by_sale_price',
                            name: 'stock_value_by_sale_price',
                            searchable: false,
                            orderable: false
                        });
                        stock_report_cols.push({
                            data: 'potential_profit',
                            name: 'potential_profit',
                            searchable: false,
                            orderable: false
                        });
                    }

                    stock_report_cols.push({
                        data: 'total_sold',
                        name: 'total_sold',
                        searchable: false
                    });
                    stock_report_cols.push({
                        data: 'total_transfered',
                        name: 'total_transfered',
                        searchable: false
                    });
                    stock_report_cols.push({
                        data: 'total_adjusted',
                        name: 'total_adjusted',
                        searchable: false
                    });


                    if ($('th.current_stock_mfg').length) {
                        stock_report_cols.push({
                            data: 'total_mfg_stock',
                            name: 'total_mfg_stock',
                            searchable: false
                        });
                    }
                    stock_report_table = $('#stock_report_table').DataTable({
                        order: [
                            [1, 'asc']
                        ],
                        processing: true,
                        serverSide: true,
                        autoWidth: false,
                        fixedHeader: false,
                        ajax: {
                            url: '/reports/stock-report',
                            data: function(d) {
                                d.location_id = $('#location_id').val();
                                d.category_id = $('#product_list_filter_category_id').val();
                                d.brand_id = $('#product_list_filter_brand_id').val();
                                d.unit_id = $('#product_list_filter_unit_id').val();
                                d.type = $('#product_list_filter_type').val();
                                d.active_state = $('#active_state').val();
                                d.not_for_selling = $('#not_for_selling').is(':checked');
                        d.hide_not_for_sale = $('#hide_not_for_sale').val() === '1' || $('#hide_not_for_sale_cb').is(':checked');
                                if ($('#repair_model_id').length == 1) {
                                    d.repair_model_id = $('#repair_model_id').val();
                                }
                            }
                        },
                        columns: stock_report_cols,
                        fnDrawCallback: function(oSettings) {
                            __currency_convert_recursively($('#stock_report_table'));
                        },
                        "footerCallback": function(row, data, start, end, display) {
                            var footer_total_stock = 0;
                            var footer_total_sold = 0;
                            var footer_total_transfered = 0;
                            var total_adjusted = 0;
                            var total_stock_price = 0;
                            var footer_stock_value_by_sale_price = 0;
                            var total_potential_profit = 0;
                            var footer_total_mfg_stock = 0;
                            for (var r in data) {
                                footer_total_stock += $(data[r].stock).data('orig-value') ?
                                    parseFloat($(data[r].stock).data('orig-value')) : 0;

                                footer_total_sold += $(data[r].total_sold).data('orig-value') ?
                                    parseFloat($(data[r].total_sold).data('orig-value')) : 0;

                                footer_total_transfered += $(data[r].total_transfered).data(
                                        'orig-value') ?
                                    parseFloat($(data[r].total_transfered).data('orig-value')) : 0;

                                total_adjusted += $(data[r].total_adjusted).data('orig-value') ?
                                    parseFloat($(data[r].total_adjusted).data('orig-value')) : 0;

                                total_stock_price += $(data[r].stock_price).data('orig-value') ?
                                    parseFloat($(data[r].stock_price).data('orig-value')) : 0;

                                footer_stock_value_by_sale_price += $(data[r].stock_value_by_sale_price)
                                    .data('orig-value') ?
                                    parseFloat($(data[r].stock_value_by_sale_price).data(
                                        'orig-value')) : 0;

                                total_potential_profit += $(data[r].potential_profit).data(
                                        'orig-value') ?
                                    parseFloat($(data[r].potential_profit).data('orig-value')) : 0;

                                footer_total_mfg_stock += $(data[r].total_mfg_stock).data(
                                        'orig-value') ?
                                    parseFloat($(data[r].total_mfg_stock).data('orig-value')) : 0;
                            }

                            $('.footer_total_stock').html(__currency_trans_from_en(footer_total_stock,
                                false));
                            $('.footer_total_stock_price').html(__currency_trans_from_en(
                                total_stock_price));
                            $('.footer_total_sold').html(__currency_trans_from_en(footer_total_sold,
                                false));
                            $('.footer_total_transfered').html(__currency_trans_from_en(
                                footer_total_transfered, false));
                            $('.footer_total_adjusted').html(__currency_trans_from_en(total_adjusted,
                                false));
                            $('.footer_stock_value_by_sale_price').html(__currency_trans_from_en(
                                footer_stock_value_by_sale_price));
                            $('.footer_potential_profit').html(__currency_trans_from_en(
                                total_potential_profit));
                            if ($('th.current_stock_mfg').length) {
                                $('.footer_total_mfg_stock').html(__currency_trans_from_en(
                                    footer_total_mfg_stock, false));
                            }
                        },
                    });
                    initTopScrollbarSync('stock_report_table', 'stock_report_table_top_scroll', 'stock_report_table_top_scroll_inner');
                    data_table_initailized = true;
                } else {
                    stock_report_table.ajax.reload();
                }
            } else {
                product_table.ajax.reload();
            }



            // Reliable Toggle for Bulk Actions Dropdown
            $(document).on('click', '#bulk_actions_toggle', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $('#bulk_actions_dropdown_group').toggleClass('open');
            });

            // Close dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#bulk_actions_dropdown_group').length) {
                    $('#bulk_actions_dropdown_group').removeClass('open');
                }
            });

            // Bulk action dropdown handlers
            $(document).on('click', '#bulk-action-edit', function(e) {
                e.preventDefault();
                $('#bulk_actions_dropdown_group').removeClass('open');
                var selected_rows = getSelectedRows();
                if (selected_rows.length > 0) {
                    $('input#selected_products_for_edit').val(selected_rows.join(','));
                    if ($('form#bulk_edit_form').length) {
                        $('form#bulk_edit_form')[0].submit();
                    }
                } else {
                    swal('@lang('lang_v1.no_row_selected')');
                }
            });



            $(document).on('click', '#bulk-action-deactivate, #deactivate-selected', function(e) {
                e.preventDefault();
                $('#bulk_actions_dropdown_group').removeClass('open');
                var selected_rows = getSelectedRows();
                if (selected_rows.length > 0) {
                    swal({
                        title: LANG.sure,
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDeactivate) => {
                        if (willDeactivate) {
                            var form = $('form#mass_deactivate_form');
                            form.find('input#selected_products').val(selected_rows.join(','));
                            var data = form.serialize();
                            $.ajax({
                                method: 'POST',
                                url: form.attr('action'),
                                dataType: 'json',
                                data: data,
                                success: function(result) {
                                    if (result.success == true) {
                                        toastr.success(result.msg);
                                        product_table.ajax.reload();
                                        form.find('input#selected_products').val('');
                                    } else {
                                        toastr.error(result.msg);
                                    }
                                },
                            });
                        }
                    });
                } else {
                    swal('@lang('lang_v1.no_row_selected')');
                }
            });

            $(document).on('click', '#bulk-action-delete, #delete-selected', function(e) {
                e.preventDefault();
                $('#bulk_actions_dropdown_group').removeClass('open');
                var selected_rows = getSelectedRows();
                if (selected_rows.length > 0) {
                    $('input#selected_rows').val(selected_rows.join(','));
                    swal({
                        title: LANG.sure,
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            if ($('form#mass_delete_form').length > 0) {
                                $('form#mass_delete_form')[0].submit();
                            }
                        }
                    });
                } else {
                    swal('@lang('lang_v1.no_row_selected')');
                }
            });
        });

        // Enhanced getSelectedRows function to detect both :checked inputs and tr.selected rows
        function getSelectedRows() {
            var selected_rows = [];
            $('#product_table tbody input.row-select').each(function () {
                if ($(this).is(':checked') || $(this).closest('tr').hasClass('selected')) {
                    var val = $(this).val();
                    if (val && $.inArray(val, selected_rows) === -1) {
                        selected_rows.push(val);
                    }
                }
            });
            return selected_rows; 
        }

        $(document).on('click', '.bulk-action-location, .update_product_location', function(e) {
            e.preventDefault();
            $('#bulk_actions_dropdown_group').removeClass('open');
            var selected_rows = getSelectedRows();

            if (selected_rows.length > 0) {
                $('input#selected_products').val(selected_rows.join(','));
                var type = $(this).data('type');
                var modal = $('#edit_product_location_modal');
                if (type == 'add') {
                    modal.find('.remove_from_location_title').addClass('hide');
                    modal.find('.add_to_location_title').removeClass('hide');
                } else if (type == 'remove') {
                    modal.find('.add_to_location_title').addClass('hide');
                    modal.find('.remove_from_location_title').removeClass('hide');
                }

                modal.find('#products_to_update_location').val(selected_rows.join(','));
                modal.find('#update_type').val(type);
                modal.modal('show');
                modal.find('#product_location').select2({
                    dropdownParent: modal
                });
                modal.find('#product_location').val('').change();
            } else {
                $('input#selected_products').val('');
                swal('@lang('lang_v1.no_row_selected')');
            }
        });

        $(document).on('submit', 'form#edit_product_location_form', function(e) {
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();

            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                beforeSend: function(xhr) {
                    __disable_submit_button(form.find('button[type="submit"]'));
                },
                success: function(result) {
                    if (result.success == true) {
                        $('div#edit_product_location_modal').modal('hide');
                        toastr.success(result.msg);
                        product_table.ajax.reload();
                        $('form#edit_product_location_form')
                            .find('button[type="submit"]')
                            .attr('disabled', false);
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        });
    </script>
@endsection
