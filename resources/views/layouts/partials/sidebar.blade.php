<!-- Left side column. contains the logo and sidebar -->
<aside id="app-sidebar" class="side-bar tw-relative tw-hidden tw-h-screen tw-bg-white tw-w-64 xl:tw-w-64 lg:tw-flex lg:tw-flex-col tw-shrink-0 tw-overflow-y-auto">

    <!-- sidebar: style can be found in sidebar.less -->

    <a href="{{route('home')}}"
        class="tw-flex tw-items-center tw-justify-center tw-w-full tw-border-r tw-h-15 tw-bg-@if(!empty(session('business.theme_color'))){{session('business.theme_color')}}@else{{'primary'}}@endif-800 tw-shrink-0 tw-border-primary-500/30">
        <p class="tw-text-lg tw-font-medium tw-text-white side-bar-heading tw-text-center">
            {{ Session::get('business.name') }} <span class="tw-inline-block tw-w-3 tw-h-3 tw-bg-green-400 tw-rounded-full" title="Online"></span>
        </p>
    </a>

    <!-- Sidebar Menu -->
    <div style="flex: 1 1 0%; overflow-y: auto;">
        {!! Menu::render('admin-sidebar-menu', 'adminltecustom') !!}
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer" style="padding: 14px 16px; border-top: 1px solid #e2e8f0; background-color: #ffffff; text-align: center; font-size: 11px; color: #64748b; margin-top: auto; flex-shrink: 0;">
        <p style="margin: 0; font-weight: 500; color: #475569; font-size: 11px; line-height: 1.4;">
            @include('layouts.partials.app_footer_text')
        </p>
    </div>

    <!-- /.sidebar-menu -->
    <!-- /.sidebar -->
</aside>
