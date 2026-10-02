@extends('layouts.auth-login')
@section('title', __('lang_v1.login'))
@inject('request', 'Illuminate\Http\Request')
@section('css')
<style>
    .login-demo {
        margin-bottom: 1.25rem;
    }
    .login-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin: 0.15rem 0 0.85rem;
    }
    .login-remember {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
        font-size: 0.85rem;
        font-weight: 500;
        color: #0f172a;
        cursor: pointer;
        user-select: none;
    }
    .login-remember input[type="checkbox"] {
        width: 1.1rem;
        height: 1.1rem;
        margin: 0;
        flex-shrink: 0;
        accent-color: var(--login-theme);
        cursor: pointer;
        appearance: auto;
        -webkit-appearance: checkbox;
        opacity: 1;
        visibility: visible;
        position: static;
    }
</style>
@endsection
@section('content')
    @php
        $username = old('username');
        $password = null;
        if (config('app.env') == 'demo') {
            $username = 'admin';
            $password = '123456';

            $demo_types = [
                'all_in_one' => 'admin',
                'super_market' => 'admin',
                'pharmacy' => 'admin-pharmacy',
                'electronics' => 'admin-electronics',
                'services' => 'admin-services',
                'restaurant' => 'admin-restaurant',
                'superadmin' => 'superadmin',
                'woocommerce' => 'woocommerce_user',
                'essentials' => 'admin-essentials',
                'manufacturing' => 'manufacturer-demo',
            ];

            if (!empty($_GET['demo_type']) && array_key_exists($_GET['demo_type'], $demo_types)) {
                $username = $demo_types[$_GET['demo_type']];
            }
        }

        $theme_color = (!empty($login_business) && !empty($login_business->theme_color)) ? $login_business->theme_color : 'primary';
        $color_map = [
            'blue' => '#3c8dbc',
            'primary' => '#3c8dbc',
            'purple' => '#605ca8',
            'green' => '#00a65a',
            'red' => '#dd4b39',
            'yellow' => '#f39c12',
            'orange' => '#ff851b',
            'sky' => '#00c0ef',
        ];
        $primary_hex = $color_map[$theme_color] ?? '#3c8dbc';
        $system_name = 'IMARAPOS';
    @endphp

    <div class="login-split" style="--login-theme: {{ $primary_hex }};">
        <aside class="login-hero" aria-hidden="true">
            <img src="{{ asset('img/login-hero.png') }}" alt="" fetchpriority="high">
        </aside>

        <main class="login-panel">
            <div class="login-lang">
                @include('layouts.partials.language_btn')
            </div>

            <div class="login-card">
                @if (config('app.env') == 'demo')
                    <div class="login-demo">
                        @component('components.widget', [
                            'class' => 'box-primary',
                            'header' =>
                                '<h4 class="text-center">Demo Shops <small><i> <br/>Demos are for example purpose only, this application <u>can be used in many other similar businesses.</u></i> <br/><b>Click button to login that business</b></small></h4>',
                        ])
                            <a href="?demo_type=all_in_one" class="btn btn-app bg-olive demo-login" data-toggle="tooltip"
                                title="Showcases all feature available in the application."
                                data-admin="{{ $demo_types['all_in_one'] }}"> <i class="fas fa-star"></i> All In One</a>
                            <a href="?demo_type=pharmacy" class="btn bg-maroon btn-app demo-login" data-toggle="tooltip"
                                title="Shops with products having expiry dates." data-admin="{{ $demo_types['pharmacy'] }}"><i
                                    class="fas fa-medkit"></i>Pharmacy</a>
                            <a href="?demo_type=services" class="btn bg-orange btn-app demo-login" data-toggle="tooltip"
                                title="For all service providers like Web Development, Restaurants, Repairing, Plumber, Salons, Beauty Parlors etc."
                                data-admin="{{ $demo_types['services'] }}"><i class="fas fa-wrench"></i>Multi-Service Center</a>
                            <a href="?demo_type=electronics" class="btn bg-purple btn-app demo-login" data-toggle="tooltip"
                                title="Products having IMEI or Serial number code." data-admin="{{ $demo_types['electronics'] }}"><i
                                    class="fas fa-laptop"></i>Electronics & Mobile Shop</a>
                            <a href="?demo_type=super_market" class="btn bg-navy btn-app demo-login" data-toggle="tooltip"
                                title="Super market & Similar kind of shops." data-admin="{{ $demo_types['super_market'] }}"><i
                                    class="fas fa-shopping-cart"></i> Super Market</a>
                            <a href="?demo_type=restaurant" class="btn bg-red btn-app demo-login" data-toggle="tooltip"
                                title="Restaurants, Salons and other similar kind of shops."
                                data-admin="{{ $demo_types['restaurant'] }}"><i class="fas fa-utensils"></i> Restaurant</a>
                        @endcomponent
                    </div>
                @endif

                <div class="login-brand">
                    <div class="login-brand-lockup">
                        <h1>{{ $system_name }}</h1>
                        <p>Point of Sale &amp; Inventory</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('login') }}" id="login-form">
                    {{ csrf_field() }}
                    <div class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
                        <label class="tw-dw-form-control">
                            <div class="tw-dw-label">
                                <span
                                    class="tw-text-xs md:tw-text-sm tw-font-medium tw-text-black">@lang('lang_v1.username')</span>
                            </div>
                            <input
                                name="username" required autofocus placeholder="@lang('lang_v1.username')"
                                data-last-active-input="" id="username" type="text"
                                value="{{ $username }}" />
                            @if ($errors->has('username'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('username') }}</strong>
                                </span>
                            @endif
                        </label>
                    </div>

                    <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}" style="position: relative;">
                        <label class="tw-dw-form-control">
                            <div class="tw-dw-label">
                                <span
                                    class="tw-text-xs md:tw-text-sm tw-font-medium tw-text-black">@lang('lang_v1.password')</span>
                            </div>
                            <input
                                id="password" type="password" name="password" value="{{ $password }}" required
                                placeholder="@lang('lang_v1.password')" />
                            <button type="button" id="show_hide_icon" class="show_hide_icon"
                                style="position: absolute; top:48px;right:5px;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye tw-w-6" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                </svg>
                            </button>
                        </label>
                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="login-meta">
                        <label class="login-remember">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>@lang('lang_v1.remember_me')</span>
                        </label>
                        @if (config('app.env') != 'demo')
                            <a href="{{ route('password.request') }}"
                                class="tw-text-xs md:tw-text-sm login-link-accent"
                                tabindex="-1">@lang('lang_v1.forgot_your_password')</a>
                        @endif
                    </div>
                    @if(config('constants.enable_recaptcha'))
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="g-recaptcha" data-sitekey="{{ config('constants.google_recaptcha_key') }}"></div>
                                    @if ($errors->has('g-recaptcha-response'))
                                        <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
                                    @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    <button type="submit" class="login-submit">
                        @lang('lang_v1.login')
                    </button>
                </form>

                <div class="tw-flex tw-items-center tw-flex-col">
                    @if (!($request->segment(1) == 'business' && $request->segment(2) == 'register'))
                        @if (config('constants.allow_registration'))
                            <a href="{{ route('business.getRegister') }}@if (!empty(request()->lang)) {{ '?lang=' . request()->lang }} @endif"
                                class="tw-text-sm tw-font-medium tw-text-gray-500 hover:tw-text-gray-500 tw-mt-2">{{ __('business.not_yet_registered') }}
                                <span class="login-link-accent">{{ __('business.register_now') }}</span></a>
                        @endif
                    @endif
                    @if(config('constants.SHOW_REPAIR_STATUS_LOGIN_SCREEN') && Route::has('repair-status'))
                        <a class="tw-text-sm tw-font-medium login-link-accent tw-mt-2"
                            href="{{ action([\Modules\Repair\Http\Controllers\CustomerRepairStatusController::class, 'index']) }}">
                            @lang('repair::lang.repair_status')
                        </a>
                    @endif
                    @if(Route::has('member_scanner'))
                        <a class="tw-text-sm tw-font-medium login-link-accent tw-mt-2"
                            href="{{ action([\Modules\Gym\Http\Controllers\MemberController::class, 'member_scanner']) }}">
                            @lang('gym::lang.gym_member_profile')
                        </a>
                    @endif
                </div>
            </div>
        </main>
    </div>
@stop
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#show_hide_icon').off('click');
            $('.change_lang').click(function() {
                window.location = "{{ route('login') }}?lang=" + $(this).attr('value');
            });
            $('a.demo-login').click(function(e) {
                e.preventDefault();
                $('#username').val($(this).data('admin'));
                $('#password').val("{{ $password }}");
                $('form#login-form').submit();
            });

            $('#show_hide_icon').on('click', function(e) {
            e.preventDefault();
            const passwordInput = $('#password');

            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                $('#show_hide_icon').html('<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye-off tw-w-6" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828"/><path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87"/><path d="M3 3l18 18"/></svg>');
            }
            else if (passwordInput.attr('type') === 'text') {
                passwordInput.attr('type', 'password');
                $('#show_hide_icon').html('<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye tw-w-6" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>');
            }
        });
        })
    </script>
@endsection
