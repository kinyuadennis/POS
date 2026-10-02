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
    .expense-pay-modal {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04) !important;
        overflow: hidden;
        background: #ffffff;
    }
    .expense-pay-modal .expense-pay-header {
        background: #ffffff;
        padding: 16px 24px;
        border-bottom: 1px solid #cbd5e160;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        text-align: left;
    }
    @media (min-width: 768px) {
        .expense-pay-modal .expense-pay-header-add {
            padding: 12px 10px 12px 12px !important;
            gap: 8px;
        }
        .expense-pay-modal .expense-pay-header-add > div:first-child {
            flex: 1 1 auto;
            min-width: 0;
            margin: 0;
            padding: 0;
            text-align: left;
        }
        .expense-pay-modal .expense-pay-header-add .expense-pay-title,
        .expense-pay-modal .expense-pay-header-add .expense-pay-subtitle {
            text-align: left;
        }
        .expense-pay-modal .expense-pay-header-add .expense-pay-close {
            margin: -2px 0 0 0;
            padding: 0;
            flex-shrink: 0;
            align-self: flex-start;
        }
    }
    .expense-pay-modal .expense-pay-title {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.3;
    }
    .expense-pay-modal .expense-pay-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        font-weight: 500;
        color: #475569;
    }
    .expense-pay-modal .expense-pay-close {
        opacity: 0.45;
        font-size: 24px;
        font-weight: 400;
        line-height: 1;
        padding: 0 4px;
        margin-top: -2px;
        float: none;
        position: static;
    }
    .expense-pay-modal .expense-pay-close:hover {
        opacity: 1;
    }
    .expense-pay-modal .expense-pay-body {
        padding: 20px 24px;
        background: {{ $primary_hex }}0d;
        max-height: calc(100vh - 220px);
        overflow-y: auto;
    }
    .expense-pay-modal .expense-pay-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }
    .expense-pay-modal .expense-pay-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        padding: 14px 16px;
        flex: 1 1 140px;
        min-width: 0;
    }
    .expense-pay-modal .expense-pay-card.meta {
        flex: 1 1 160px;
    }
    .expense-pay-modal .expense-pay-card.total-card {
        flex: 1 1 150px;
    }
    .expense-pay-modal .expense-pay-card.due-card {
        border-left: 3px solid {{ $primary_hex }};
    }
    .expense-pay-modal .expense-pay-label {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 6px;
    }
    .expense-pay-modal .expense-pay-value {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        word-break: break-word;
        line-height: 1.35;
    }
    .expense-pay-modal .expense-pay-value.amount {
        font-size: 18px;
        font-variant-numeric: tabular-nums;
    }
    .expense-pay-modal .expense-pay-value.due {
        color: {{ $primary_hex }};
    }
    .expense-pay-modal .expense-pay-section {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        padding: 20px;
    }
    .expense-pay-modal .expense-pay-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    .expense-pay-modal .expense-pay-section-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .expense-pay-modal label {
        font-weight: 500;
        color: #475569;
        font-size: 13px;
        margin-bottom: 6px;
    }
    .expense-pay-modal .form-control,
    .expense-pay-modal .select2-container--default .select2-selection--single {
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        height: 42px !important;
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05);
        background: #ffffff;
    }
    .expense-pay-modal textarea.form-control {
        height: auto !important;
        min-height: 84px;
        padding-top: 10px;
    }
    .expense-pay-modal .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px !important;
        color: #1e293b;
    }
    .expense-pay-modal .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
    .expense-pay-modal .input-group-addon {
        border-radius: 8px 0 0 8px !important;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        color: #94a3b8;
    }
    .expense-pay-modal .input-group .form-control {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
    .expense-pay-modal .expense-pay-amount-group .form-control {
        font-weight: 700;
        font-size: 16px;
        color: #1e293b;
    }
    .expense-pay-modal .help-block {
        color: #64748b;
        font-size: 12px;
        margin-top: 6px;
        margin-bottom: 0;
    }
    .expense-pay-modal .expense-pay-note {
        color: #475569;
        font-size: 13px;
        line-height: 1.45;
        margin: 0;
    }
    .expense-pay-modal .expense-pay-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 0 -4px;
    }
    .expense-pay-modal .expense-pay-table {
        margin-bottom: 0;
        width: 100%;
    }
    .expense-pay-modal .expense-pay-table thead th {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
        vertical-align: middle;
        padding: 10px 12px;
    }
    .expense-pay-modal .expense-pay-table td {
        white-space: nowrap;
        vertical-align: middle;
        color: #1e293b;
        padding: 12px;
        border-top: 1px solid #f1f5f9;
    }
    .expense-pay-modal .expense-pay-table td.amount-cell {
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }
    .expense-pay-modal .expense-pay-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: nowrap;
    }
    .expense-pay-modal .expense-pay-footer {
        background: #ffffff;
        padding: 14px 24px;
        border-top: 1px solid #cbd5e160;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }
    .expense-pay-modal .expense-pay-btn-primary {
        background: {{ $primary_hex }} !important;
        border: 1px solid {{ $primary_hex }} !important;
        color: #ffffff !important;
        border-radius: 8px !important;
        height: 42px;
        padding: 0 18px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: opacity 0.2s ease, transform 0.2s ease;
    }
    .expense-pay-modal .expense-pay-btn-primary:hover {
        opacity: 0.9;
        transform: translateY(-1px);
        color: #ffffff !important;
    }
    .expense-pay-modal .expense-pay-btn-secondary {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        color: #475569 !important;
        border-radius: 8px !important;
        height: 42px;
        padding: 0 18px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .expense-pay-modal .expense-pay-btn-xs {
        height: 32px;
        min-width: 32px;
        padding: 0 8px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
    }
    .expense-pay-modal .expense-pay-btn-xs.theme {
        background: {{ $primary_hex }};
        border-color: {{ $primary_hex }};
        color: #ffffff;
    }
    .expense-pay-modal .expense-pay-btn-xs.danger {
        color: #b91c1c;
        border-color: #fecaca;
        background: #ffffff;
    }
    .expense-pay-modal .expense-pay-empty {
        text-align: center;
        padding: 32px 12px;
        color: #64748b;
        font-size: 14px;
    }
    .expense-pay-modal .label {
        font-weight: 600;
        border-radius: 4px;
        padding: 4px 8px;
        font-size: 11px;
    }
    @media (max-width: 767px) {
        .modal-dialog.expense-pay-dialog {
            width: auto;
            margin: 10px;
        }
        .expense-pay-modal .expense-pay-header,
        .expense-pay-modal .expense-pay-body,
        .expense-pay-modal .expense-pay-footer,
        .expense-pay-modal .expense-pay-section {
            padding: 14px 12px;
        }
        .expense-pay-modal .expense-pay-body {
            max-height: calc(100vh - 170px);
        }
        .expense-pay-modal .expense-pay-card {
            flex: 1 1 calc(50% - 12px);
        }
        .expense-pay-modal .expense-pay-card.total-card {
            flex: 1 1 100%;
        }
        .expense-pay-modal .expense-pay-footer {
            flex-direction: column-reverse;
            align-items: stretch;
        }
        .expense-pay-modal .expense-pay-footer .expense-pay-btn-primary,
        .expense-pay-modal .expense-pay-footer .expense-pay-btn-secondary {
            width: 100%;
        }
        .expense-pay-modal .expense-pay-section-head .expense-pay-btn-primary {
            width: 100%;
        }
    }
    @media print {
        .expense-pay-modal .expense-pay-body {
            background: #ffffff;
            max-height: none;
            overflow: visible;
        }
        .expense-pay-modal .no-print {
            display: none !important;
        }
    }
</style>
