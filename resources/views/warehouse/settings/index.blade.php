@extends('layouts.app')
@section('title', __('lang_v1.warehouse_settings'))

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.warehouse_settings')</h1>
</section>

<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.warehouse_settings')])
        {!! Form::open(['url' => action([\App\Http\Controllers\Warehouse\WarehouseSettingsController::class, 'update']), 'method' => 'post', 'id' => 'warehouse_settings_form']) !!}
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('default_warehouse_location_id', __('lang_v1.warehouse_default_location') . ':') !!}
                    {!! Form::select('default_warehouse_location_id', $warehouse_locations, $default_warehouse_id, [
                        'class' => 'form-control select2',
                        'placeholder' => __('messages.please_select'),
                    ]) !!}
                    <p class="help-block">@lang('lang_v1.warehouse_default_location_help')</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white pull-right">@lang('messages.update')</button>
            </div>
        </div>
        {!! Form::close() !!}
    @endcomponent
</section>
@endsection

@section('javascript')
    <script src="{{ asset('js/warehouse.js?v=' . $asset_v) }}"></script>
@endsection
