$(document).ready(function() {
    if ($('#dashboard_date_filter').length == 1) {
        function isMobileHome() {
            return window.matchMedia('(max-width: 767px)').matches;
        }

        function setHomePageScrollLock(locked) {
            var $scroll = $('#scrollable-container');
            if (!$scroll.length) {
                return;
            }
            if (locked) {
                $scroll.data('home-prev-overflow', $scroll.css('overflow-y'));
                $scroll.css('overflow-y', 'hidden');
            } else {
                $scroll.css('overflow-y', $scroll.data('home-prev-overflow') || '');
                $scroll.removeData('home-prev-overflow');
            }
        }

        function fitHomeMobilePicker(picker) {
            if (!picker || !picker.container || !isMobileHome()) {
                return;
            }

            var $c = picker.container.addClass('home-daterangepicker show-calendar');
            var margin = 12;
            var vh = window.innerHeight || document.documentElement.clientHeight;
            var vw = window.innerWidth || document.documentElement.clientWidth;
            var el = $c.get(0);
            var styles = {
                position: 'fixed',
                top: margin + 'px',
                left: margin + 'px',
                right: margin + 'px',
                bottom: 'auto',
                width: 'auto',
                'min-width': '0',
                'max-width': (vw - margin * 2) + 'px',
                'max-height': (vh - margin * 2) + 'px',
                overflow: 'auto',
                'z-index': '4000'
            };
            Object.keys(styles).forEach(function (prop) {
                el.style.setProperty(prop, styles[prop], 'important');
            });
        }

        function closeHomePicker(picker) {
            setHomePageScrollLock(false);
            if (!picker || !picker.container) {
                return;
            }
            picker.container.hide();
        }

        var dashboardDateSettings = $.extend(true, {}, dateRangeSettings, {
            parentEl: 'body',
            alwaysShowCalendars: true,
            drops: 'down',
            opens: isMobileHome() ? 'center' : 'left'
        });
        dashboardDateSettings.startDate = moment();
        dashboardDateSettings.endDate = moment();
        $('#dashboard_date_filter').daterangepicker(dashboardDateSettings, function(start, end) {
            $('#dashboard_date_filter span').html(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            update_statistics(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
            if ($('#quotation_table').length && $('#dashboard_location').length) {
                quotation_datatable.ajax.reload();
            }
        });

        $('#dashboard_date_filter').on('show.daterangepicker showCalendar.daterangepicker', function(ev, picker) {
            picker.container.addClass('home-daterangepicker show-calendar');
            window.requestAnimationFrame(function() {
                fitHomeMobilePicker(picker);
            });
            if (isMobileHome()) {
                setHomePageScrollLock(true);
            }
        });

        $('#dashboard_date_filter').on('apply.daterangepicker cancel.daterangepicker hide.daterangepicker', function(ev, picker) {
            closeHomePicker(picker);
        });

        $(window).on('resize.homePicker orientationchange.homePicker', function() {
            var picker = $('#dashboard_date_filter').data('daterangepicker');
            if (picker && picker.isShowing) {
                fitHomeMobilePicker(picker);
            }
        });

        update_statistics(moment().format('YYYY-MM-DD'), moment().format('YYYY-MM-DD'));
    }

    function isDesktopKpi() {
        return window.matchMedia('(min-width: 768px)').matches;
    }

    function syncKpiA11y($card) {
        if (!$card.length) {
            return;
        }

        if (isDesktopKpi()) {
            $card.attr({
                role: 'button',
                tabindex: '0',
                'aria-expanded': $card.hasClass('is-open')
            });
        } else {
            $card.removeClass('is-open').removeAttr('role tabindex aria-expanded');
        }
    }

    function syncAllKpisA11y() {
        syncKpiA11y($('#total_sell_kpi'));
        syncKpiA11y($('#total_expense_kpi'));
    }

    syncAllKpisA11y();
    $(window).on('resize', syncAllKpisA11y);

    $('#total_sell_kpi, #total_expense_kpi').on('click', function() {
        if (!isDesktopKpi()) {
            return;
        }

        var $card = $(this);
        var isOpen = $card.toggleClass('is-open').hasClass('is-open');
        $card.attr('aria-expanded', isOpen);
    }).on('keydown', function(e) {
        if (!isDesktopKpi()) {
            return;
        }
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            $(this).trigger('click');
        }
    });

    $('#dashboard_location').change( function(e) {
        var start = $('#dashboard_date_filter')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');

        var end = $('#dashboard_date_filter')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');

        update_statistics(start, end);
    });

    //atock alert datatables
    var stock_alert_table = $('#stock_alert_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: {
            "url": '/home/product-stock-alert',
            "data": function ( d ) {
                if ($('#stock_alert_location').length > 0) {
                    d.location_id = $('#stock_alert_location').val();
                }
            }
        },
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#stock_alert_table'));
        },
        initComplete: function() {
            var $table = $('#stock_alert_table');
            var viewAllUrl = $table.data('view-all-url');
            var viewAllLabel = $table.data('view-all-label') || 'View All';

            if (!viewAllUrl) {
                return;
            }

            var $viewAllBtn = $('<a>', {
                href: viewAllUrl,
                class: 'tw-dw-btn-xs tw-dw-btn tw-dw-btn-outline tw-my-2',
                text: viewAllLabel,
            });

            var $buttonsContainer = $('#stock_alert_table_wrapper .dt-buttons');
            if ($buttonsContainer.length) {
                $buttonsContainer.append($viewAllBtn);
            } else {
                $('#stock_alert_table_wrapper').prepend(
                    $('<div class="dt-buttons btn-group tw-mb-2">').append($viewAllBtn)
                );
            }
        },
    });

    $('#stock_alert_location').change( function(){
        stock_alert_table.ajax.reload();
    });
    //payment dues datatables
    purchase_payment_dues_table = $('#purchase_payment_dues_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: {
            "url": '/home/purchase-payment-dues',
            "data": function ( d ) {
                if ($('#purchase_payment_dues_location').length > 0) {
                    d.location_id = $('#purchase_payment_dues_location').val();
                }
            }
        },
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#purchase_payment_dues_table'));
        },
    });

    $('#purchase_payment_dues_location').change( function(){
        purchase_payment_dues_table.ajax.reload();
    });

    //Sales dues datatables
    sales_payment_dues_table = $('#sales_payment_dues_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: {
            "url": '/home/sales-payment-dues',
            "data": function ( d ) {
                if ($('#sales_payment_dues_location').length > 0) {
                    d.location_id = $('#sales_payment_dues_location').val();
                }
            }
        },
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#sales_payment_dues_table'));
        },
    });

    $('#sales_payment_dues_location').change( function(){
        sales_payment_dues_table.ajax.reload();
    });

    //Stock expiry report table
    stock_expiry_alert_table = $('#stock_expiry_alert_table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        scrollY:        "75vh",
        scrollX:        true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: {
            url: '/reports/stock-expiry',
            data: function(d) {
                d.exp_date_filter = $('#stock_expiry_alert_days').val();
            },
        },
        order: [[3, 'asc']],
        columns: [
            { data: 'product', name: 'p.name' },
            { data: 'location', name: 'l.name' },
            { data: 'stock_left', name: 'stock_left' },
            { data: 'exp_date', name: 'exp_date' },
        ],
        fnDrawCallback: function(oSettings) {
            __show_date_diff_for_human($('#stock_expiry_alert_table'));
            __currency_convert_recursively($('#stock_expiry_alert_table'));
        },
    });

    if ($('#quotation_table').length) {
        quotation_datatable = $('#quotation_table').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader:false,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": '/sells/draft-dt?is_quotation=1',
                "data": function ( d ) {
                    if ($('#dashboard_location').length > 0) {
                        d.location_id = $('#dashboard_location').val();
                    }
                }
            },
            columnDefs: [ {
                "targets": 4,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'name', name: 'contacts.name'},
                { data: 'business_location', name: 'bl.name'},
                { data: 'action', name: 'action'}
            ]            
        });
    }
});

function update_statistics(start, end) {
    var location_id = '';
    if ($('#dashboard_location').length > 0) {
        location_id = $('#dashboard_location').val();
    }
    var data = { start: start, end: end, location_id: location_id };
    //get purchase details
    var loader = '<i class="fas fa-sync fa-spin fa-fw margin-bottom"></i>';
    $('.total_purchase').html(loader);
    $('.purchase_due').html(loader);
    $('.total_sell').html(loader);
    $('.total_sell_paybill, .total_sell_cash, .total_sell_till').html(loader);
    $('.invoice_due').html(loader);
    $('.total_expense').html(loader);
    $('.total_expense_direct, .total_cash_banked, .total_cash_outflow').html(loader);
    $('.total_purchase_return').html(loader);
    $('.total_sell_return').html(loader);
    $('.net').html(loader);
    $.ajax({
        method: 'get',
        url: '/home/get-totals',
        dataType: 'json',
        data: data,
        success: function(data) {
            //purchase details
            $('.total_purchase').html(__currency_trans_from_en(data.total_purchase, true));
            $('.purchase_due').html(__currency_trans_from_en(data.purchase_due, true));

            //sell details
            $('.total_sell').html(__currency_trans_from_en(data.total_sell, true));
            var sell_payments = data.sell_payments || {};
            $('.total_sell_paybill').html(__currency_trans_from_en(sell_payments.paybill || 0, true));
            $('.total_sell_cash').html(__currency_trans_from_en(sell_payments.cash || 0, true));
            $('.total_sell_till').html(__currency_trans_from_en(sell_payments.till || 0, true));
            $('.invoice_due').html(__currency_trans_from_en(data.invoice_due, true));
            //expense details
            $('.total_expense').html(__currency_trans_from_en(data.total_expense, true));
            $('.total_expense_direct').html(__currency_trans_from_en(data.total_expense_direct !== undefined ? data.total_expense_direct : data.total_expense, true));
            $('.total_cash_banked').html(__currency_trans_from_en(data.total_cash_banked || 0, true));
            $('.total_cash_outflow').html(__currency_trans_from_en(data.total_cash_outflow !== undefined ? data.total_cash_outflow : ((parseFloat(data.total_expense) || 0) + (parseFloat(data.total_cash_banked) || 0)), true));
            var total_purchase_return = data.total_purchase_return - data.total_purchase_return_paid;
            $('.total_purchase_return').html(__currency_trans_from_en(total_purchase_return, true));
            var total_sell_return_due = data.total_sell_return - data.total_sell_return_paid;
            $('.total_sell_return').html(__currency_trans_from_en(total_sell_return_due, true));
            $('.total_sr').html(__currency_trans_from_en(data.total_sell_return, true));
            $('.total_srp').html(__currency_trans_from_en(data.total_sell_return_paid, true));
            $('.total_pr').html(__currency_trans_from_en(data.total_purchase_return, true));
            $('.total_prp').html(__currency_trans_from_en(data.total_purchase_return_paid, true));
            $('.net').html(__currency_trans_from_en(data.net, true));

            // assign tooltip total_sell_return 
            var lang = $('#total_srp').data('value');
            var splitlang = lang.split('-');
            
            var newContent = "<p class='mb-0 text-muted fs-10 mt-5'>" + splitlang[0] + ": <span class=''>" + __currency_trans_from_en(data.total_sell_return, true) + "</span><br>" + splitlang[1] + ": <span class=''>" + __currency_trans_from_en(data.total_sell_return_paid, true) + "</span></p>";
            $('#total_srp').attr('data-content', newContent)
            // assign tooltip total_purchase_return 
            var lang = $('#total_prp').data('value');
            var splitlang = lang.split('-');
            
            var newContent = "<p class='mb-0 text-muted fs-10 mt-5'>" + splitlang[0] + ": <span class=''>" + __currency_trans_from_en(data.total_purchase_return, true) + "</span><br>" + splitlang[1] + ": <span class=''>" + __currency_trans_from_en(data.total_purchase_return_paid, true) + "</span></p>";
            
            $('#total_prp').attr('data-content', newContent);

        },
    });
}
