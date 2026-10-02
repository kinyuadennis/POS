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

                <h3 class="login-form-title">@lang('lang_v1.reset_password')</h3>

                <form method="POST" action="{{ route('password.request') }}">
                    {{ csrf_field() }}

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group has-feedback {{ $errors->has('email') ? ' has-error' : '' }}">
                        <label class="tw-dw-form-control">
                            <div class="tw-dw-label">
                                <span class="tw-text-xs md:tw-text-sm tw-font-medium tw-text-black">@lang('Email')</span>
                            </div>
                            <input id="email" type="email" name="email"
                                value="{{ $email ?? old('email') }}" required autofocus placeholder="@lang('lang_v1.email_address')">

                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </label>
                    </div>

                    <div class="form-group has-feedback {{ $errors->has('password') ? ' has-error' : '' }}">
                        <label class="tw-dw-form-control">
                            <div class="tw-dw-label">
                                <span class="tw-text-xs md:tw-text-sm tw-font-medium tw-text-black">@lang('lang_v1.password')</span>
                            </div>
                            <input id="password" type="password" name="password"
                                required placeholder="@lang('lang_v1.password')">
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </label>
                    </div>

                    <div class="form-group has-feedback {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                        <label class="tw-dw-form-control">
                            <div class="tw-dw-label">
                                <span class="tw-text-xs md:tw-text-sm tw-font-medium tw-text-black">@lang('business.confirm_password')</span>
                            </div>
                            <input id="password" type="password"
                                name="password_confirmation" required placeholder="@lang('business.confirm_password')">
                            @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                            @endif
                        </label>
                    </div>
                    <button type="submit" class="login-submit">
                        @lang('lang_v1.reset_password')</button>
                </form>
            </div>
        </main>
    </div>
@endsection
@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.change_lang').click(function() {
                window.location = "{{ url()->current() }}?lang=" + $(this).attr('value');
            });
        })
    </script>
@endsection
