$(document).ready(function() {
    update_label_item_count();

    // Lazy load datepicker on focus to keep bulk table rendering instant
    $(document).on('focus', '.label-date-picker', function() {
        if (!$(this).data('datepicker')) {
            $(this).datepicker({
                autoclose: true
            });
        }
    });

    // Add single product via autocomplete
    if ($('#search_product_for_label').length > 0) {
        $('#search_product_for_label')
            .autocomplete({
                source: '/purchases/get_products?check_enable_stock=false&exclude_not_for_selling=1',
                minLength: 2,
                response: function(event, ui) {
                    if (ui.content.length == 1) {
                        ui.item = ui.content[0];
                        $(this)
                            .data('ui-autocomplete')
                            ._trigger('select', 'autocompleteselect', ui);
                        $(this).autocomplete('close');
                    } else if (ui.content.length == 0) {
                        swal(LANG.no_products_found);
                    }
                },
                select: function(event, ui) {
                    $(this).val(null);
                    get_label_product_row(ui.item.product_id, ui.item.variation_id);
                },
            })
            .autocomplete('instance')._renderItem = function(ul, item) {
            return $('<li>')
                .append('<div>' + item.text + '</div>')
                .appendTo(ul);
        };
    }

    // Toggle stock location dropdown if stock qty mode selected
    $('#bulk_qty_type').change(function() {
        if ($(this).val() === 'stock') {
            $('#bulk_location_div').fadeIn();
        } else {
            $('#bulk_location_div').fadeOut();
        }
    });

    // Bulk Add Filtered Products
    $('#btn_bulk_add_products').click(function() {
        var category_id = $('#bulk_category_id').val();
        var brand_id = $('#bulk_brand_id').val();
        var qty_type = $('#bulk_qty_type').val();
        var location_id = $('#bulk_location_id').val();

        load_bulk_products(category_id, brand_id, qty_type, location_id);
    });

    // Add ALL Catalog Products (Appends missing products seamlessly or loads initial catalog)
    $('#btn_add_all_catalog').click(function() {
        var current_count = $('table#product_table tbody tr').length;
        if (current_count === 0) {
            load_bulk_products('all', 'all', '1', null, true);
        } else {
            load_bulk_products('all', 'all', '1', null, false);
        }
    });

    // Clear Print Queue Table
    $('#btn_clear_label_table').click(function() {
        if ($('table#product_table tbody tr').length === 0) {
            return;
        }
        swal({
            title: 'Clear print queue?',
            text: 'This will remove all products currently in the label list.',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(function(willClear) {
            if (willClear) {
                $('table#product_table tbody').empty();
                update_label_item_count();
                toastr.info('Label print queue cleared.');
            }
        });
    });

    // Delete individual row
    $(document).on('click', '.remove_label_row', function() {
        $(this).closest('tr').remove();
        update_label_item_count();
    });

    $('input#is_show_price').change(function() {
        if ($(this).is(':checked')) {
            $('div#price_type_div').show();
        } else {
            $('div#price_type_div').hide();
        }
    });

    $('button#labels_preview').click(function() {
        if ($('form#preview_setting_form table#product_table tbody tr').length > 0) {
            var $form = $('form#preview_setting_form');
            $form.attr('action', base_path + '/labels/preview')
                 .attr('method', 'post')
                 .attr('target', '_blank');
            $form[0].submit();
        } else {
            swal(LANG.label_no_product_error).then(value => {
                $('#search_product_for_label').focus();
            });
        }
    });

    $(document).on('click', 'button#print_label', function() {
        window.print();
    });
});

function update_label_item_count() {
    var count = $('table#product_table tbody tr').length;
    $('#label_item_count').text(count);
}

function load_bulk_products(category_id, brand_id, qty_type, location_id, replace_all = false) {
    var row_count = replace_all ? 0 : $('table#product_table tbody tr').length;
    var existing_variation_ids = [];

    if (!replace_all) {
        $('table#product_table tbody tr').find('input[name*="[variation_id]"]').each(function() {
            existing_variation_ids.push($(this).val());
        });
    }

    $('#bulk_add_spinner').show();
    $('#btn_bulk_add_products, #btn_add_all_catalog').prop('disabled', true);

    $.ajax({
        method: 'POST',
        url: '/labels/add-bulk-products',
        dataType: 'json',
        data: {
            category_id: category_id,
            brand_id: brand_id,
            qty_type: qty_type,
            location_id: location_id,
            row_count: row_count,
            existing_variation_ids: existing_variation_ids,
        },
        success: function(result) {
            if (result.success) {
                if (replace_all) {
                    $('table#product_table tbody').html(result.html);
                } else {
                    if (result.count > 0) {
                        $('table#product_table tbody').append(result.html);
                    }
                }

                update_label_item_count();

                // Wait for the browser to complete DOM layout and paint before playing success sound / alert
                setTimeout(function() {
                    $('#bulk_add_spinner').hide();
                    $('#btn_bulk_add_products, #btn_add_all_catalog').prop('disabled', false);

                    if (replace_all) {
                        toastr.success(result.count + ' products loaded into label queue.');
                    } else {
                        if (result.count > 0) {
                            toastr.success(result.count + ' new products added to label queue.');
                        } else {
                            toastr.info('All matching products are already in the list.');
                        }
                    }
                }, 80);
            } else {
                $('#bulk_add_spinner').hide();
                $('#btn_bulk_add_products, #btn_add_all_catalog').prop('disabled', false);
                toastr.error('Failed to load products.');
            }
        },
        error: function(err) {
            $('#bulk_add_spinner').hide();
            $('#btn_bulk_add_products, #btn_add_all_catalog').prop('disabled', false);
            toastr.error('Error occurred while loading bulk products.');
        }
    });
}

function get_label_product_row(product_id, variation_id) {
    if (product_id) {
        // Check if variation already exists in the table
        var existing_row = $('table#product_table tbody tr').find('input[name*="[variation_id]"][value="' + variation_id + '"]').closest('tr');
        if (existing_row.length > 0) {
            var qty_input = existing_row.find('input[name*="[quantity]"]');
            var current_qty = parseInt(qty_input.val()) || 0;
            var new_qty = current_qty + 1;
            qty_input.val(new_qty);

            // Highlight existing row
            existing_row.css('transition', 'background-color 0.3s ease');
            existing_row.css('background-color', '#fef3c7');
            setTimeout(function() {
                existing_row.css('background-color', '');
            }, 1000);

            toastr.info('Increased label quantity to ' + new_qty);
            return;
        }

        var row_count = $('table#product_table tbody tr').length;
        $.ajax({
            method: 'GET',
            url: '/labels/add-product-row',
            dataType: 'html',
            data: { product_id: product_id, row_count: row_count, variation_id: variation_id },
            success: function(result) {
                $('table#product_table tbody').append(result);

                $('table#product_table tbody').find('.label-date-picker').each(function(){
                    $(this).datepicker({
                        autoclose: true
                    });
                });

                update_label_item_count();
            },
        });
    }
}
