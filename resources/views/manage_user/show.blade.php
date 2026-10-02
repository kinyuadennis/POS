@extends('layouts.app')

@section('title', __( 'lang_v1.view_user' ))

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
    .box.box-primary {
        border-top-color: {{ $primary_hex }} !important;
    }
    .nav-tabs-custom > .nav-tabs > li.active {
        border-top-color: {{ $primary_hex }} !important;
    }
    #edit_user_button {
        background-color: {{ $primary_hex }} !important;
        border-color: {{ $primary_hex }} !important;
        color: #ffffff !important;
        transition: all 0.2s ease-in-out !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        width: 100% !important;
        text-align: center !important;
        height: 36px !important;
    }
    #edit_user_button:hover {
        background-color: {{ $primary_hex }} !important;
        opacity: 0.9 !important;
        transform: translateY(-1px) !important;
    }
    .profile-username {
        font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif !important;
        font-weight: 700 !important;
    }

    /* Prevent text overflow and overlap on user profile list items */
    .list-group-unbordered > .list-group-item {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 15px !important;
        flex-wrap: nowrap !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    .list-group-unbordered > .list-group-item a,
    .list-group-unbordered > .list-group-item span {
        max-width: 65% !important;
        word-break: break-all !important;
        white-space: normal !important;
        text-align: right !important;
    }
</style>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-4">
                <h3>@lang( 'lang_v1.view_user' )</h3>
            </div>
            <div class="col-md-4 col-xs-12 mt-15 pull-right">
                {!! Form::select('user_id', $users, $user->id , ['class' => 'form-control select2', 'id' => 'user_id']); !!}
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-3">
                <!-- Profile Image -->
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        @php
                            if(isset($user->media->display_url)) {
                                $img_src = $user->media->display_url;
                            } else {
                                $img_src = 'https://ui-avatars.com/api/?name='.$user->first_name;
                            }
                        @endphp

                        <img class="profile-user-img img-responsive img-circle" src="{{$img_src}}" alt="User profile picture">

                        <h3 class="profile-username text-center">
                            {{$user->user_full_name}}
                        </h3>

                        <p class="text-muted text-center" title="@lang('user.role')">
                            {{$user->role_name}}
                        </p>

                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>@lang( 'business.username' )</b>
                                <a>{{$user->username}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>@lang( 'business.email' )</b>
                                <a>{{$user->email}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>{{ __('lang_v1.status_for_user') }}</b>
                                @if($user->status == 'active')
                                    <span class="label label-success">
                                        @lang('business.is_active')
                                    </span>
                                @else
                                    <span class="label label-danger">
                                        @lang('lang_v1.inactive')
                                    </span>
                                @endif
                            </li>
                        </ul>
                        @can('user.update')
                            <a href="{{action([\App\Http\Controllers\ManageUserController::class, 'edit'], [$user->id])}}" class="tw-dw-btn tw-dw-btn-sm tw-text-white" id="edit_user_button" style="margin-top: 15px;">
                                <i class="glyphicon glyphicon-edit"></i>
                                @lang("messages.edit")
                            </a>
                        @endcan
                        </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <div class="col-md-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs nav-justified">
                        <li class="active">
                            <a href="#user_info_tab" data-toggle="tab" aria-expanded="true"><i class="fas fa-user" aria-hidden="true"></i> @lang( 'lang_v1.user_info')</a>
                        </li>
                        
                        <li>
                            <a href="#documents_and_notes_tab" data-toggle="tab" aria-expanded="true"><i class="fas fa-paperclip" aria-hidden="true"></i> @lang('lang_v1.documents_and_notes')</a>
                        </li>

                        <li>
                            <a href="#activities_tab" data-toggle="tab" aria-expanded="true"><i class="fas fa-pen-square" aria-hidden="true"></i> @lang('lang_v1.activities')</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="user_info_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                            <p><strong>@lang( 'lang_v1.cmmsn_percent' ): </strong> {{$user->cmmsn_percent}}%</p>
                                    </div>
                                    <div class="col-md-6">
                                        @php
                                            $selected_contacts = ''
                                        @endphp
                                        @if(count($user->contactAccess)) 
                                            @php
                                                $selected_contacts_array = [];
                                            @endphp
                                            @foreach($user->contactAccess as $contact) 
                                                @php
                                                    $selected_contacts_array[] = $contact->name; 
                                                @endphp
                                            @endforeach 
                                            @php
                                                $selected_contacts = implode(', ', $selected_contacts_array);
                                            @endphp
                                        @else 
                                            @php
                                                $selected_contacts = __('lang_v1.all'); 
                                            @endphp
                                        @endif
                                        <p>
                                            <strong>@lang( 'lang_v1.allowed_contacts' ): </strong>
                                                {{$selected_contacts}}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @include('user.show_details')
                        </div>
                        <div class="tab-pane" id="documents_and_notes_tab">
                            <!-- model id like project_id, user_id -->
                            <input type="hidden" name="notable_id" id="notable_id" value="{{$user->id}}">
                            <!-- model name like App\User -->
                            <input type="hidden" name="notable_type" id="notable_type" value="App\User">
                            <div class="document_note_body">
                            </div>
                        </div>
                        <div class="tab-pane" id="activities_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    @include('activity_log.activities')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>    
@endsection
@section('javascript')
    <!-- document & note.js -->
    @include('documents_and_notes.document_and_note_js')

    <script type="text/javascript">
        $(document).ready( function(){
            $('#user_id').change( function() {
                if ($(this).val()) {
                    window.location = "{{url('/users')}}/" + $(this).val();
                }
            });
        });
    </script>
@endsection