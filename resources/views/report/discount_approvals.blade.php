@extends('layouts.app')
@section('title', __('Discount Approval Audit Log'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
        <i class="fas fa-shield-alt tw-text-primary-600"></i>
        @lang('Discount Approval Audit Log')
        <small class="tw-text-sm tw-text-gray-500">@lang('Review all cashier discount approval requests, decisions, and history')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('approval_location_filter', __('purchase.business_location') . ':') !!}
                        {!! Form::select('approval_location_filter', $business_locations, $default_location_id ?? null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all'), 'data-location-filter' => '1']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('approval_status_filter', __('sale.status') . ':') !!}
                        {!! Form::select('approval_status_filter', [
                            '' => __('lang_v1.all'),
                            'approved' => __('Approved'),
                            'modified' => __('Modified & Approved'),
                            'rejected' => __('Rejected'),
                            'used' => __('Used in Sale'),
                            'pending' => __('Pending'),
                            'cancelled' => __('Cancelled'),
                            'expired' => __('Expired')
                        ], null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('approval_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('approval_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="discount_approvals_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang('messages.date')</th>
                                <th>@lang('Cashier')</th>
                                <th>@lang('sale.location')</th>
                                <th>@lang('Original Total')</th>
                                <th>@lang('Requested Discount')</th>
                                <th>@lang('Approved Discount')</th>
                                <th>@lang('Resulting Total')</th>
                                <th>@lang('Approver')</th>
                                <th>@lang('sale.status')</th>
                                <th>@lang('Cashier Notes')</th>
                                <th>@lang('Admin Remarks')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
</section>

<!-- Cart Details Modal for Report -->
<div class="modal fade" id="reportCartSnapshotModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header tw-bg-gray-800 tw-text-white">
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                <h4 class="modal-title tw-font-bold text-white"><i class="fas fa-shopping-cart tw-mr-2"></i> @lang('Cart Items Snapshot at Request Time')</h4>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="report_cart_table">
                        <thead>
                            <tr class="tw-bg-gray-50">
                                <th>#</th>
                                <th>@lang('Product / Variation')</th>
                                <th class="text-center">@lang('Qty')</th>
                                <th class="text-right">@lang('Unit Price')</th>
                                <th class="text-right">@lang('Line Total')</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    // Date range picker
    $('#approval_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#approval_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            discount_approvals_table.ajax.reload();
        }
    );
    $('#approval_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#approval_date_range').val('');
        discount_approvals_table.ajax.reload();
    });

    var discount_approvals_table = $('#discount_approvals_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('reports.discount_approvals') }}",
            data: function (d) {
                d.location_id = $('#approval_location_filter').val();
                d.status = $('#approval_status_filter').val();
                if ($('#approval_date_range').val()) {
                    var start = $('#approval_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#approval_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                }
            }
        },
        columns: [
            { data: 'created_at', name: 'discount_approvals.created_at' },
            { data: 'cashier_name', name: 'cashier.first_name' },
            { data: 'location_name', name: 'bl.name' },
            { data: 'original_total', name: 'discount_approvals.original_total' },
            { data: 'requested_discount', name: 'discount_approvals.requested_discount_amount' },
            { data: 'approved_discount', name: 'discount_approvals.approved_discount_amount' },
            { data: 'resulting_total', name: 'discount_approvals.resulting_total' },
            { data: 'admin_name', name: 'admin.first_name' },
            { data: 'status', name: 'discount_approvals.status' },
            { data: 'cashier_notes', name: 'discount_approvals.cashier_notes' },
            { data: 'admin_notes', name: 'discount_approvals.admin_notes' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#discount_approvals_table'));
        }
    });

    $('#approval_location_filter, #approval_status_filter').change(function() {
        discount_approvals_table.ajax.reload();
    });

    // View Cart Modal Click
    $(document).on('click', '.view-cart-details', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '/pos/discount-approvals/details/' + id,
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    var tbody = $('#report_cart_table tbody');
                    tbody.empty();
                    var items = res.cart_snapshot || [];
                    if (items.length === 0) {
                        tbody.append('<tr><td colspan="5" class="text-center text-muted">No items snapshot recorded</td></tr>');
                    } else {
                        $.each(items, function(index, item) {
                            tbody.append(
                                '<tr>' +
                                    '<td>' + (index + 1) + '</td>' +
                                    '<td><strong>' + (item.product_name || item.name || 'Item') + '</strong>' + (item.sub_sku ? ' <small class="text-muted">(' + item.sub_sku + ')</small>' : '') + '</td>' +
                                    '<td class="text-center">' + (item.quantity || 1) + '</td>' +
                                    '<td class="text-right">' + __currency_trans_from_en(item.unit_price || 0, true) + '</td>' +
                                    '<td class="text-right">' + __currency_trans_from_en((item.unit_price || 0) * (item.quantity || 1), true) + '</td>' +
                                '</tr>'
                            );
                        });
                    }
                    $('#reportCartSnapshotModal').modal('show');
                }
            }
        });
    });
});
</script>
@endsection
