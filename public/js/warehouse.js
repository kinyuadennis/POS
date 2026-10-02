$(document).ready(function() {
    if ($('#warehouse_stock_table').length) {
        warehouse_stock_table = $('#warehouse_stock_table').DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 400,
            autoWidth: false,
            fixedHeader: false,
            dom: 'lfrtip',
            pageLength: 25,
            lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, LANG.all]],
            ajax: {
                url: '/warehouse/stock',
                data: function(d) {
                    d.location_id =
                        $('#warehouse_location_filter').val() ||
                        $('#warehouse_default_location_id').val();
                },
            },
            columns: [
                { data: 'product', name: 'products.name' },
                { data: 'sku', name: 'variations.sub_sku' },
                { data: 'category_name', name: 'c.name' },
                { data: 'stock', name: 'stock', orderable: false, searchable: false },
                { data: 'location_name', name: 'l.name' },
            ],
            fnDrawCallback: function() {
                __currency_convert_recursively($('#warehouse_stock_table'));
            },
        });

        var warehouse_search_timer;
        $('#custom_warehouse_stock_search').on('keyup input', function() {
            var val = $(this).val();
            clearTimeout(warehouse_search_timer);
            warehouse_search_timer = setTimeout(function() {
                warehouse_stock_table.search(val).draw();
            }, 400);
        });

        $('#custom_warehouse_stock_length').on('change', function() {
            warehouse_stock_table.page.len($(this).val()).draw();
        });

        $('#warehouse_location_filter').on('change', function() {
            warehouse_stock_table.ajax.reload();
        });

        $(document).on('click', '#warehouse_actions_toggle', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#warehouse_actions_dropdown_group').toggleClass('open');
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#warehouse_actions_dropdown_group').length) {
                $('#warehouse_actions_dropdown_group').removeClass('open');
            }
        });
    }

    if ($('#warehouse_dispatch_table').length) {
        warehouse_dispatch_table = $('#warehouse_dispatch_table').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader: false,
            ajax: '/warehouse/dispatch',
            pageLength: 25,
            lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, LANG.all]],
            dom: 'lBrtip',
            buttons: [
                {
                    extend: 'csv',
                    className: 'buttons-csv',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] },
                },
                {
                    extend: 'excel',
                    className: 'buttons-excel',
                    exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] },
                },
            ],
            columnDefs: [{
                targets: 8,
                orderable: false,
                searchable: false,
            }],
            columns: [
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'ref_no', name: 'ref_no' },
                { data: 'location_from', name: 'l1.name' },
                { data: 'location_to', name: 'l2.name' },
                { data: 'status', name: 'status' },
                { data: 'shipping_charges', name: 'shipping_charges' },
                { data: 'final_total', name: 'final_total' },
                { data: 'additional_notes', name: 'additional_notes' },
                { data: 'action', name: 'action' },
            ],
            fnDrawCallback: function() {
                __currency_convert_recursively($('#warehouse_dispatch_table'));
            },
            initComplete: function() {
                $('#warehouse_dispatch_table .skeleton-row').remove();
            },
        });

        $('#custom_warehouse_dispatch_search').on('keyup', function() {
            warehouse_dispatch_table.search(this.value).draw();
        });

        $('#custom_warehouse_dispatch_length').on('change', function() {
            warehouse_dispatch_table.page.len($(this).val()).draw();
        });

        $(document).on('click', '.warehouse-dispatch-export-opt', function(e) {
            e.preventDefault();
            warehouse_dispatch_table.button('.buttons-' + $(this).data('format')).trigger();
        });
    }

    if ($('#warehouse_adjustment_table').length) {
        warehouse_adjustment_table = $('#warehouse_adjustment_table').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader: false,
            ajax: {
                url: '/warehouse/adjustments',
                data: function(d) {
                    d.location_id = $('#warehouse_adjustment_location_filter').val();
                },
            },
            pageLength: 25,
            lengthMenu: [[25, 50, 100, 250, -1], [25, 50, 100, 250, LANG.all]],
            dom: 'lBrtip',
            buttons: [
                {
                    extend: 'csv',
                    className: 'buttons-csv',
                    exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8] },
                },
                {
                    extend: 'excel',
                    className: 'buttons-excel',
                    exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8] },
                },
            ],
            columnDefs: [{
                targets: 0,
                orderable: false,
                searchable: false,
            }],
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
            fnDrawCallback: function() {
                __currency_convert_recursively($('#warehouse_adjustment_table'));
            },
            initComplete: function() {
                $('#warehouse_adjustment_table .skeleton-row').remove();
            },
        });

        $('#warehouse_adjustment_location_filter').on('change', function() {
            warehouse_adjustment_table.ajax.reload();
        });

        $('#custom_warehouse_adjustment_search').on('keyup', function() {
            warehouse_adjustment_table.search(this.value).draw();
        });

        $('#custom_warehouse_adjustment_length').on('change', function() {
            warehouse_adjustment_table.page.len($(this).val()).draw();
        });

        $(document).on('click', '.warehouse-adjustment-export-opt', function(e) {
            e.preventDefault();
            warehouse_adjustment_table.button('.buttons-' + $(this).data('format')).trigger();
        });
    }

    $(document).on('click', 'button.delete_warehouse_stock_adjustment', function() {
        var btn = $(this);
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(function(willDelete) {
            if (willDelete) {
                $.ajax({
                    method: 'DELETE',
                    url: btn.data('href'),
                    dataType: 'json',
                    success: function(result) {
                        if (result.success) {
                            toastr.success(result.msg);
                            if (typeof warehouse_adjustment_table !== 'undefined') {
                                warehouse_adjustment_table.ajax.reload();
                            }
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            }
        });
    });

    if ($('#warehouse_settings_form').length) {
        $('#warehouse_settings_form').submit(function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success) {
                        toastr.success(result.msg);
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        });
    }

    if (window.location.hash === '#warehouse-stock') {
        var stockSection = document.getElementById('warehouse-stock');
        if (stockSection) {
            stockSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
});
