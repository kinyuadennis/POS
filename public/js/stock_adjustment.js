$(document).ready(function() {
    //Add products
    if ($('#search_product_for_srock_adjustment').length > 0) {
        var $search_input = $('#search_product_for_srock_adjustment');
        var $append_to = $search_input.closest('.product-search-wrapper').length > 0 
                         ? $search_input.closest('.product-search-wrapper') 
                         : $search_input.closest('.form-group');
        
        // Ensure relative positioning on the container so the absolute dropdown anchors correctly
        $append_to.css('position', 'relative');

        //Add Product
        $search_input
            .autocomplete({
                appendTo: $append_to,
                source: function(request, response) {
                    $.getJSON(
                        '/products/list',
                        { location_id: $('#location_id').val(), term: request.term },
                        response
                    );
                },
                minLength: 2,
                response: function(event, ui) {
                    if (ui.content.length == 1) {
                        ui.item = ui.content[0];
                        if (ui.item.qty_available > 0 && ui.item.enable_stock == 1) {
                            $(this)
                                .data('ui-autocomplete')
                                ._trigger('select', 'autocompleteselect', ui);
                            $(this).autocomplete('close');
                        }
                    } else if (ui.content.length == 0) {
                        swal(LANG.no_products_found);
                    }
                },
                focus: function(event, ui) {
                    if (ui.item.qty_available <= 0) {
                        return false;
                    }
                },
                select: function(event, ui) {
                    if (ui.item.enable_stock == 1) {
                        $(this).val(null);
                        stock_adjustment_product_row(ui.item.variation_id);
                    } else {
                        alert(LANG.out_of_stock);
                    }
                },
            })
            .autocomplete('instance')._renderItem = function(ul, item) {
            if (item.enable_stock != 1) {
                return ul;
            } else {
                var string = '<div>' + item.name;
                if (item.type == 'variable') {
                    string += '-' + item.variation;
                }
                string += ' (' + item.sub_sku + ')';
                if (item.qty_available <= 0) {
                    string += ' <small class="text-danger">(Out of stock)</small>';
                }
                string += '</div>';
                return $('<li>')
                    .append(string)
                    .appendTo(ul);
            }
        };
    }

    function toggle_stock_adjustment_product_search() {
        var $search = $('#search_product_for_srock_adjustment');
        if (!$search.length) {
            return;
        }

        var hasLocation = !!$('select#location_id').val();
        $search.prop('disabled', !hasLocation);
        $('.search-hint').toggleClass('hide', hasLocation);
    }

    $(document).on('change select2:select select2:clear', 'select#location_id', function() {
        toggle_stock_adjustment_product_search();
        $('table#stock_adjustment_product_table tbody').html('');
        $('#product_row_index').val(0);
        update_table_total();
    });
    toggle_stock_adjustment_product_search();

    $(document).on('change', '.adjustment_type', function() {
        update_table_row($(this).closest('tr'));
    });

    $(document).on('change', 'input.product_quantity', function() {
        update_table_row($(this).closest('tr'));
    });
    $(document).on('change', 'input.product_unit_price', function() {
        update_table_row($(this).closest('tr'));
    });

    $(document).on('click', '.remove_product_row', function() {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                $(this)
                    .closest('tr')
                    .remove();
                update_table_total();
            }
        });
    });

    //Date picker
    $('#transaction_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });

    $('form#stock_adjustment_form').validate();

    if (!$('#stock_adjustment_table').length) {
        return;
    }

    var saExportOptions = {
        columns: [1, 2, 3, 4, 5, 6, 7, 8],
        stripHtml: true,
    };

    stock_adjustment_table = $('#stock_adjustment_table').DataTable({
        processing: true,
        serverSide: true,
        fixedHeader: false,
        scrollX: true,
        pageLength: 25,
        lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, 'All']],
        ajax: '/stock-adjustments',
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false,
            },
        ],
        aaSorting: [[1, 'desc']],
        columns: [
            { data: 'action', name: 'action' },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'ref_no', name: 'ref_no' },
            { data: 'location_name', name: 'BL.name' },
            { data: 'adjustment_type', name: 'adjustment_type' },
            { data: 'final_total', name: 'final_total' },
            { data: 'total_amount_recovered', name: 'total_amount_recovered' },
            { data: 'additional_notes', name: 'additional_notes' },
            { data: 'added_by', name: 'u.first_name' },
        ],
        buttons: [
            {
                extend: 'collection',
                text: '<i class="fa fa-share-square"></i> Export',
                className: 'btn btn-default btn-sm',
                buttons: [
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-csv"></i> CSV',
                        title: 'Stock Adjustments',
                        exportOptions: saExportOptions,
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel"></i> Excel',
                        title: 'Stock Adjustments',
                        exportOptions: saExportOptions,
                    },
                ],
            },
        ],
        dom: 'lBrtip',
        initComplete: function() {
            $('#stock_adjustment_table .skeleton-row').remove();
            this.api().columns.adjust();
        },
        fnDrawCallback: function() {
            var api = this.api();
            var $wrapper = $(api.table().container());
            __currency_convert_recursively($wrapper);
        },
    });

    $('#custom_stock_adjustment_length').on('change', function() {
        stock_adjustment_table.page.len($(this).val()).draw();
    });

    $(document).on('click', '.stock-adjustment-export-opt', function(e) {
        e.preventDefault();
        stock_adjustment_table.button('.buttons-' + $(this).data('format')).trigger();
    });

    $('#custom_stock_adjustment_search').on('keyup', function() {
        stock_adjustment_table.search(this.value).draw();
    });

    $(document).on('click', 'button.delete_stock_adjustment', function() {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(willDelete => {
            if (willDelete) {
                var href = $(this).data('href');
                $.ajax({
                    method: 'DELETE',
                    url: href,
                    dataType: 'json',
                    success: function(result) {
                        if (result.success) {
                            toastr.success(result.msg);
                            stock_adjustment_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });
});

function stock_adjustment_product_row(variation_id) {
    var row_index = parseInt($('#product_row_index').val());
    var location_id = $('select#location_id').val();
    $.ajax({
        method: 'POST',
        url: '/stock-adjustments/get_product_row',
        data: { row_index: row_index, variation_id: variation_id, location_id: location_id },
        dataType: 'html',
        success: function(result) {
            $('table#stock_adjustment_product_table tbody').append(result);
            update_table_total();
            $('#product_row_index').val(row_index + 1);
        },
    });
}

function update_table_total() {
    var table_total = 0;
    $('table#stock_adjustment_product_table tbody tr').each(function() {
        var this_total = parseFloat(__read_number($(this).find('input.product_line_total')));
        if (this_total) {
            table_total += this_total;
        }
    });
    $('input#total_amount').val(table_total);
    $('span#total_adjustment').text(__number_f(table_total));
}

function update_table_row(tr) {
    var type = tr.find('.adjustment_type').val();
    var sign = type == 'add' ? 1 : -1;
    var $qtyInput = tr.find('input.product_quantity');

    if (type == 'add') {
        $qtyInput.removeAttr('data-rule-max-value');
        if (typeof $qtyInput.rules === 'function') {
            $qtyInput.rules('remove', 'max-value');
        }
    } else {
        var qty_avail = $qtyInput.data('qty_available');
        if (qty_avail !== undefined) {
            $qtyInput.attr('data-rule-max-value', qty_avail);
        }
    }

    var quantity = parseFloat(__read_number($qtyInput));
    var unit_price = parseFloat(__read_number(tr.find('input.product_unit_price')));
    var row_total = 0;
    if (quantity && unit_price) {
        row_total = quantity * unit_price * sign;
    }
    tr.find('input.product_line_total').val(__number_f(row_total));
    update_table_total();
}

$(document).on('shown.bs.modal', '.view_modal', function() {
    __currency_convert_recursively($('.view_modal'));
});
