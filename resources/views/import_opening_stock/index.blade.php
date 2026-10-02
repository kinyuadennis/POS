@extends('layouts.app')
@section('title', __('lang_v1.import_opening_stock'))

@section('content')
<br/>
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.import_opening_stock')</h1>
</section>

<section class="content">

@if (session('notification') || !empty($notification))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                @if(!empty($notification['msg']))
                    {{$notification['msg']}}
                @elseif(is_array(session('notification')) && !empty(session('notification')['msg']))
                    {{ session('notification')['msg'] }}
                @elseif(is_string(session('notification')))
                    {{ session('notification') }}
                @endif
            </div>
        </div>
    </div>
@endif

    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['class' => 'box-primary'])
                {!! Form::open(['url' => action([\App\Http\Controllers\ImportOpeningStockController::class, 'store']), 'method' => 'post', 'enctype' => 'multipart/form-data' ]) !!}
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    {!! Form::label('name', __( 'product.file_to_import' ) . ':') !!}
                                    @show_tooltip(__('lang_v1.tooltip_import_opening_stock'))
                                    {!! Form::file('products_csv', ['accept'=> '.xls,.xlsx', 'required' => 'required']); !!}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <br>
                                <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.submit')</button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}

                <br><br>
                <div class="row">
                    <div class="col-sm-12">
                        <h4>@lang('lang_v1.download_template_file')</h4>
                        <p class="text-muted">@lang('lang_v1.opening_stock_prefilled_template_help')</p>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            {!! Form::label('template_location_id', __('business.business_location') . ':') !!}
                            {!! Form::select('template_location_id', $business_locations ?? [], $selected_location_id ?? null, [
                                'class' => 'form-control select2',
                                'id' => 'template_location_id',
                                'placeholder' => __('messages.please_select'),
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-sm-8" style="padding-top: 25px;">
                        <a href="#" id="download_prefilled_opening_stock_template" class="tw-dw-btn tw-dw-btn-primary tw-text-white">
                            <i class="fa fa-download"></i> @lang('lang_v1.download_prefilled_opening_stock_template')
                        </a>
                        <a href="{{ asset('files/import_opening_stock_csv_template.xls') }}" class="tw-dw-btn tw-dw-btn-default" download style="margin-left: 8px;">
                            <i class="fa fa-download"></i> @lang('lang_v1.download_blank_opening_stock_template')
                        </a>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.instructions')])
            <strong>@lang('lang_v1.instruction_line1')</strong><br>@lang('lang_v1.instruction_line2')
            <br><br>
            <table class="table table-striped">
                <tr>
                    <th>@lang('lang_v1.col_no')</th>
                    <th>@lang('lang_v1.col_name')</th>
                    <th>@lang('lang_v1.instruction')</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>@lang('sale.product') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                    <td>@lang('lang_v1.opening_stock_product_name_help')</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>@lang('product.sku')<small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>@lang('business.location') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                    <td>@lang('lang_v1.location_ins1')</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>@lang('lang_v1.opening_stock') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                    <td>@lang('lang_v1.opening_stock_quantity_help')</td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>@lang('report.current_stock') <small class="text-muted">(not imported)</small></td>
                    <td>@lang('lang_v1.current_stock_import_help')</td>
                </tr>
                <tr>
                    <td>6</td>
                    <td>Purchase Price <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                    <td>@lang('lang_v1.opening_stock_unit_cost_help')</td>
                </tr>
            </table>
            @endcomponent
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).on('click', '#download_prefilled_opening_stock_template', function(e) {
        e.preventDefault();
        var location_id = $('#template_location_id').val();
        if (!location_id) {
            toastr.error('{{ __('messages.please_select') }} {{ __('business.business_location') }}');
            return;
        }
        window.location = '{{ url('import-opening-stock/download-template') }}?location_id=' + location_id;
    });
</script>
@endsection
