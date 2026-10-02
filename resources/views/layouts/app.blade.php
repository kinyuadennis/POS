@inject('request', 'Illuminate\Http\Request')

@if (
    $request->segment(1) == 'pos' &&
        ($request->segment(2) == 'create' || $request->segment(3) == 'edit' || $request->segment(2) == 'payment'))
    @php
        $pos_layout = true;
    @endphp
@else
    @php
        $pos_layout = false;
    @endphp
@endif

@php
    $whitelist = ['127.0.0.1', '::1'];
@endphp

<!DOCTYPE html>
<html class="tw-bg-white tw-scroll-smooth" lang="{{ app()->getLocale() }}"
    dir="{{ in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr' }}">
<head>
    <!-- Tell the browser to be responsive to screen width -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"
        name="viewport">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ Session::get('business.name') }}</title>

    @include('layouts.partials.css')
    

    @include('layouts.partials.extracss')

    @yield('css')

</head>
<body
    class="tw-font-sans tw-antialiased tw-text-gray-900 tw-bg-gray-100 @if ($pos_layout) hold-transition lockscreen @else hold-transition skin-@if (!empty(session('business.theme_color'))){{ session('business.theme_color') }}@else{{ 'blue-light' }} @endif sidebar-mini @endif" >
    <div class="tw-flex tw-h-screen tw-w-screen tw-overflow-hidden thetop">
        <script type="text/javascript">
            if (localStorage.getItem("upos_sidebar_collapse") == 'true') {
                var body = document.getElementsByTagName("body")[0];
                body.className += " sidebar-collapse";
            }
        </script>
        @if (!$pos_layout && $request->segment(1) != 'customer-display')
            @include('layouts.partials.sidebar')
            <div class="sidebar-backdrop" aria-hidden="true"></div>
        @endif

        @if (in_array($_SERVER['REMOTE_ADDR'], $whitelist))
            <input type="hidden" id="__is_localhost" value="true">
        @endif

        <!-- Add currency related field-->
        <input type="hidden" id="__code" value="{{ session('currency')['code'] }}">
        <input type="hidden" id="__symbol" value="{{ session('currency')['symbol'] }}">
        <input type="hidden" id="__thousand" value="{{ session('currency')['thousand_separator'] }}">
        <input type="hidden" id="__decimal" value="{{ session('currency')['decimal_separator'] }}">
        <input type="hidden" id="__symbol_placement" value="{{ session('business.currency_symbol_placement') }}">
        <input type="hidden" id="__precision" value="{{ session('business.currency_precision', 2) }}">
        <input type="hidden" id="__quantity_precision" value="{{ session('business.quantity_precision', 2) }}">
        <!-- End of currency related field-->
        @can('view_export_buttons')
            <input type="hidden" id="view_export_buttons">
        @endcan
        @if (isMobile())
            <input type="hidden" id="__is_mobile">
        @endif
        @if (session('status'))
            @php
                $sess_status = session('status');
                $status_code = '0';
                $status_msg = '';
                if (is_array($sess_status)) {
                    $is_success = !empty($sess_status['success']) || (isset($sess_status['success']) && ($sess_status['success'] == 1 || $sess_status['success'] === true));
                    $status_code = $is_success ? '1' : '0';
                    $status_msg = $sess_status['msg'] ?? ($sess_status['message'] ?? '');
                } else {
                    $status_code = !empty($sess_status) ? '1' : '0';
                    $status_msg = (string) $sess_status;
                }
            @endphp
            <input type="hidden" id="status_span" data-status="{{ $status_code }}" data-msg="{{ $status_msg }}">
        @endif
        <main class="tw-flex tw-flex-col tw-flex-1 tw-h-screen tw-min-w-0 tw-bg-gray-100 tw-overflow-hidden">
            @if($request->segment(1) != 'customer-display' && !$pos_layout)
                @include('layouts.partials.header')
            @elseif($request->segment(1) != 'customer-display')
                @include('layouts.partials.header-pos')
            @endif
            <!-- empty div for vuejs -->
            <div id="app">
                @yield('vue')
            </div>
            <div class="tw-flex-1 tw-overflow-y-auto tw-h-screen" id="scrollable-container">
                @yield('content')
                @if (!$pos_layout)
                
                    @include('layouts.partials.footer')
                @else
                    @include('layouts.partials.footer_pos')
                @endif
            </div>
            <div class='scrolltop no-print'>
                <div class='scroll icon'><i class="fas fa-angle-up"></i></div>
            </div>

            @if (config('constants.iraqi_selling_price_adjustment'))
                <input type="hidden" id="iraqi_selling_price_adjustment">
            @endif

            <!-- This will be printed -->
            <section class="invoice print_section" id="receipt_section">
            </section>
        </main>

        @include('home.todays_profit_modal')
        <!-- /.content-wrapper -->



        <audio id="success-audio">
            <source src="{{ asset('/audio/success.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/success.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>
        <audio id="error-audio">
            <source src="{{ asset('/audio/error.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/error.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>
        <audio id="warning-audio">
            <source src="{{ asset('/audio/warning.ogg?v=' . $asset_v) }}" type="audio/ogg">
            <source src="{{ asset('/audio/warning.mp3?v=' . $asset_v) }}" type="audio/mpeg">
        </audio>

        @if (!empty($__additional_html))
            {!! $__additional_html !!}
        @endif

        @include('layouts.partials.javascripts')

        <div class="modal fade view_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

        @if (!empty($__additional_views) && is_array($__additional_views))
            @foreach ($__additional_views as $additional_view)
                @includeIf($additional_view)
            @endforeach
        @endif
    </div>
</body>
<style>
    @media print {
        @page {
            size: auto;
            margin: 0mm !important;
        }
        html, body {
            height: auto !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        #scrollable-container, .print_section, #receipt_section, .invoice, .ticket {
            overflow: visible !important;
            height: auto !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding-bottom: 0 !important;
            page-break-after: avoid !important;
            break-after: avoid !important;
        }
    }
    @media screen {
        html, body {
            height: 100vh !important;
            overflow: hidden !important;
        }
        .thetop {
            height: 100vh !important;
            width: 100vw !important;
            overflow: hidden !important;
        }
        .thetop > main {
            min-width: 0 !important;
        }
        .side-bar {
            height: 100vh !important;
            overflow-y: auto !important;
            position: sticky !important;
            top: 0 !important;
        }
        #scrollable-container {
            position: relative;
            flex: 1 1 0% !important;
            overflow-y: auto !important;
        }
    }

    .sidebar-backdrop {
        display: none;
    }

    @media screen and (max-width: 1023.98px) {
        .thetop {
            overflow-x: hidden !important;
        }
        .thetop > main {
            flex: 1 1 auto !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }
        .side-bar {
            display: flex !important;
            flex-direction: column !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: auto !important;
            bottom: 0 !important;
            height: 100vh !important;
            height: 100dvh !important;
            width: 16rem !important;
            max-width: 85vw !important;
            margin: 0 !important;
            z-index: 1050 !important;
            transform: translateX(-100%);
            transition: transform 0.25s ease;
            pointer-events: none;
            box-shadow: none;
        }
        html[dir="rtl"] .side-bar {
            left: auto !important;
            right: 0 !important;
            transform: translateX(100%);
        }
        .side-bar.small-view-side-active {
            transform: translateX(0);
            pointer-events: auto;
            box-shadow: 8px 0 24px rgba(0, 0, 0, 0.18);
        }
        html[dir="rtl"] .side-bar.small-view-side-active {
            box-shadow: -8px 0 24px rgba(0, 0, 0, 0.18);
        }
        .sidebar-backdrop {
            display: block;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            z-index: 1040;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }
        .sidebar-backdrop.is-visible {
            opacity: 1;
            pointer-events: auto;
        }
    }

    @media screen and (min-width: 1024px) {
        .side-bar {
            transform: none !important;
            pointer-events: auto !important;
        }
        .sidebar-backdrop,
        .sidebar-backdrop.is-visible {
            display: none !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
    }

    .tw-dw-btn.tw-dw-btn-xs.tw-dw-btn-outline {
        width: max-content;
        margin: 2px;
    }

    #scrollable-container{
        position:relative;
    }
</style>

</html>
