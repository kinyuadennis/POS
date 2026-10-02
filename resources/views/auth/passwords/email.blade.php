@extends('layouts.auth-login')

@section('title', __('lang_v1.reset_password'))

@section('content')
    @php
        $login_business = \App\Business::select('name', 'logo', 'theme_color')->orderBy('id')->first();
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
                <div class="login-brand">
                    <div class="login-brand-lockup">
                        <h1>{{ $system_name }}</h1>
                        <p>Point of Sale &amp; Inventory</p>
                    </div>
                </div>

                <h3 class="login-form-title">@lang('lang_v1.send_password_reset_link')</h3>

                @if (session('status') && is_string(session('status')))
                    <div class="alert alert-info" role="alert">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    {{ csrf_field() }}
                    <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                        <label class="tw-dw-form-control">
                            <div class="tw-dw-label">
                                <span class="tw-text-xs md:tw-text-sm tw-font-medium tw-text-black">@lang('Email')</span>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="@lang('lang_v1.email_address')">

                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </label>
                    </div>

                    <button type="submit" class="login-submit">
                        @lang('lang_v1.send_password_reset_link')
                    </button>
                </form>

                <div class="login-footer-link">
                    <a href="{{ route('login') }}@if (!empty(request()->lang)){{ '?lang=' . request()->lang }}@endif"
                        class="tw-text-sm tw-font-medium login-link-accent">{{ __('business.sign_in') }}</a>
                </div>
            </div>
        </main>
    </div>
@endsection
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.change_lang').click(function() {
                window.location = "{{ route('password.request') }}?lang=" + $(this).attr('value');
            });
        })
    </script>
@endsection
