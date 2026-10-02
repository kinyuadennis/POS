@extends('layouts.app')
@section('title', __('lang_v1.product_stock_history'))

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
    .content-header {
        background-color: #ffffff !important;
        padding: 16px 24px !important;
        border-bottom: 1px solid #cbd5e160 !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
    }

    .stock-history-content {
        background-color: {{ $primary_hex }}0d !important;
        padding: 24px !important;
        min-height: calc(100vh - 100px);
        overflow: visible;
    }

    .search-section {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 20px !important;
        margin-bottom: 24px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
        position: relative;
        z-index: 10;
        overflow: visible;
    }

    .search-section-title {
        font-size: 16px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin-top: 0 !important;
        margin-bottom: 16px !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    .search-section-title i {
        color: {{ $primary_hex }} !important;
    }

    .search-section label {
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        margin-bottom: 6px !important;
    }

    .search-section .form-control,
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-search--dropdown .select2-search__field,
    .select2-dropdown {
        background-color: #ffffff !important;
        color: #1e293b !important;
    }

    .form-control {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        height: 40px !important;
        box-shadow: none !important;
        color: #1e293b !important;
        font-size: 14px !important;
    }
    .form-control:focus {
        border-color: {{ $primary_hex }} !important;
        box-shadow: 0 0 0 3px {{ $primary_hex }}26 !important;
    }

    .select2-container--default .select2-selection--single {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        height: 40px !important;
        padding: 5px 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
        color: #1e293b !important;
        background-color: #ffffff !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #94a3b8 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        padding: 6px 10px !important;
        color: #1e293b !important;
        background-color: #ffffff !important;
    }
    .select2-container--default .select2-results__option {
        background-color: #ffffff !important;
        color: #1e293b !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
    }

    #stock_history_table_wrapper > .row:first-child,
    #stock_history_table_wrapper .dataTables_length,
    #stock_history_table_wrapper .dt-buttons,
    #stock_history_table_wrapper .dataTables_filter {
        display: none !important;
    }

    /* Skeleton Loading Animation */
    @keyframes skeleton-shimmer {
        0% { background-position: -200px 0; }
        100% { background-position: calc(200px + 100%) 0; }
    }

    .skeleton-block {
        display: inline-block;
        height: 14px;
        width: 100%;
        background: #e2e8f0;
        background: linear-gradient(90deg, #e2e8f0 0px, #f1f5f9 50px, #e2e8f0 100px);
        background-size: 200px 100%;
        animation: skeleton-shimmer 1.5s infinite linear;
        border-radius: 4px;
    }

    @media (max-width: 767px) {
        .content-header { padding: 12px 16px !important; }
        .stock-history-content { padding: 14px 12px !important; }
        .search-section { padding: 14px 12px !important; margin-bottom: 16px !important; }
    }
</style>

<section class="content-header tw-flex tw-justify-between tw-items-center">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang('lang_v1.product_stock_history')</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">View stock transactions, adjustments, and movement history for {{ $product->name }}</p>
    </div>
</section>

<section class="content stock-history-content">
    <div class="search-section">
        <h3 class="search-section-title">
            <i class="fa fa-filter"></i>
            Filter History & Product Selection
        </h3>
        <div class="row">
            <div class="col-md-5">
                <div class="form-group" style="margin-bottom: 0;">
                    {!! Form::label('product_id', __('sale.product') . ':') !!}
                    {!! Form::select('product_id', [$product->id=>$product->name . ' - ' . $product->sku], $product->id, ['class' => 'form-control', 'style' => 'width:100%']); !!}
                    <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;"><i class="fa fa-search tw-mr-1"></i> Type product name or SKU to search another product</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group" style="margin-bottom: 0;">
                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('location_id', $business_locations, request()->input('location_id', null), ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                </div>
            </div>
            @if($product->type == 'variable')
                <div class="col-md-4">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="variation_id">@lang('product.variations'):</label>
                        <select class="select2 form-control" name="variation_id" id="variation_id" style="width:100%">
                            @foreach($product->variations as $variation)
                                <option value="{{$variation->id}}"
                                @if(request()->input('variation_id', null) == $variation->id)
                                    selected
                                @endif
                                >{{$variation->product_variation->name}} - {{$variation->name}} ({{$variation->sub_sku}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @else
                <input type="hidden" id="variation_id" name="variation_id" value="{{$product->variations->first()->id}}">
            @endif
        </div>
    </div>

    <!-- Stock History Content Container -->
    <div id="product_stock_history"></div>

    <!-- Skeleton Template -->
    <template id="stock_history_skeleton_template">
        <div class="row" style="margin-bottom: 8px;">
            <div class="col-md-4 col-sm-6" style="margin-bottom: 16px;">
                <div class="sh-summary-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden;">
                    <div class="sh-summary-card-header" style="padding: 14px 18px; background-color: #f8fafc; border-top: 4px solid #cbd5e1;">
                        <span class="skeleton-block" style="width: 140px; height: 16px;"></span>
                    </div>
                    <div class="sh-summary-card-body" style="padding: 16px 18px;">
                        <table style="width: 100%;">
                            <tr><th style="padding: 7px 0;"><span class="skeleton-block" style="width: 75%;"></span></th><td style="text-align: right;"><span class="skeleton-block" style="width: 45px;"></span></td></tr>
                            <tr><th style="padding: 7px 0;"><span class="skeleton-block" style="width: 65%;"></span></th><td style="text-align: right;"><span class="skeleton-block" style="width: 45px;"></span></td></tr>
                            <tr><th style="padding: 7px 0;"><span class="skeleton-block" style="width: 70%;"></span></th><td style="text-align: right;"><span class="skeleton-block" style="width: 45px;"></span></td></tr>
                            <tr><th style="padding: 7px 0;"><span class="skeleton-block" style="width: 80%;"></span></th><td style="text-align: right;"><span class="skeleton-block" style="width: 45px;"></span></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6" style="margin-bottom: 16px;">
                <div class="sh-summary-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden;">
                    <div class="sh-summary-card-header" style="padding: 14px 18px; background-color: #f8fafc; border-top: 4px solid #cbd5e1;">
                        <span class="skeleton-block" style="width: 140px; height: 16px;"></span>
                    </div>
                    <div class="sh-summary-card-body" style="padding: 16px 18px;">
                        <table style="width: 100%;">
                            <tr><th style="padding: 7px 0;"><span class="skeleton-block" style="width: 70%;"></span></th><td style="text-align: right;"><span class="skeleton-block" style="width: 45px;"></span></td></tr>
                            <tr><th style="padding: 7px 0;"><span class="skeleton-block" style="width: 80%;"></span></th><td style="text-align: right;"><span class="skeleton-block" style="width: 45px;"></span></td></tr>
                            <tr><th style="padding: 7px 0;"><span class="skeleton-block" style="width: 75%;"></span></th><td style="text-align: right;"><span class="skeleton-block" style="width: 45px;"></span></td></tr>
                            <tr><th style="padding: 7px 0;"><span class="skeleton-block" style="width: 65%;"></span></th><td style="text-align: right;"><span class="skeleton-block" style="width: 45px;"></span></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12" style="margin-bottom: 16px;">
                <div class="sh-summary-card" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden;">
                    <div class="sh-summary-card-header" style="padding: 14px 18px; background-color: #f8fafc; border-top: 4px solid #cbd5e1;">
                        <span class="skeleton-block" style="width: 110px; height: 16px;"></span>
                    </div>
                    <div class="sh-summary-card-body" style="padding: 16px 18px;">
                        <table style="width: 100%;">
                            <tr><th style="padding: 7px 0;"><span class="skeleton-block" style="width: 75%;"></span></th><td style="text-align: right;"><span class="skeleton-block" style="width: 60px; height: 22px;"></span></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 24px; margin-bottom: 24px;">
            <div class="tw-flex tw-justify-between tw-items-center tw-mb-5 flex-wrap" style="margin-bottom: 20px;">
                <span class="skeleton-block" style="width: 220px; height: 24px;"></span>
                <span class="skeleton-block" style="width: 320px; height: 36px;"></span>
            </div>
            <div class="table-responsive" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                <table class="table table-slim" style="width: 100%; margin-bottom: 0;">
                    <thead>
                        <tr style="background-color: #f8fafc;">
                            <th style="padding: 12px 14px;"><span class="skeleton-block" style="width: 60px;"></span></th>
                            <th style="padding: 12px 14px;"><span class="skeleton-block" style="width: 80px;"></span></th>
                            <th style="padding: 12px 14px;"><span class="skeleton-block" style="width: 80px;"></span></th>
                            <th style="padding: 12px 14px;"><span class="skeleton-block" style="width: 100px;"></span></th>
                            <th style="padding: 12px 14px;"><span class="skeleton-block" style="width: 70px;"></span></th>
                            <th style="padding: 12px 14px;"><span class="skeleton-block" style="width: 120px;"></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td></tr>
                        <tr><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td></tr>
                        <tr><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td></tr>
                        <tr><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td></tr>
                        <tr><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td><td style="padding: 14px;"><span class="skeleton-block"></span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </template>
</section>

@endsection

@section('javascript')
   <script type="text/javascript">
        $(document).ready( function(){
            load_stock_history($('#variation_id').val(), $('#location_id').val());

            $(document).on('select2:open', '#product_id', function() {
                setTimeout(function() {
                    $('.select2-container--open .select2-search__field').attr('placeholder', 'Type product name or SKU...');
                }, 50);
            });

            $('#product_id').select2({
                placeholder: 'Type product name or SKU...',
                allowClear: false,
                ajax: {
                    url: '/products/list-no-variation',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term, // search term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data,
                        };
                    },
                },
                minimumInputLength: 1,
                escapeMarkup: function(m) {
                    return m;
                },
            }).on('select2:select', function (e) {
                var data = e.params.data;
                window.location.href = "{{url('/')}}/products/stock-history/" + data.id
            });
        });

       var history_table = null;

       function load_stock_history(variation_id, location_id) {
            $('#product_stock_history').html($('#stock_history_skeleton_template').html()).show();
            
            $.ajax({
                url: '/products/stock-history/' + variation_id + "?location_id=" + location_id,
                dataType: 'html',
                success: function(result) {
                    $('#product_stock_history')
                        .html(result)
                        .fadeIn();

                    __currency_convert_recursively($('#product_stock_history'));

                    var shExportOptions = {
                        columns: ':visible',
                        stripHtml: true
                    };

                    history_table = $('#stock_history_table').DataTable({
                        searching: false,
                        fixedHeader: false,
                        ordering: false,
                        dom: 'lBrtip',
                        pageLength: 25,
                        buttons: [
                            {
                                extend: 'excel',
                                text: '<i class="fa fa-file-excel-o"></i> Excel',
                                title: 'Stock Movement History',
                                exportOptions: shExportOptions
                            },
                            {
                                extend: 'pdf',
                                text: '<i class="fa fa-file-pdf-o"></i> PDF',
                                title: 'Stock Movement History',
                                exportOptions: shExportOptions
                            }
                        ]
                    });
                },
            });
       }

       $(document).on('change', '#custom_stock_history_length', function() {
            if (history_table) {
                history_table.page.len($(this).val()).draw();
            }
       });

       $(document).on('click', '.stock-history-export-btn', function(e) {
            e.preventDefault();
            var format = $(this).data('format');
            if (history_table) {
                history_table.button('.buttons-' + format).trigger();
            }
       });

       $(document).on('change', '#variation_id, #location_id', function(){
            load_stock_history($('#variation_id').val(), $('#location_id').val());
       });
   </script>
@endsection
