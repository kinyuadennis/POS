@extends('layouts.app')

@section('title', __( 'user.edit_user' ))

@section('content')

@php
    $theme_color = session('business.theme_color', 'blue');
    $color_map = [
        'blue' => '#3c8dbc',
        'purple' => '#605ca8',
        'green' => '#00a65a',
        'red' => '#dd4b39',
        'yellow' => '#f39c12',
        'orange' => '#ff851b',
        'sky' => '#00c0ef',
        'primary' => '#3c8dbc'
    ];
    $primary_hex = isset($color_map[$theme_color]) ? $color_map[$theme_color] : '#3c8dbc';
@endphp

<style>
    /* Content Header styling */
    .content-header {
        background-color: #ffffff !important;
        padding: 16px 24px !important;
        border-bottom: 1px solid #cbd5e160 !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
    }

    /* Page container */
    .manage-users-content {
        background-color: {{ $primary_hex }}0d !important;
        padding: 24px !important;
        min-height: calc(100vh - 100px);
    }

    /* White card section */
    .form-card {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 24px !important;
        margin-bottom: 24px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }
    .form-card-title {
        font-size: 18px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin-top: 0 !important;
        margin-bottom: 20px !important;
        padding-bottom: 12px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
    }

    /* Form controls & labels */
    .content label {
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        margin-bottom: 6px !important;
    }
    .form-control {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        height: 40px !important;
        box-shadow: none !important;
        color: #1e293b !important;
        font-size: 14px !important;
    }
    .form-control:focus {
        border-color: {{ $primary_hex }} !important;
        box-shadow: 0 0 0 3px {{ $primary_hex }}26 !important;
        outline: none !important;
    }
    .input-group-addon {
        border: 1px solid #cbd5e1 !important;
        border-radius: 0 8px 8px 0 !important;
        background-color: #f8fafc !important;
        color: #64748b !important;
    }
    .input-group .form-control {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    .select2-container--default .select2-selection--single {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        height: 40px !important;
        padding: 5px 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
        color: #1e293b !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }
    .help-block {
        font-size: 12px !important;
        color: #64748b !important;
        margin-top: 4px !important;
        margin-bottom: 0 !important;
    }

    /* Submit button styling */
    #submit_user_button {
        background-color: {{ $primary_hex }} !important;
        border-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 15px !important;
        padding: 10px 36px !important;
        height: 44px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        transition: all 0.2s ease-in-out !important;
    }
    #submit_user_button:hover {
        background-color: {{ $primary_hex }} !important;
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }

    /* Checkbox styling */
    .checkbox {
        display: inline-flex !important;
        align-items: center !important;
        margin: 6px 0 !important;
        padding: 0 !important;
        gap: 6px !important;
        min-height: auto !important;
    }
    .checkbox label {
        padding-left: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        margin: 0 !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #334155 !important;
        cursor: pointer !important;
    }
    .icheckbox_square-blue {
        border-radius: 4px !important;
        border: 1px solid #cbd5e1 !important;
        transition: all 0.2s ease !important;
        background-image: none !important;
        background-color: #ffffff !important;
        margin: 0 !important;
    }
    .icheckbox_square-blue.hover,
    .icheckbox_square-blue:hover {
        border-color: {{ $primary_hex }} !important;
    }
    .icheckbox_square-blue.checked {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%23ffffff'%3E%3Cpath fill-rule='evenodd' d='M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z' clip-rule='evenodd'/%3E%3C/svg%3E") !important;
        background-color: {{ $primary_hex }} !important;
        background-position: center !important;
        background-size: 80% !important;
        border: 1px solid {{ $primary_hex }} !important;
    }
    input[type="checkbox"].input-icheck {
        appearance: none !important;
        -webkit-appearance: none !important;
        width: 20px !important;
        height: 20px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 4px !important;
        outline: none !important;
        background-color: #ffffff !important;
        cursor: pointer !important;
        display: inline-block !important;
        vertical-align: middle !important;
        margin: 0 !important;
        position: static !important;
    }
    input[type="checkbox"].input-icheck:checked {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%23ffffff'%3E%3Cpath fill-rule='evenodd' d='M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z' clip-rule='evenodd'/%3E%3C/svg%3E") !important;
        background-color: {{ $primary_hex }} !important;
        background-position: center !important;
        background-size: 80% !important;
        border: 1px solid {{ $primary_hex }} !important;
    }

    @media (max-width: 767px) {
        .content-header {
            padding: 12px 16px !important;
        }
        .manage-users-content {
            padding: 12px !important;
        }
        .form-card {
            padding: 16px !important;
        }
    }
</style>

<!-- Content Header (Page header) -->
<section class="content-header tw-flex tw-justify-between tw-items-center">
    <div>
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" style="margin: 0;">@lang('user.edit_user')</h1>
        <p class="tw-text-sm md:tw-text-base tw-text-gray-700 tw-font-semibold tw-m-0">Update user credentials, role assignments, and location permissions</p>
    </div>
</section>

<!-- Main content -->
<section class="content manage-users-content">
{!! Form::open(['url' => action([\App\Http\Controllers\ManageUserController::class, 'update'], [$user->id]), 'method' => 'PUT', 'id' => 'user_edit_form' ]) !!}
    <div class="row">
        <!-- Card 1: User Information -->
        <div class="col-md-12">
            <div class="form-card">
                <h3 class="form-card-title">
                    <i class="fa fa-user" style="color: {{ $primary_hex }}; font-size: 16px;"></i>
                    @lang('lang_v1.user_info')
                </h3>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                          {!! Form::label('surname', __( 'business.prefix' ) . ':') !!}
                            {!! Form::select('surname', ['Mr' => 'Mr', 'Mrs' => 'Mrs', 'Miss' => 'Miss'], $user->surname, ['class' => 'form-control', 'placeholder' => __( 'messages.please_select' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                          {!! Form::label('first_name', __( 'business.first_name' ) . ':*') !!}
                            {!! Form::text('first_name', $user->first_name, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                          {!! Form::label('last_name', __( 'business.last_name' ) . ':') !!}
                            {!! Form::text('last_name', $user->last_name, ['class' => 'form-control', 'placeholder' => __( 'business.last_name' ) ]); !!}
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-4">
                        <div class="form-group">
                          {!! Form::label('email', __( 'business.email' ) . ':*') !!}
                            {!! Form::text('email', $user->email, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.email' ) ]); !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                          <div class="checkbox" style="margin-top: 28px !important;">
                            <label>
                                 {!! Form::checkbox('is_active', $user->status, $is_checked_checkbox, ['class' => 'input-icheck status']); !!} {{ __('lang_v1.status_for_user') }}
                            </label>
                            @show_tooltip(__('lang_v1.tooltip_enable_user_active'))
                          </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <div class="checkbox" style="margin-top: 28px !important;">
                          <label>
                               {!! Form::checkbox('is_enable_service_staff_pin', 1, $user->is_enable_service_staff_pin, ['class' => 'input-icheck status', 'id' => 'is_enable_service_staff_pin']); !!} {{ __('lang_v1.enable_service_staff_pin') }}
                          </label>
                          @show_tooltip(__('lang_v1.tooltip_is_enable_service_staff_pin'))
                        </div>
                      </div>
                    </div>
                    <div class="col-md-2 service_staff_pin_div {{ $user->is_enable_service_staff_pin == 1 ? '' : 'hide' }}">
                      <div class="form-group">
                        {!! Form::label('service_staff_pin', __( 'lang_v1.staff_pin' ) . ':') !!}
                          {!! Form::password('service_staff_pin', ['class' => 'form-control','placeholder' => __( 'lang_v1.staff_pin' ) ]); !!}
                      </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Roles and Permissions -->
        <div class="col-md-12">
            <div class="form-card">
                <h3 class="form-card-title">
                    <i class="fa fa-shield" style="color: {{ $primary_hex }}; font-size: 16px;"></i>
                    @lang('lang_v1.roles_and_permissions')
                </h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="checkbox">
                              <label>
                                {!! Form::checkbox('allow_login', 1, !empty($user->allow_login), 
                                [ 'class' => 'input-icheck', 'id' => 'allow_login']); !!} {{ __( 'lang_v1.allow_login' ) }}
                              </label>
                            </div>
                        </div>
                    </div>
                    <div class="user_auth_fields @if(empty($user->allow_login)) hide @endif">
                    @php
                      $display_username = $user->username;
                      if (!empty($username_ext) && !empty($display_username)) {
                        $display_username = preg_replace('/' . preg_quote($username_ext, '/') . '$/', '', $display_username);
                      }
                    @endphp
                    <div class="col-md-4">
                        <div class="form-group">
                          {!! Form::label('username', __( 'business.username' ) . ':') !!}
                          @if(!empty($username_ext))
                            <div class="input-group">
                              {!! Form::text('username', $display_username, ['class' => 'form-control', 'placeholder' => __( 'business.username' ), 'id' => 'username' ]); !!}
                              <span class="input-group-addon">{{$username_ext}}</span>
                            </div>
                            <p class="help-block" id="show_username"></p>
                          @else
                              {!! Form::text('username', $display_username, ['class' => 'form-control', 'placeholder' => __( 'business.username' ), 'id' => 'username' ]); !!}
                          @endif
                          <p class="help-block">@lang('lang_v1.username_help')</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                          {!! Form::label('password', __( 'business.password' ) . ':') !!}
                          <div class="input-group">
                            {!! Form::password('password', ['class' => 'form-control', 'placeholder' => __( 'business.password'), 'required' => empty($user->allow_login) ? true : false, 'id' => 'password' ]); !!}
                            <span class="input-group-addon toggle-password" style="cursor: pointer; user-select: none;" title="Show/Hide Password">
                              <i class="fa fa-eye"></i>
                            </span>
                          </div>
                          <p class="help-block">@lang('user.leave_password_blank')</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                          {!! Form::label('confirm_password', __( 'business.confirm_password' ) . ':') !!}
                          <div class="input-group">
                            {!! Form::password('confirm_password', ['class' => 'form-control', 'placeholder' => __( 'business.confirm_password' ), 'required' => empty($user->allow_login) ? true : false, 'id' => 'confirm_password' ]); !!}
                            <span class="input-group-addon toggle-password" style="cursor: pointer; user-select: none;" title="Show/Hide Password">
                              <i class="fa fa-eye"></i>
                            </span>
                          </div>
                        </div>
                    </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-6" style="margin-top: 12px;">
                        <div class="form-group">
                          {!! Form::label('role', __( 'user.role' ) . ':*') !!} @show_tooltip(__('lang_v1.admin_role_location_permission_help'))
                            {!! Form::select('role', $roles, !empty($user->roles->first()->id) ? $user->roles->first()->id : null, ['class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12" style="margin-top: 16px;">
                        <div class="form-group">
                          <label style="font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 12px; display: block;">
                            @lang( 'role.access_locations' ) @show_tooltip(__('tooltip.access_locations_permission'))
                          </label>
                          <div class="row">
                            <div class="col-md-12">
                              <div class="checkbox" style="margin-bottom: 10px !important;">
                                <label>
                                  {!! Form::checkbox('access_all_locations', 'access_all_locations', !is_array($permitted_locations) && $permitted_locations == 'all', 
                                  [ 'class' => 'input-icheck']); !!} {{ __( 'role.all_locations' ) }} 
                                </label>
                                @show_tooltip(__('tooltip.all_location_permission'))
                              </div>
                            </div>
                            @foreach($locations as $location)
                            <div class="col-md-4 col-sm-6">
                              <div class="checkbox" style="margin-bottom: 8px !important;">
                                <label>
                                  {!! Form::checkbox('location_permissions[]', 'location.' . $location->id, is_array($permitted_locations) && in_array($location->id, $permitted_locations), 
                                  [ 'class' => 'input-icheck']); !!} {{ $location->name }} @if(!empty($location->location_id))({{ $location->location_id}}) @endif
                                </label>
                              </div>
                            </div>
                            @endforeach
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($form_partials))
      @foreach($form_partials as $partial)
        {!! $partial !!}
      @endforeach
    @endif

    <div class="row" style="margin-top: 8px; margin-bottom: 30px;">
        <div class="col-md-12 text-center">
            <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white" id="submit_user_button">@lang( 'messages.update' )</button>
        </div>
    </div>
{!! Form::close() !!}
</section>
@stop

@section('javascript')
<script type="text/javascript">
  __page_leave_confirmation('#user_edit_form');
  $(document).ready(function(){
    $('#is_enable_service_staff_pin').on('ifChecked', function(event){
      $('div.service_staff_pin_div').removeClass('hide');
    });

    $('#is_enable_service_staff_pin').on('ifUnchecked', function(event){
      $('div.service_staff_pin_div').addClass('hide');
      $('#service_staff_pin').val('');
    });

    $('#allow_login').on('ifChecked', function(event){
      $('div.user_auth_fields').removeClass('hide');
    });
    $('#allow_login').on('ifUnchecked', function(event){
      $('div.user_auth_fields').addClass('hide');
    });
  });

  $('form#user_edit_form').validate({
        rules: {
            first_name: {
                required: true,
            },
            email: {
                email: true,
                remote: {
                    url: "/business/register/check-email",
                    type: "post",
                    data: {
                        email: function() {
                            return $( "#email" ).val();
                        },
                        user_id: {{$user->id}}
                    }
                }
            },
            password: {
                minlength: 5
            },
            confirm_password: {
                equalTo: "#password",
            },
            username: {
                minlength: 5,
                remote: {
                    url: "/business/register/check-username",
                    type: "post",
                    data: {
                        username: function() {
                            return $( "#username" ).val();
                        },
                        @if(!empty($username_ext))
                          username_ext: "{{$username_ext}}",
                        @endif
                        user_id: {{$user->id}}
                    }
                }
            }
        },
        messages: {
            password: {
                minlength: 'Password should be minimum 5 characters',
            },
            confirm_password: {
                equalTo: 'Should be same as password'
            },
            username: {
                remote: 'Invalid username or User already exist'
            },
            email: {
                remote: '{{ __("validation.unique", ["attribute" => __("business.email")]) }}'
            }
        }
    });

  $('#username').change( function(){
    if($('#show_username').length > 0){
      if($(this).val().trim() != ''){
        $('#show_username').html("{{__('lang_v1.your_username_will_be')}}: <b>" + $(this).val() + "{{$username_ext}}</b>");
      } else {
        $('#show_username').html('');
      }
    }
  });

  $(document).on('click', '.toggle-password', function() {
    var $input = $(this).closest('.input-group').find('input');
    var $icon = $(this).find('i');
    if ($input.attr('type') === 'password') {
      $input.attr('type', 'text');
      $icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
      $input.attr('type', 'password');
      $icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
  });
</script>
@endsection
