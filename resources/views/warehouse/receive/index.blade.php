@extends('layouts.app')
@section('title', __('lang_v1.warehouse_receive_stock'))

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('lang_v1.warehouse_receive_stock')</h1>
</section>

<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('lang_v1.warehouse_receive_stock')])
        <p>@lang('lang_v1.warehouse_receive_help')</p>

        <div style="margin-top: 20px;">
            @include('warehouse.partials.receive_stock_dropdown')
        </div>

        @if (!empty($warehouse_locations) && count($warehouse_locations))
            <div class="alert alert-info" style="margin-top: 20px;">
                <strong>@lang('lang_v1.warehouse_locations'):</strong>
                {{ implode(', ', $warehouse_locations->toArray()) }}
            </div>
        @endif
    @endcomponent
</section>
@endsection
