<div id="opening_stock_section" @if(empty($show_opening_stock)) style="display:none" @endif>
    <div class="table-responsive">
        <table class="table table-condensed table-bordered" id="product_opening_stock_table">
            <thead>
                <tr class="bg-green">
                    <th>@lang('sale.location')</th>
                    <th>@lang('lang_v1.quantity')</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($is_edit) && !empty($opening_stock_data))
                    @foreach($opening_stock_data as $location_id => $stock_row)
                        <tr>
                            <td>{{ $stock_row['name'] }}</td>
                            <td>
                                @if(!empty($stock_row['purchase_line_id']))
                                    {!! Form::hidden('stocks[' . $location_id . '][' . $stock_row['variation_id'] . '][0][purchase_line_id]', $stock_row['purchase_line_id']) !!}
                                @endif
                                {!! Form::hidden('stocks[' . $location_id . '][' . $stock_row['variation_id'] . '][0][purchase_price]', null, ['class' => 'opening_stock_purchase_price']) !!}
                                {!! Form::text('stocks[' . $location_id . '][' . $stock_row['variation_id'] . '][0][quantity]', @format_quantity($stock_row['quantity']), [
                                    'class' => 'form-control input-sm input_number opening_stock_qty',
                                ]); !!}
                            </td>
                        </tr>
                    @endforeach
                @else
                    @php
                        $stock_locations = !empty($stock_locations) ? $stock_locations : $business_locations;
                    @endphp
                    @foreach($stock_locations as $location_id => $location_name)
                        <tr data-location-id="{{ $location_id }}">
                            <td>{{ $location_name }}</td>
                            <td>
                                {!! Form::hidden('opening_stock[' . $location_id . '][purchase_price]', null, ['class' => 'opening_stock_purchase_price']) !!}
                                {!! Form::text('opening_stock[' . $location_id . '][quantity]', 0, [
                                    'class' => 'form-control input-sm input_number opening_stock_qty',
                                ]); !!}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
