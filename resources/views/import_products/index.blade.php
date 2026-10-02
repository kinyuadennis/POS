@extends('layouts.app')
@section('title', __('product.import_products'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('product.import_products')
    </h1>
</section>

<!-- Main content -->
<section class="content">
    
    @if (session('notification') || !empty($notification))
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    @if(!empty($notification['msg']))
                        {{$notification['msg']}}
                    @elseif(session('notification.msg'))
                        {{ session('notification.msg') }}
                    @endif
                </div>
            </div>  
        </div>     
    @endif

    @if (session('status') && (is_array(session('status')) ? !empty(session('status.success')) : !empty(session('status'))))
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="icon fa fa-check"></i>
                    {{ is_array(session('status')) ? (session('status.msg') ?? '') : session('status') }}
                </div>
            </div>  
        </div>     
    @endif
    
    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['class' => 'box-primary'])
                {!! Form::open(['url' => action([\App\Http\Controllers\ImportProductsController::class, 'store']), 'method' => 'post', 'enctype' => 'multipart/form-data' ]) !!}
                    <div class="row">
                        <div class="col-sm-6">
                        <div class="col-sm-8">
                            <div class="form-group">
                                {!! Form::label('name', __( 'product.file_to_import' ) . ':') !!}
                                {!! Form::file('products_csv', ['accept'=> '.xls, .xlsx, .csv', 'required' => 'required']); !!}
                              </div>
                        </div>
                        <div class="col-sm-4">
                        <br>
                            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.submit')</button>
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::hidden('update_existing', 0) !!}
                                        {!! Form::checkbox('update_existing', 1, false, ['class' => 'input-icheck']); !!} 
                                        <strong>@lang('lang_v1.update_existing_products')</strong>
                                    </label>
                                    <p class="help-block">@lang('lang_v1.update_existing_products_help')</p>
                                </div>
                            </div>
                        </div>
                    </div>

                {!! Form::close() !!}
                <br><br>
                <div class="row">
                    <div class="col-sm-4">
                        <a href="{{ action([\App\Http\Controllers\ImportProductsController::class, 'downloadTemplate']) }}" class="tw-dw-btn tw-dw-btn-success tw-text-white" download><i class="fa fa-download"></i> @lang('lang_v1.download_template_file')</a>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.instructions')])
                <strong>@lang('lang_v1.instruction_line1')</strong><br>
                    @lang('lang_v1.instruction_line2')
                    <br><br>
                <p class="help-block">Accepted file formats: CSV, XLS, XLSX. Use product type <strong>single</strong> only.</p>
                <table class="table table-striped">
                    <tr>
                        <th>@lang('lang_v1.col_no')</th>
                        <th>@lang('lang_v1.col_name')</th>
                        <th>@lang('lang_v1.instruction')</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>@lang('product.product_name') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('lang_v1.name_ins')</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>@lang('product.brand') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('lang_v1.brand_ins') <br><small class="text-muted">(@lang('lang_v1.brand_ins2'))</small></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>@lang('product.unit') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('lang_v1.unit_ins')</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>@lang('product.category') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('lang_v1.category_ins') <br><small class="text-muted">(@lang('lang_v1.category_ins2'))</small></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>@lang('product.sku') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('lang_v1.sku_ins')</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>@lang('product.retail_price') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('lang_v1.selling_price_ins')</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Wholesale Price <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>Wholesale selling price for the product. Leave blank if not applicable.</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>@lang('lang_v1.opening_stock') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('lang_v1.opening_stock_ins') {!! __('lang_v1.opening_stock_help_text') !!}</td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>@lang('report.current_stock') <small class="text-muted">(not imported)</small></td>
                        <td>Available quantity on hand. Included on Product List Excel for reference and ignored when you re-import.</td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>@lang('lang_v1.opening_stock_location') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('lang_v1.location_ins1')</td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td>@lang('lang_v1.product_locations') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('lang_v1.product_locations_ins')</td>
                    </tr>
                    <tr>
                        <td>12</td>
                        <td>@lang('product.manage_stock') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('lang_v1.manage_stock_ins')<br>
                            <strong>1 = @lang('messages.yes')<br>
                            0 = @lang('messages.no')</strong>
                        </td>
                    </tr>
                    <tr>
                        <td>13</td>
                        <td>@lang('product.alert_quantity') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                        <td>@lang('product.alert_quantity')</td>
                    </tr>
                    <tr>
                        <td>14</td>
                        <td>@lang('product.purchase_price') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                        <td>@lang('lang_v1.purchase_price_inc_tax_ins2')</td>
                    </tr>
                </table>
            @endcomponent
        </div>
    </div>
</section>

@endsection
