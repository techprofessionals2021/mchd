@extends('layouts.admin')
@php
    
    $logo = \App\Models\Utility::get_file('logo/');
    if (Auth::user()->type == 'admin') {
        $setting = App\Models\Utility::getAdminPaymentSettings();
        if ($setting['color']) {
            $color = $setting['color'];
        } else {
            $color = 'theme-3';
        }
        $dark_mode = $setting['cust_darklayout'];
        $cust_theme_bg = $setting['cust_theme_bg'];
        $SITE_RTL = env('SITE_RTL');
    } else {
        $setting = App\Models\Utility::getcompanySettings($currentWorkspace->id);
        $settings = App\Models\Utility::getcompanySettings($currentWorkspace->id);
        $color = $setting->theme_color;
        $dark_mode = $setting->cust_darklayout;
        $SITE_RTL = $setting->site_rtl;
        $cust_theme_bg = $setting->cust_theme_bg;
    }
    
    if ($color == '' || $color == null) {
        $settings = App\Models\Utility::getAdminPaymentSettings();
        $color = $settings['color'];
    }
    
    if ($dark_mode == '' || $dark_mode == null) {
        $dark_mode = $settings['cust_darklayout'];
    }
    
    if ($cust_theme_bg == '' || $dark_mode == null) {
        $cust_theme_bg = $settings['cust_theme_bg'];
    }
    
    if ($SITE_RTL == '' || $SITE_RTL == null) {
        $SITE_RTL = env('SITE_RTL');
    }
@endphp


@section('page-title', __('Settings'))
@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('Settings') }}</li>
@endsection
<style type="text/css">
    .row>* {
        flex-shrink: 0;
        /* width: 100%; */
        width: none !important;
        max-width: 100% !important;
        padding-right: calc(var(--bs-gutter-x) * .5);
        padding-left: calc(var(--bs-gutter-x) * .5);
        margin-top: var(--bs-gutter-y);
        /* width: auto; */
    }
</style>
@section('content')
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#workspace-settings"
                                class="list-group-item list-group-item-action border-0 ">{{ __('Workspace Settings') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#task-stage-settings"
                                class="list-group-item list-group-item-action border-0 ">{{ __('Task Stage Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            <a href="#bug-stage-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Bug Stage Settings') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#tax-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Tax Settings') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#company-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Company Settings') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#payment-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Payment Settings') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                            <a href="#invoice-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Invoice Settings') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                            <a href="#email-notification-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Email Notification Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>

                            <a href="#time-tracker-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Time Tracker Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                            <a href="#zoom-meeting-settings"
                                class="list-group-item list-group-item-action border-0">{{ __('Zoom Meeting Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            @if (Auth::user()->type == 'user')
                                <a href="#slack-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Slack Settings') }}<div
                                        class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                <a href="#telegram-settings"
                                    class="list-group-item list-group-item-action border-0">{{ __('Telegram Settings') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                                <a href="#google-calender"
                                   class="list-group-item list-group-item-action border-0">{{ __('Google Calender') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>

                                <a href="#webhook-settings"
                                   class="list-group-item list-group-item-action border-0">{{ __('Webhook Settings') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                                
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">

                    <div id="workspace-settings" class="">

                        {{ Form::open(['route' => ['workspace.settings.store', $currentWorkspace->slug], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                        <div class="row">
                            <div class="col-12">
                                <div class="card ">
                                    <div class="card-header">
                                        <h5>{{ __('Workspace Settings') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-4">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5>{{ __('Dark Logo') }}</h5>

                                                        </div>
                                                        <div class="card-body">
                                                            <div class="logo-content">
                                                                <img src="@if ($currentWorkspace->logo) {{ $logo . $currentWorkspace->logo  .'?timestamp='.strtotime(isset($currentWorkspace) ? $currentWorkspace->updated_at : '')}} @else{{ $logo . 'logo-light.png' }} @endif"
                                                                    class="small_logo" id="dark_logo" style="filter: drop-shadow(2px 3px 7px #011c4b);"/>
                                                            </div>
                                                            <div class="choose-file mt-5 ">
                                                                <label for="logo">

                                                                    <div class=" bg-primary" style="cursor: pointer;transform: translateY(+110%);"> <i
                                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                    </div>
                                                                    <input type="file" class="form-control choose_file_custom"
                                                                        name="logo" id="logo"
                                                                        data-filename="edit-logo">
                                                                </label>
                                                                <p class="edit-logo"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-4">
                                                    <div class="card ">
                                                        <div class="card-header">
                                                            <h5>{{ __('Light Logo') }}</h5>

                                                        </div>
                                                        <div class="card-body">
                                                            <div class="logo-content">
                                                                <img src="@if ($currentWorkspace->logo_white) {{ $logo . $currentWorkspace->logo_white .'?timestamp='.strtotime(isset($currentWorkspace) ? $currentWorkspace->updated_at : '') }} @else{{ $logo . 'logo-dark.png' }} @endif"
                                                                    id="image" class="small_logo" style="filter: drop-shadow(2px 3px 7px #011c4b);" />
                                                            </div>
                                                            <div class="choose-file mt-5 ">
                                                                <label for="logo_white">

                                                                    <div class=" bg-primary" style="cursor: pointer;transform: translateY(+110%);"> <i
                                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                    </div>
                                                                    <input type="file" class="form-control choose_file_custom"
                                                                        name="logo_white" id="logo_white"
                                                                        data-filename="edit-logo_white">
                                                                </label>
                                                                <p class="edit-logo_white"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-4">
                                                    <div class="card ">
                                                        <div class="card-header">
                                                            <h5>{{ __('Favicon') }}</h5>

                                                        </div>
                                                        <div class="card-body">
                                                            <div class="logo-content">
                                                                <img src="@if ($currentWorkspace->favicon) {{ $logo . $currentWorkspace->favicon.'?timestamp='.strtotime(isset($currentWorkspace) ? $currentWorkspace->updated_at : '') }} @else{{ $logo . 'favicon.png' }} @endif"
                                                                    id="favicon" class="favicon"
                                                                    style="width:60px !important" />
                                                            </div>
                                                            <div class="choose-file mt-5 ">
                                                                <label for="small-favicon">

                                                                    <div class=" bg-primary" style="cursor: pointer;transform: translateY(+110%);"> <i
                                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                    </div>
                                                                    <input type="file" class="form-control choose_file_custom"
                                                                        name="favicon" id="small-favicon"
                                                                        data-filename="edit-favicon">
                                                                </label>
                                                                <p class="edit-favicon"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-sm-8">
                                                <div class="form-group">
                                                    {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                                                    {{ Form::text('name', $currentWorkspace->name, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Name')]) }}
                                                    @error('name')
                                                        <span class="invalid-name" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                @php
                                                    $DEFAULT_LANG = $currentWorkspace->lang ? $currentWorkspace->lang : 'en';
                                                @endphp
                                                <div class="form-group">
                                                    {{ Form::label('default_language', __('Default Language'), ['class' => 'form-label']) }}
                                                    <div class="changeLanguage">
                                                        <select name="default_language" id="default_language"
                                                            class="form-control select2">
                                                            @foreach (\App\Models\Utility::languages() as $lang)
                                                                <option value="{{ $lang }}"
                                                                    @if ($DEFAULT_LANG == $lang) selected @endif>
                                                                    {{ ucfirst( \App\Models\Utility::getlang_fullname($lang)) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <h4 class="small-title mb-4">{{ __('Theme Customizer') }}</h4>
                                            <div class="col-12">
                                                <div class="pct-body">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <h6 class="">
                                                                <i data-feather="credit-card"
                                                                    class="me-2"></i>{{ __('Primary color settings') }}
                                                            </h6>
                                                            <hr class="my-2" />
                                                            
                                                            <div class="theme-color themes-color">
                                                                <input type="hidden" name="color" id="color_value" value="{{ $color }}">
                                                                <a href="#!" class="{{($color == 'theme-1') ? 'active_color' : ''}}" data-value="theme-1" onclick="check_theme('theme-1')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-1" style="display: none;">
                                                                <a href="#!" class="{{($color == 'theme-2') ? 'active_color' : ''}} " data-value="theme-2" onclick="check_theme('theme-2')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-2" style="display: none;">
                                                                <a href="#!" class="{{($color == 'theme-3') ? 'active_color' : ''}}" data-value="theme-3" onclick="check_theme('theme-3')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-3" style="display: none;">
                                                                <a href="#!" class="{{($color == 'theme-4') ? 'active_color' : ''}}" data-value="theme-4" onclick="check_theme('theme-4')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-4" style="display: none;">
                                                                <a href="#!" class="{{($color == 'theme-5') ? 'active_color' : ''}}" data-value="theme-5" onclick="check_theme('theme-5')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-5" style="display: none;">
                                                                <br>
                                                                <a href="#!" class="{{($color == 'theme-6') ? 'active_color' : ''}}" data-value="theme-6" onclick="check_theme('theme-6')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-6" style="display: none;">
                                                                <a href="#!" class="{{($color == 'theme-7') ? 'active_color' : ''}}" data-value="theme-7" onclick="check_theme('theme-7')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-7" style="display: none;">
                                                                <a href="#!" class="{{($color == 'theme-8') ? 'active_color' : ''}}" data-value="theme-8" onclick="check_theme('theme-8')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-8" style="display: none;">
                                                                <a href="#!" class="{{($color == 'theme-9') ? 'active_color' : ''}}" data-value="theme-9" onclick="check_theme('theme-9')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-9" style="display: none;">
                                                                <a href="#!" class="{{($color == 'theme-10') ? 'active_color' : ''}}" data-value="theme-10" onclick="check_theme('theme-10')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-10" style="display: none;">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <h6 class="">
                                                                <i data-feather="layout" class="me-2"></i>Sidebar
                                                                settings
                                                            </h6>
                                                            <hr class="my-2" />
                                                            <div class="form-check form-switch">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="cust-theme-bg" name="cust_theme_bg"
                                                                    @if ($cust_theme_bg == 'on') checked @endif />
                                                                <label class="form-check-label f-w-600 pl-1"
                                                                    for="cust-theme-bg">Transparent layout</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <h6 class="">
                                                                <i data-feather="sun" class=""></i>Layout settings
                                                            </h6>
                                                            <hr class="my-2" />
                                                            <div class="form-check form-switch mt-2">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="cust-darklayout" name="cust_darklayout"
                                                                    @if ($dark_mode == 'on') checked @endif />

                                                                <label class="form-check-label f-w-600 pl-1"
                                                                    for="cust-darklayout">Dark Layout</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="col switch-width">
                                                                <div class="form-group ml-2 mr-3 ">
                                                                    <label
                                                                        class="form-label mb-1">{{ __('Enable RTL') }}</label>
                                                                    <div class="custom-control custom-switch">
                                                                        <input type="checkbox" data-toggle="switchbutton"
                                                                            data-onstyle="primary" class=""
                                                                            name="site_rtl" id="site_rtl"
                                                                            {{ !empty($SITE_RTL) && $SITE_RTL == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="custom-control-label"
                                                                            for="site_rtl"></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end mt-2">
                                                <input type="submit" value="{{ __('Save Changes') }}"
                                                    class="btn btn-primary">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>

                    <div id="task-stage-settings" class="">
                        <div class="">
                            <div class="col-md-12">
                                <div class="card task-stages" data-value="{{ json_encode($stages) }}" style="overflow-x: auto">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-11">
                                                <h5 class="pb-2">
                                                    {{ __('Task Stage Settings') }}

                                                </h5>
                                                <small
                                                    class="">{{ __('System will consider the last stage as a completed/done project or task status.') }}</small>
                                            </div>
                                            <div class="col-auto text-end">

                                                <button data-repeater-create type="button"
                                                    class="btn-submit btn btn-sm btn-primary btn-icon "
                                                    data-toggle="tooltip" title="{{ __('Add') }}">
                                                    <i class="ti ti-plus"></i>
                                                </button>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <form method="post"
                                            action="{{ route('stages.store', $currentWorkspace->slug) }}">
                                            @csrf
                                            <table class="table table-hover" data-repeater-list="stages">
                                                <thead>
                                                    <th>
                                                        <div data-toggle="tooltip" data-placement="left"
                                                            data-title="{{ __('Drag Stage to Change Order') }}"
                                                            data-original-title="" title="">
                                                            <i class="fas fa-crosshairs"></i>
                                                        </div>
                                                    </th>
                                                    <th>{{ __('Color') }}</th>
                                                    <th>{{ __('Name') }}</th>
                                                    <th class="text-right">{{ __('Delete') }}</th>
                                                </thead>
                                                <tbody>
                                                    <tr data-repeater-item>
                                                        <td><i class="fas fa-crosshairs sort-handler"></i></td>
                                                        <td>
                                                            <input type="color" name="color">
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="id" id="id" />
                                                            <input type="text" name="name"
                                                                class="form-control mb-0" required />
                                                        </td>
                                                        <td class="text-right ">
                                                            <a data-repeater-delete
                                                                class=" action-btn btn-danger  btn btn-sm d-inline-flex align-items-center"
                                                                data-toggle="tooltip" title="{{ __('Delete') }}"><i
                                                                    class="ti ti-trash text-white"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-end pt-2">
                                                <button class="btn-submit btn btn-primary"
                                                    type="submit">{{ __('Save Changes') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="bug-stage-settings" class="tab-pane">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="card bug-stages" data-value="{{ json_encode($bugStages) }}" style="overflow-x: auto">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-11">
                                                <h5 class="pb-2">
                                                    {{ __('Bug Stage Settings') }}

                                                </h5>
                                                <small
                                                    class="">{{ __('System will consider the last stage as a completed/done project or bug status.') }}</small>
                                            </div>
                                            <div class=" col-auto text-end">
                                                <button data-repeater-create type="button"
                                                    class="btn-submit btn btn-sm btn-primary " data-toggle="tooltip"
                                                    title="{{ __('Add') }}">
                                                    <i class="ti ti-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <form method="post"
                                            action="{{ route('bug.stages.store', $currentWorkspace->slug) }}">
                                            @csrf
                                            <table class="table table-hover" data-repeater-list="stages">
                                                <thead>
                                                    <th>
                                                        <div data-toggle="tooltip" data-placement="left"
                                                            data-title="{{ __('Drag Stage to Change Order') }}"
                                                            data-original-title="" title="">
                                                            <i class="fas fa-crosshairs"></i>
                                                        </div>
                                                    </th>
                                                    <th>{{ __('Color') }}</th>
                                                    <th>{{ __('Name') }}</th>
                                                    <th class="text-right">{{ __('Delete') }}</th>
                                                </thead>
                                                <tbody>
                                                    <tr data-repeater-item>
                                                        <td><i class="fas fa-crosshairs sort-handler"></i></td>
                                                        <td>
                                                            <input type="color" name="color">
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="id" id="id" />
                                                            <input type="text" name="name"
                                                                class="form-control mb-0" required />
                                                        </td>
                                                        <td class="text-right">
                                                            <a data-repeater-delete
                                                                class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center"
                                                                data-toggle="tooltip" title="{{ __('Delete') }}"><i
                                                                    class="ti ti-trash text-white"></i></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="text-end pt-2">
                                                <button class="btn-submit btn btn-primary"
                                                    type="submit">{{ __('Save Changes') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="tax-settings" class="">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-11">
                                                <h5 class="">
                                                    {{ __('Tax Settings') }}
                                                </h5>
                                            </div>
                                            <div class="text-end  col-auto">
                                                <button class="btn-submit btn btn-sm btn-primary" type="button"
                                                    data-ajax-popup="true" data-title="{{ __('Add Tax') }}"
                                                    data-url="{{ route('tax.create', $currentWorkspace->slug) }}"
                                                    data-toggle="tooltip" title="{{ __('Add Tax') }}">
                                                    <i class="ti ti-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">

                                            <table id="" class="table table-bordered px-2">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Name') }}</th>
                                                        <th>{{ __('Rate') }}</th>
                                                        <th width="200px" class="text-right">{{ __('Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($taxes as $tax)
                                                        <tr>
                                                            <td>{{ $tax->name }}</td>
                                                            <td>{{ $tax->rate }}%</td>
                                                            <td class="text-right">
                                                                <a href="#"
                                                                    class="action-btn btn-info  btn btn-sm d-inline-flex align-items-center"
                                                                    data-ajax-popup="true"
                                                                    data-title="{{ __('Edit Tax') }}"
                                                                    data-url="{{ route('tax.edit', [$currentWorkspace->slug, $tax->id]) }}"
                                                                    data-toggle="tooltip" title="{{ __('Edit Tax') }}">
                                                                    <i class="ti ti-pencil text-white"></i>
                                                                </a>
                                                                <a href="#"
                                                                    class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                    data-confirm-yes="delete-form-{{ $tax->id }}"data-toggle="tooltip"
                                                                    title="{{ __('Delete') }}">
                                                                    <i class="ti ti-trash text-white"></i>
                                                                </a>
                                                                <form id="delete-form-{{ $tax->id }}"
                                                                    action="{{ route('tax.destroy', [$currentWorkspace->slug, $tax->id]) }}"
                                                                    method="POST" style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="company-settings" class="tab-pane">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="">
                                            {{ __('Company Settings') }}
                                        </h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <form method="post"
                                            action="{{ route('workspace.settings.store', $currentWorkspace->slug) }}"
                                            class="payment-form">
                                            @csrf
                                            <div class="row mt-3">
                                                <div class="form-group col-md-6">
                                                    <label for="company" class="form-label">{{ __('Name') }}</label>
                                                    <input type="text" name="company" id="company"
                                                        class="form-control" value="{{ $currentWorkspace->company }}"
                                                        required="required" />
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="address" class="form-label">{{ __('Address') }}</label>
                                                    <input type="text" name="address" id="address"
                                                        class="form-control" value="{{ $currentWorkspace->address }}"
                                                        required="required" />
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="city" class="form-label">{{ __('City') }}</label>
                                                    <input class="form-control" name="city" type="text"
                                                        value="{{ $currentWorkspace->city }}" id="city">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="state" class="form-label">{{ __('State') }}</label>
                                                    <input class="form-control" name="state" type="text"
                                                        value="{{ $currentWorkspace->state }}" id="state">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="zipcode"
                                                        class="form-label">{{ __('Zip/Post Code') }}</label>
                                                    <input class="form-control" name="zipcode" type="text"
                                                        value="{{ $currentWorkspace->zipcode }}" id="zipcode">
                                                </div>
                                                <div class="form-group  col-md-6">
                                                    <label for="country" class="form-label">{{ __('Country') }}</label>
                                                    <input class="form-control" name="country" type="text"
                                                        value="{{ $currentWorkspace->country }}" id="country">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="telephone"
                                                        class="form-label">{{ __('Telephone') }}</label>
                                                    <input class="form-control" name="telephone" type="text"
                                                        value="{{ $currentWorkspace->telephone }}" id="telephone">
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <button type="submit"
                                                    class="btn-submit btn btn-primary">{{ __('Save Changes') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div id="payment-settings" class="faq">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="">
                                            {{ __('Payment Settings') }}
                                        </h5>
                                        <small
                                            class="d-block mt-2">{{ __('These details will be used to collect invoice payments. Each invoice will have a payment button based on the below configuration.') }}</small>
                                    </div>
                                    <div class="card-body p-4">
                                        <form method="post"
                                            action="{{ route('workspace.settings.store', $currentWorkspace->slug) }}"
                                            class="payment-form">
                                            @csrf
                                            <div class="row mt-3">
                                                <div class="form-group col-md-6">
                                                    <label for="currency_code"
                                                        class="form-label">{{ __('Currency Code') }}</label>
                                                    <input type="text" name="currency_code" id="currency_code"
                                                        class="form-control"
                                                        value="{{ $currentWorkspace->currency_code }}"
                                                        required="required" />
                                                    <small>
                                                        {{ __('Note: Add currency code as per three-letter ISO code.') }}
                                                        <a href="https://stripe.com/docs/currencies"
                                                            target="_new">{{ __('You can find out how to do that here.') }}</a>.</small>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="currency"
                                                        class="form-label">{{ __('Currency') }}</label>
                                                    <input type="text" name="currency" id="currency"
                                                        class="form-control" value="{{ $currentWorkspace->currency }}"
                                                        required="required" />
                                                </div>

                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="accordion accordion-flush setting-accordion" id="payment-gateways">

                                                        <div class="accordion-item">
                                                            <!-- Bank Transfer -->
                                                            <h2 class="accordion-header" id="headingOne">
                                                                <button class="accordion-button collapsed" type="button" >
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapse7" aria-expanded="false" aria-controls="collapseOne">
                                                                        {{ __('Bank Transfer') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">{{__('Enable:')}}</span>
                                                                        <div class="form-check form-switch custom-switch-v1">
                                                                            <input type="hidden" name="is_bank_enabled" value="off">
                                                                            <input type="checkbox" class="form-check-input"
                                                                                name="is_bank_enabled" id="is_bank_enabled"
                                                                                {{ isset($payment_detail['is_bank_enabled']) && $payment_detail['is_bank_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            <label class="custom-control-label form-label"
                                                                                for="is_bank_enabled"></label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapse7" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div class="form-group">
                                                                        {{ Form::label('bank_details', __('Bank Details'), ['class' => 'form-label']) }}
                                                                        {{ Form::textarea('bank_details',isset($payment_detail['bank_details']) ? $payment_detail['bank_details'] : '',['class' => 'form-control', 'rows'=>'6' , 'placeholder' => __('Bank Transfer Details')]) }}
                                                                        <small class="text-muted">
                                                                            {{__('Example:bank:bank name</br> Account Number:0000 0000</br>')}}
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        
                                                        <!-- Stripe -->
                                                        <div id="" class="accordion-item">

                                                            <h2 class="accordion-header" id="stripe">
                                                                <button class="accordion-button collapsed" type="button">
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseone" aria-expanded="false"
                                                                    aria-controls="collapseone">
                                                                        {{ __('Stripe') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="checkbox"
                                                                            class="form-check-input"
                                                                            name="is_stripe_enabled"
                                                                            id="is_stripe_enabled"
                                                                            {{ isset($currentWorkspace->is_stripe_enabled) && $currentWorkspace->is_stripe_enabled == '1' ? 'checked' : '' }}>
                                                                            <label
                                                                                class="custom-control-label form-control-label"
                                                                                for="is_stripe_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapseone" class="accordion-collapse collapse"
                                                                aria-labelledby="stripe"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div class="row mt-2">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                {{ Form::label('stripe_key', __('Stripe Key'), ['class' => 'form-label']) }}
                                                                                {{ Form::text('stripe_key', isset($currentWorkspace->stripe_key) && !empty($currentWorkspace->stripe_key) ? $currentWorkspace->stripe_key : '', ['class' => 'form-control', 'placeholder' => __('Stripe Key')]) }}

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                {{ Form::label('stripe_secret', __('Stripe Secret'), ['class' => 'form-label']) }}
                                                                                {{ Form::text('stripe_secret', isset($currentWorkspace->stripe_secret) && !empty($currentWorkspace->stripe_secret) ? $currentWorkspace->stripe_secret : '', ['class' => 'form-control', 'placeholder' => __('Stripe Secret')]) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- paypal -->
                                                        <div id="" class="accordion-item">

                                                            <h2 class="accordion-header" id="paypal">
                                                                <button class="accordion-button collapsed" type="button">
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapsetwo" aria-expanded="false"
                                                                    aria-controls="collapsetwo"> 
                                                                        {{ __('Paypal') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="checkbox"
                                                                            class="form-check-input"
                                                                            name="is_paypal_enabled"
                                                                            id="is_paypal_enabled"
                                                                            {{ isset($currentWorkspace->is_paypal_enabled) && $currentWorkspace->is_paypal_enabled == '1' ? 'checked' : '' }}><label
                                                                            class="custom-control-label form-control-label"
                                                                            for="is_paypal_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapsetwo" class="accordion-collapse collapse"
                                                                aria-labelledby="paypal"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                   
                                                                    <div
                                                                        class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                                        <div class="row pt-2">
                                                                            <label class="pb-2"
                                                                                for="paypal_mode">{{ __('Paypal Mode') }}</label>
                                                                            <div class="col-lg-3">
                                                                                <div class="border accordion-header p-3">
                                                                                    <div class="form-check">
                                                                                        <input type="radio"
                                                                                            name="paypal_mode"
                                                                                            class="form-check-input input-primary "
                                                                                            value="sandbox"
                                                                                            {{ !isset($currentWorkspace->paypal_mode) || empty($currentWorkspace->paypal_mode) || $currentWorkspace->paypal_mode == 'sandbox' ? 'checked' : '' }}
                                                                                            id="">
                                                                                        <label
                                                                                            class="form-check-label d-block"
                                                                                            for="">
                                                                                            <span>
                                                                                                <span
                                                                                                    class="h5 d-block"><strong
                                                                                                        class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-3">
                                                                                <div class="border accordion-header p-3">
                                                                                    <div class="form-check">
                                                                                        <input type="radio"
                                                                                            name="paypal_mode"
                                                                                            class="form-check-input input-primary "
                                                                                            value="live"
                                                                                            {{ isset($currentWorkspace->paypal_mode) && $currentWorkspace->paypal_mode == 'live' ? 'checked' : '' }}>
                                                                                        <label
                                                                                            class="form-check-label d-block"
                                                                                            for="">
                                                                                            <span>
                                                                                                <span
                                                                                                    class="h5 d-block"><strong
                                                                                                        class="float-end"></strong>{{ __('Live') }}</span>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mt-2">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                {{ Form::label('paypal_client_id', __('Client ID'), ['class' => 'form-label']) }}
                                                                                {{ Form::text('paypal_client_id', isset($currentWorkspace->paypal_client_id) ? $currentWorkspace->paypal_client_id : '', ['class' => 'form-control', 'placeholder' => __('Client ID')]) }}

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                {{ Form::label('paypal_secret_key', __('Secret Key'), ['class' => 'form-label']) }}
                                                                                {{ Form::text('paypal_secret_key', isset($currentWorkspace->paypal_secret_key) ? $currentWorkspace->paypal_secret_key : '', ['class' => 'form-control', 'placeholder' => __('Secret Key')]) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- paystack -->
                                                        <div id="" class="accordion-item">

                                                            <h2 class="accordion-header" id="paystack">
                                                                <button class="accordion-button collapsed" type="button">
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapsethree" aria-expanded="false"
                                                                    aria-controls="collapsethree">
                                                                        {{ __('Paystack') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="checkbox"
                                                                            class="form-check-input"
                                                                            name="is_paystack_enabled"
                                                                            id="is_paystack_enabled"
                                                                            {{ isset($payment_detail['is_paystack_enabled']) && $payment_detail['is_paystack_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            <label
                                                                                class="custom-control-label form-control-label"
                                                                                for="is_paystack_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapsethree" class="accordion-collapse collapse"
                                                                aria-labelledby="paystack"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div
                                                                        class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">

                                                                    </div>
                                                                    <div class="row mt-2">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label class="form-label"
                                                                                    for="paystack_public_key">{{ __('Public Key') }}</label>
                                                                                <input type="text"
                                                                                    name="paystack_public_key"
                                                                                    id="paystack_public_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['paystack_public_key']) ? $payment_detail['paystack_public_key'] : '' }}"
                                                                                    placeholder="{{ __('Public Key') }}" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label class="form-label"
                                                                                    for="paystack_secret_key">{{ __('Secret Key') }}</label>
                                                                                <input type="text"
                                                                                    name="paystack_secret_key"
                                                                                    id="paystack_secret_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['paystack_secret_key']) ? $payment_detail['paystack_secret_key'] : '' }}"
                                                                                    placeholder="{{ __('Secret Key') }}" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Flutterwave -->
                                                        <div id="" class="accordion-item">

                                                            <h2 class="accordion-header" id="Flutterwave">
                                                                <button class="accordion-button collapsed" type="button">
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapsefor" aria-expanded="false"
                                                                    aria-controls="collapsefor">
                                                                        {{ __('Flutterwave') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="checkbox"
                                                                            class="form-check-input"
                                                                            name="is_flutterwave_enabled"
                                                                            id="is_flutterwave_enabled"
                                                                            {{ isset($payment_detail['is_flutterwave_enabled']) && $payment_detail['is_flutterwave_enabled'] == 'on' ? 'checked="checked"' : '' }}><label
                                                                            class="custom-control-label form-control-label"
                                                                            for="is_flutterwave_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapsefor" class="accordion-collapse collapse"
                                                                aria-labelledby="Flutterwave"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div
                                                                        class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">

                                                                    </div>
                                                                    <div class="row mt-2">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label class="form-label"
                                                                                    for="flutterwave_public_key">{{ __('Public Key') }}</label>
                                                                                <input type="text"
                                                                                    name="flutterwave_public_key"
                                                                                    id="flutterwave_public_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['flutterwave_public_key']) ? $payment_detail['flutterwave_public_key'] : '' }}"
                                                                                    placeholder="{{ __('Public Key') }}" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label class="form-label"
                                                                                    for="paystack_secret_key">{{ __('Secret Key') }}</label>
                                                                                <input type="text"
                                                                                    name="flutterwave_secret_key"
                                                                                    id="flutterwave_secret_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['flutterwave_secret_key']) ? $payment_detail['flutterwave_secret_key'] : '' }}"
                                                                                    placeholder="{{ __('Secret Key') }}" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Razorpay -->
                                                        <div id="" class="accordion-item">

                                                            <h2 class="accordion-header" id="Razorpay">
                                                                <button class="accordion-button collapsed" type="button">
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapsefive" aria-expanded="false"
                                                                    aria-controls="collapsefive">
                                                                        {{ __('Razorpay') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="checkbox"
                                                                            class="form-check-input"
                                                                            name="is_razorpay_enabled"
                                                                            id="is_razorpay_enabled"
                                                                            {{ isset($payment_detail['is_razorpay_enabled']) && $payment_detail['is_razorpay_enabled'] == 'on' ? 'checked="checked"' : '' }}><label
                                                                            class="custom-control-label form-control-label"
                                                                            for="is_razorpay_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapsefive" class="accordion-collapse collapse"
                                                                aria-labelledby="Razorpay"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div
                                                                        class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">

                                                                    </div>
                                                                    <div class="row mt-2">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label class="form-label"
                                                                                    for="razorpay_public_key">{{ __('Public Key') }}</label>
                                                                                <input type="text"
                                                                                    name="razorpay_public_key"
                                                                                    id="razorpay_public_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['razorpay_public_key']) ? $payment_detail['razorpay_public_key'] : '' }}"
                                                                                    placeholder="{{ __('Public Key') }}" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label class="form-label"
                                                                                    for="paystack_secret_key">{{ __('Secret Key') }}</label>
                                                                                <input type="text"
                                                                                    name="razorpay_secret_key"
                                                                                    id="razorpay_secret_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['razorpay_secret_key']) ? $payment_detail['razorpay_secret_key'] : '' }}"
                                                                                    placeholder="{{ __('Secret Key') }}" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- paypal -->
                                                        <div id="" class="accordion-item">

                                                            <h2 class="accordion-header" id="mercado">
                                                                <button class="accordion-button collapsed" type="button">
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapsetsix" aria-expanded="false"
                                                                    aria-controls="collapsetsix">
                                                                        {{ __('Mercado Pago') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="checkbox"
                                                                            class="form-check-input"
                                                                            name="is_mercado_enabled"
                                                                            id="is_mercado_enabled"
                                                                            {{ isset($payment_detail['is_mercado_enabled']) && $payment_detail['is_mercado_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            <label
                                                                                class="custom-control-label form-control-label"
                                                                                for="is_mercado_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapsetsix" class="accordion-collapse collapse"
                                                                aria-labelledby="mercado"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    
                                                                    <div
                                                                        class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                                        <div class="row pt-2">
                                                                            <label class="pb-2"
                                                                                for="paypal_mode">{{ __('Mercado Mode') }}</label>
                                                                            <div class="col-lg-3">
                                                                                <div class="border accordion-header p-3">
                                                                                    <div class="form-check">
                                                                                        <input type="radio"
                                                                                            class="form-check-input input-primary "name="mercado_mode"
                                                                                            value="sandbox"
                                                                                            {{ (isset($payment_detail['mercado_mode']) && $payment_detail['mercado_mode'] == '') || (isset($payment_detail['mercado_mode']) && $payment_detail['mercado_mode'] == 'sandbox') ? 'checked' : '' }}>


                                                                                        <label
                                                                                            class="form-check-label d-block"
                                                                                            for="">
                                                                                            <span>
                                                                                                <span
                                                                                                    class="h5 d-block"><strong
                                                                                                        class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-3">
                                                                                <div class="border accordion-header p-3">
                                                                                    <div class="form-check">
                                                                                        <input type="radio"
                                                                                            class="form-check-input input-primary "name="mercado_mode"
                                                                                            value="live"
                                                                                            {{ isset($payment_detail['mercado_mode']) && $payment_detail['mercado_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                        <label
                                                                                            class="form-check-label d-block"
                                                                                            for="">
                                                                                            <span>
                                                                                                <span
                                                                                                    class="h5 d-block"><strong
                                                                                                        class="float-end"></strong>{{ __('Live') }}</span>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mt-2">
                                                                        <div class="col-md-12">
                                                                            <label for="mercado_access_token"
                                                                                class="form-label">{{ __('Access Token') }}</label>
                                                                            <input type="text"
                                                                                name="mercado_access_token"
                                                                                id="mercado_access_token"
                                                                                class="form-control"
                                                                                value="{{ isset($payment_detail['mercado_access_token']) ? $payment_detail['mercado_access_token'] : '' }}"
                                                                                placeholder="{{ __('Access Token') }}" />
                                                                            @if ($errors->has('mercado_secret_key'))
                                                                                <span class="invalid-feedback d-block">
                                                                                    {{ $errors->first('mercado_access_token') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Paytm -->
                                                        <div id="" class="accordion-item">

                                                            <h2 class="accordion-header" id="Paytm">
                                                                <button class="accordion-button collapsed" type="button">
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse" data-bs-target="#collapset7"
                                                                    aria-expanded="false" aria-controls="collapset7">
                                                                        {{ __('Paytm') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="checkbox"
                                                                            class="form-check-input"
                                                                            name="is_paytm_enabled"
                                                                            id="is_paytm_enabled"
                                                                            {{ isset($payment_detail['is_paytm_enabled']) && $payment_detail['is_paytm_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            <label
                                                                                class="custom-control-label form-control-label"
                                                                                for="is_paytm_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapset7" class="accordion-collapse collapse"
                                                                aria-labelledby="Paytm"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    
                                                                    <div
                                                                        class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                                        <div class="row pt-2">
                                                                            <label class="pb-2"
                                                                                for="paypal_mode">{{ __('Paytm Environment') }}</label>
                                                                            <div class="col-lg-3">
                                                                                <div class="border accordion-header p-3">
                                                                                    <div class="form-check">
                                                                                        <input type="radio"
                                                                                            class="form-check-input input-primary "name="paytm_mode"
                                                                                            value="local"
                                                                                            {{ (isset($payment_detail['paytm_mode']) && $payment_detail['paytm_mode'] == '') || (isset($payment_detail['paytm_mode']) && $payment_detail['paytm_mode'] == 'local') ? 'checked="checked"' : '' }}>


                                                                                        <label
                                                                                            class="form-check-label d-block"
                                                                                            for="">
                                                                                            <span>
                                                                                                <span
                                                                                                    class="h5 d-block"><strong
                                                                                                        class="float-end"></strong>{{ __('Local') }}</span>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-3">
                                                                                <div class="border accordion-header p-3">
                                                                                    <div class="form-check">
                                                                                        <input type="radio"
                                                                                            class="form-check-input input-primary"name="paytm_mode"
                                                                                            value="production"
                                                                                            {{ isset($payment_detail['paytm_mode']) && $payment_detail['paytm_mode'] == 'production' ? 'checked="checked"' : '' }}>
                                                                                        <label
                                                                                            class="form-check-label d-block"
                                                                                            for="">
                                                                                            <span>
                                                                                                <span
                                                                                                    class="h5 d-block"><strong
                                                                                                        class="float-end"></strong>{{ __('Production') }}</span>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mt-2">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label class="form-label"
                                                                                    for="paytm_public_key">{{ __('Merchant ID') }}</label>
                                                                                <input type="text"
                                                                                    name="paytm_merchant_id"
                                                                                    id="paytm_merchant_id"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['paytm_merchant_id']) ? $payment_detail['paytm_merchant_id'] : '' }}"
                                                                                    placeholder="{{ __('Merchant ID') }}" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label class="form-label"
                                                                                    for="paytm_secret_key">{{ __('Merchant Key') }}</label>
                                                                                <input type="text"
                                                                                    name="paytm_merchant_key"
                                                                                    id="paytm_merchant_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['paytm_merchant_key']) ? $payment_detail['paytm_merchant_key'] : '' }}"
                                                                                    placeholder="{{ __('Merchant Key') }}" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label class="form-label"
                                                                                    for="paytm_industry_type">{{ __('Industry Type') }}</label>
                                                                                <input type="text"
                                                                                    name="paytm_industry_type"
                                                                                    id="paytm_industry_type"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['paytm_industry_type']) ? $payment_detail['paytm_industry_type'] : '' }}"
                                                                                    placeholder="{{ __('Industry Type') }}" />
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Mollie -->
                                                        <div id="" class="accordion-item">

                                                            <h2 class="accordion-header" id="Mollie">
                                                                <button class="accordion-button collapsed" type="button">
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse" data-bs-target="#collapset8"
                                                                    aria-expanded="false" aria-controls="collapset8">
                                                                        {{ __('Mollie') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="checkbox"
                                                                            class="form-check-input"name="is_mollie_enabled"
                                                                            id="is_mollie_enabled"
                                                                            {{ isset($payment_detail['is_mollie_enabled']) && $payment_detail['is_mollie_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            <label
                                                                                class="custom-control-label form-control-label"
                                                                                for="is_mollie_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapset8" class="accordion-collapse collapse"
                                                                aria-labelledby="Mollie"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">

                                                                    <div class="row mt-2">
                                                                        <div class="col-md-4 col-lg-4">
                                                                            <div class="form-group">
                                                                                <label class="form-label"
                                                                                    for="mollie_api_key">{{ __('Mollie Api Key') }}</label>
                                                                                <input type="text"
                                                                                    name="mollie_api_key"
                                                                                    id="mollie_api_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['mollie_api_key']) ? $payment_detail['mollie_api_key'] : '' }}"
                                                                                    placeholder="{{ __('Mollie Api Key') }}" />
                                                                            </div>
                                                                        </div>
                                                                        <div class=" col-md-4 col-lg-4">
                                                                            <div class="form-group">
                                                                                <label class="form-label"
                                                                                    for="mollie_profile_id">{{ __('Mollie Profile Id') }}</label>
                                                                                <input type="text"
                                                                                    name="mollie_profile_id"
                                                                                    id="mollie_profile_id"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['mollie_profile_id']) ? $payment_detail['mollie_profile_id'] : '' }}"
                                                                                    placeholder="{{ __('Mollie Profile Id') }}" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4 col-lg-4">
                                                                            <div class="form-group">
                                                                                <label class="form-label"
                                                                                    for="mollie_partner_id">{{ __('Mollie Partner Id') }}</label>
                                                                                <input type="text"
                                                                                    name="mollie_partner_id"
                                                                                    id="mollie_partner_id"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['mollie_partner_id']) ? $payment_detail['mollie_partner_id'] : '' }}"
                                                                                    placeholder="{{ __('Mollie Partner Id') }}" />
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Skrill -->
                                                        <div id="" class="accordion-item">

                                                            <h2 class="accordion-header" id="Skrill">
                                                                <button class="accordion-button collapsed" type="button">
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse" data-bs-target="#collapset9"
                                                                    aria-expanded="false" aria-controls="collapset9"> 
                                                                        {{ __('Skrill') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="checkbox"
                                                                            class="form-check-input"name="is_skrill_enabled"
                                                                            id="is_skrill_enabled"
                                                                            {{ isset($payment_detail['is_skrill_enabled']) && $payment_detail['is_skrill_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            <label
                                                                                class="custom-control-label form-control-label"
                                                                                for="is_skrill_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapset9" class="accordion-collapse collapse"
                                                                aria-labelledby="Skrill"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">

                                                                    <div class="row mt-2">
                                                                        <div
                                                                            class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                                            <div class="form-group">
                                                                                <label class="form-label"
                                                                                    for="mollie_api_key">{{ __('Skrill Email') }}</label>
                                                                                <input type="email" name="skrill_email"
                                                                                    id="skrill_email" class="form-control"
                                                                                    value="{{ isset($payment_detail['skrill_email']) ? $payment_detail['skrill_email'] : '' }}"
                                                                                    placeholder="{{ __('Skrill Email') }}" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- paypal -->
                                                        <div id="" class="accordion-item">

                                                            <h2 class="accordion-header" id="CoinGate">
                                                                <button class="accordion-button collapsed" type="button">
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapset10" aria-expanded="false"
                                                                    aria-controls="collapset10">
                                                                        {{ __('CoinGate') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="checkbox"
                                                                            class="form-check-input"
                                                                            name="is_coingate_enabled"
                                                                            id="is_coingate_enabled"
                                                                            {{ isset($payment_detail['is_coingate_enabled']) && $payment_detail['is_coingate_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            <label
                                                                                class="custom-control-label form-control-label"
                                                                                for="is_mercado_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapset10" class="accordion-collapse collapse"
                                                                aria-labelledby="CoinGate"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div
                                                                        class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                                        <div class="row pt-2">
                                                                            <label class="pb-2"
                                                                                for="paypal_mode">{{ __('CoinGate Mode') }}</label>
                                                                            <div class="col-lg-3">
                                                                                <div class="border accordion-header p-3">
                                                                                    <div class="form-check">
                                                                                        <input type="radio"
                                                                                            class="form-check-input input-primary "name="coingate_mode"
                                                                                            value="sandbox"
                                                                                            {{ (isset($payment_detail['coingate_mode']) && $payment_detail['coingate_mode'] == '') || (isset($payment_detail['coingate_mode']) && $payment_detail['coingate_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>


                                                                                        <label
                                                                                            class="form-check-label d-block"
                                                                                            for="">
                                                                                            <span>
                                                                                                <span
                                                                                                    class="h5 d-block"><strong
                                                                                                        class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-3">
                                                                                <div class="border accordion-header p-3">
                                                                                    <div class="form-check">
                                                                                        <input type="radio"
                                                                                            class="form-check-input input-primary name="coingate_mode"
                                                                                            value="live"
                                                                                            {{ isset($payment_detail['coingate_mode']) && $payment_detail['coingate_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                        <label
                                                                                            class="form-check-label d-block"
                                                                                            for="">
                                                                                            <span>
                                                                                                <span
                                                                                                    class="h5 d-block"><strong
                                                                                                        class="float-end"></strong>{{ __('Live') }}</span>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mt-2">
                                                                        <div
                                                                            class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                                                            <div class="form-group">
                                                                                <label class="form-label"
                                                                                    for="coingate_auth_token">{{ __('CoinGate Auth Token') }}</label>
                                                                                <input type="text"
                                                                                    name="coingate_auth_token"
                                                                                    id="coingate_auth_token"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['coingate_auth_token']) ? $payment_detail['coingate_auth_token'] : '' }}"
                                                                                    placeholder="{{ __('CoinGate Auth Token') }}" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Paymentwall -->
                                                        <div id="" class="accordion-item">

                                                            <h2 class="accordion-header" id="Paymentwall">
                                                                <button class="accordion-button collapsed" type="button">
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapse11" aria-expanded="false"
                                                                    aria-controls="collapse11">
                                                                        {{ __('Paymentwall') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="hidden"
                                                                            name="is_paymentwall_enabled"
                                                                            value="off">
                                                                            <input type="checkbox"
                                                                                class="form-check-input"
                                                                                name="is_paymentwall_enabled"
                                                                                id="is_paymentwall_enabled"
                                                                                {{ isset($payment_detail['is_paymentwall_enabled']) && $payment_detail['is_paymentwall_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            <label
                                                                                class="custom-control-label form-control-label"
                                                                                for="is_paymentwall_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapse11" class="accordion-collapse collapse"
                                                                aria-labelledby="Paymentwall"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    
                                                                    <div class="row mt-2">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="paymentwall_public_key"
                                                                                    class="form-label">{{ __('Public Key') }}</label>
                                                                                <input type="text"
                                                                                    name="paymentwall_public_key"
                                                                                    id="paymentwall_public_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['paymentwall_public_key']) ? $payment_detail['paymentwall_public_key'] : '' }}"
                                                                                    placeholder="{{ __('Public Key') }}" />
                                                                                @if ($errors->has('paymentwall_public_key'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('paymentwall_public_key') }}
                                                                                    </span>
                                                                                @endif

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="paymentwall_private_key"
                                                                                    class="form-label">{{ __('Private Key') }}</label>
                                                                                <input type="text"
                                                                                    name="paymentwall_private_key"
                                                                                    id="paymentwall_private_key"
                                                                                    class="form-control form-control-label"
                                                                                    value="{{ isset($payment_detail['paymentwall_private_key']) ? $payment_detail['paymentwall_private_key'] : '' }}"
                                                                                    placeholder="{{ __('Private Key') }}" />
                                                                                @if ($errors->has('paymentwall_private_key'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('paymentwall_private_key') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div id="" class="accordion-item ">
                                                            <!-- toyyibpay -->

                                                            <h2 class="accordion-header" id="toyyibpay">
                                                                <button class="accordion-button collapsed" type="button" >
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapse12" aria-expanded="false"
                                                                    aria-controls="collapse12">
                                                                        {{ __('Toyyibpay') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="hidden"
                                                                            name="is_toyyibpay_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input"
                                                                            name="is_toyyibpay_enabled"
                                                                            id="is_toyyibpay_enabled"
                                                                            {{ isset($payment_detail['is_toyyibpay_enabled']) && $payment_detail['is_toyyibpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label
                                                                            class="custom-control-label form-control-label"
                                                                            for="is_toyyibpay_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapse12" class="accordion-collapse collapse"
                                                                aria-labelledby="Paymentwall"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div class="row mt-2">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="toyyibpay_secret_key"
                                                                                    class="form-label">{{ __('Secret_key') }}</label>
                                                                                <input type="text"
                                                                                    name="toyyibpay_secret_key"
                                                                                    id="toyyibpay_secret_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['toyyibpay_secret_key']) ? $payment_detail['toyyibpay_secret_key'] : '' }}"
                                                                                    placeholder="{{ __('Public Key') }}" />
                                                                                @if ($errors->has('toyyibpay_secret_key'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('toyyibpay_secret_key') }}
                                                                                    </span>
                                                                                @endif

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="toyyibpay_category_code"
                                                                                    class="form-label">{{ __('Category Code') }}</label>
                                                                                <input type="text"
                                                                                    name="toyyibpay_category_code"
                                                                                    id="toyyibpay_category_code"
                                                                                    class="form-control form-control-label"
                                                                                    value="{{ isset($payment_detail['toyyibpay_category_code']) ? $payment_detail['toyyibpay_category_code'] : '' }}"
                                                                                    placeholder="{{ __('Category Code') }}" />
                                                                                @if ($errors->has('toyyibpay_category_code'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('toyyibpay_category_code') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- payfast -->
                                                        <div id="" class="accordion-item ">

                                                            <h2 class="accordion-header" id="toyyibpay">
                                                                <button class="accordion-button collapsed" type="button" >
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapse13" aria-expanded="false"
                                                                    aria-controls="collapse13">
                                                                        {{ __('Payfast') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="hidden"
                                                                            name="is_payfast_enabled"
                                                                            value="off">
                                                                        <input type="checkbox"
                                                                            class="form-check-input"
                                                                            name="is_payfast_enabled"
                                                                            id="is_payfast_enabled"
                                                                            {{ isset($payment_detail['is_payfast_enabled']) && $payment_detail['is_payfast_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label
                                                                            class="custom-control-label form-control-label"
                                                                            for="is_payfast_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapse13" class="accordion-collapse collapse"
                                                                aria-labelledby="Paymentwall"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div
                                                                        class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                                        <div class="row pt-2">
                                                                            <label class="pb-2"
                                                                                for="payfast_mode">{{ __('Payfast Mode') }}</label>
                                                                            <div class="col-lg-3">
                                                                                <div class="border p-3 accordion-header">
                                                                                    <div class="form-check">
                                                                                        <input type="radio"
                                                                                            class="form-check-input input-primary "
                                                                                            name="payfast_mode"
                                                                                            value="sandbox"
                                                                                            {{ !isset($payment_detail['payfast_mode']) || empty($payment_detail['payfast_mode']) || $payment_detail['payfast_mode'] == 'sandbox' ? 'checked' : '' }}>
                                                                                        <label
                                                                                            class="form-check-label d-block"
                                                                                            for="">
                                                                                            <span>
                                                                                                <span
                                                                                                    class="h5 d-block"><strong
                                                                                                        class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-3">
                                                                                <div class="border p-3 accordion-header">
                                                                                    <div class="form-check">
                                                                                        <input type="radio"
                                                                                            class="form-check-input input-primary "name="payfast_mode"
                                                                                            value="live"
                                                                                            {{ isset($payment_detail['payfast_mode']) && $payment_detail['payfast_mode'] == 'live' ? 'checked' : '' }}>
                                                                                        <label
                                                                                            class="form-check-label d-block"
                                                                                            for="">
                                                                                            <span>
                                                                                                <span
                                                                                                    class="h5 d-block"><strong
                                                                                                        class="float-end"></strong>{{ __('Live') }}</span>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mt-2">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="payfast_merchant_key"
                                                                                    class="form-label">{{ __('Merchant Key') }}</label>
                                                                                <input type="text"
                                                                                    name="payfast_merchant_key"
                                                                                    id="payfast_merchant_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['payfast_merchant_key']) ? $payment_detail['payfast_merchant_key'] : '' }}"
                                                                                    placeholder="{{ __('Merchant Key') }}" />
                                                                                @if ($errors->has('payfast_merchant_key'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('payfast_merchant_key') }}
                                                                                    </span>
                                                                                @endif

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="payfast_merchant_id"
                                                                                    class="form-label">{{ __('Merchant ID ') }}</label>
                                                                                <input type="text"
                                                                                    name="payfast_merchant_id"
                                                                                    id="payfast_merchant_id"
                                                                                    class="form-control form-control-label"
                                                                                    value="{{ isset($payment_detail['payfast_merchant_id']) ? $payment_detail['payfast_merchant_id'] : '' }}"
                                                                                    placeholder="{{ __('Merchant ID') }}" />
                                                                                @if ($errors->has('payfast_merchant_id'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('payfast_merchant_id') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="payfast_signature"
                                                                                    class="form-label">{{ __('Payfast Signature') }}</label>
                                                                                <input type="text"
                                                                                    name="payfast_signature"
                                                                                    id="payfast_signature"
                                                                                    class="form-control form-control-label"
                                                                                    value="{{ isset($payment_detail['payfast_signature']) ? $payment_detail['payfast_signature'] : '' }}"
                                                                                    placeholder="{{ __('Payfast Signature') }}" />
                                                                                @if ($errors->has('payfast_signature'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('payfast_signature') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- IyziPay -->
                                                        <div id="" class="accordion-item ">

                                                            <h2 class="accordion-header" id="iyzipay">
                                                                <button class="accordion-button collapsed" type="button" >
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapsetwo"
                                                                    aria-expanded="false" aria-controls="collapsetwo">
                                                                        {{ __('Iyzipay') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="checkbox"
                                                                                class="form-check-input"
                                                                                name="is_iyzipay_enabled"
                                                                                id="is_iyzipay_enabled"
                                                                                {{ isset($payment_detail['is_iyzipay_enabled']) && $payment_detail['is_iyzipay_enabled'] == 'on' ? 'checked' : '' }}><label
                                                                                class="custom-control-label form-control-label"
                                                                                for="is_iyzipay_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapsetwo" class="accordion-collapse collapse"
                                                                aria-labelledby="paypal"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div
                                                                        class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                                        <div class="row pt-2 form-group">
                                                                            <label class="pb-2"
                                                                                for="iyzipay_mode">{{ __('Iyzipay Mode') }}</label>
                                                                            <div class="col-lg-3">
                                                                                <div
                                                                                    class="border p-3 accordion-header">
                                                                                    <div class="form-check">
                                                                                        <input type="radio"
                                                                                            class="form-check-input input-primary "
                                                                                            name="iyzipay_mode"
                                                                                            value="sandbox"
                                                                                            {{ !isset($payment_detail['iyzipay_mode']) || empty($payment_detail['iyzipay_mode']) || $payment_detail['iyzipay_mode'] == 'sandbox' ? 'checked' : '' }}>
                                                                                        <label
                                                                                            class="form-check-label d-block"
                                                                                            for="">
                                                                                            <span>
                                                                                                <span
                                                                                                    class="h5 d-block"><strong
                                                                                                        class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-3">
                                                                                <div
                                                                                    class="border p-3 accordion-header">
                                                                                    <div class="form-check">
                                                                                        <input type="radio"
                                                                                            class="form-check-input input-primary "name="iyzipay_mode"
                                                                                            value="live"
                                                                                            {{ isset($payment_detail['iyzipay_mode']) && $payment_detail['iyzipay_mode'] == 'live' ? 'checked' : '' }}>
                                                                                        <label
                                                                                            class="form-check-label d-block"
                                                                                            for="">
                                                                                            <span>
                                                                                                <span
                                                                                                    class="h5 d-block"><strong
                                                                                                        class="float-end"></strong>{{ __('Live') }}</span>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mt-2">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                {{ Form::label('iyzipay_api_key', __('Api Key'), ['class' => 'form-label']) }}
                                                                                {{ Form::text('iyzipay_api_key', isset($payment_detail['iyzipay_api_key']) ? $payment_detail['iyzipay_api_key'] : '', ['class' => 'form-control', 'placeholder' => __('Api Key')]) }}

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                {{ Form::label('iyzipay_secret_key', __('Secret Key'), ['class' => 'form-label']) }}
                                                                                {{ Form::text('iyzipay_secret_key', isset($payment_detail['iyzipay_secret_key']) ? $payment_detail['iyzipay_secret_key'] : '', ['class' => 'form-control', 'placeholder' => __('Secret Key')]) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- sspay -->
                                                        <div id="" class="accordion-item ">

                                                            <h2 class="accordion-header" id="sspay">
                                                                <button class="accordion-button collapsed" type="button" >
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapse123" aria-expanded="false"
                                                                    aria-controls="collapse123">
                                                                        {{ __('SSpay') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="hidden"
                                                                                name="is_sspay_enabled"
                                                                                value="off">
                                                                            <input type="checkbox"
                                                                                class="form-check-input"
                                                                                name="is_sspay_enabled"
                                                                                id="is_sspay_enabled"
                                                                                {{ isset($payment_detail['is_sspay_enabled']) && $payment_detail['is_sspay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            <label
                                                                                class="custom-control-label form-control-label"
                                                                                for="is_sspay_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapse123" class="accordion-collapse collapse"
                                                                aria-labelledby="Paymentwall"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div class="row mt-2">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="sspay_secret_key"
                                                                                    class="form-label">{{ __('Secret_key') }}</label>
                                                                                <input type="text"
                                                                                    name="sspay_secret_key"
                                                                                    id="sspay_secret_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['sspay_secret_key']) ? $payment_detail['sspay_secret_key'] : '' }}"
                                                                                    placeholder="{{ __('Secret Key') }}" />
                                                                                @if ($errors->has('sspay_secret_key'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('sspay_secret_key') }}
                                                                                    </span>
                                                                                @endif

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="sspay_category_code"
                                                                                    class="form-label">{{ __('Category Code') }}</label>
                                                                                <input type="text"
                                                                                    name="sspay_category_code"
                                                                                    id="sspay_category_code"
                                                                                    class="form-control form-control-label"
                                                                                    value="{{ isset($payment_detail['sspay_category_code']) ? $payment_detail['sspay_category_code'] : '' }}"
                                                                                    placeholder="{{ __('Category Code') }}" />
                                                                                @if ($errors->has('sspay_category_code'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('sspay_category_code') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- paytab -->
                                                        <div id="" class="accordion-item ">
                                                            <h2 class="accordion-header" id="paytab">
                                                                <button class="accordion-button collapsed" type="button">
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapse101" aria-expanded="false"
                                                                    aria-controls="collapse101">
                                                                        {{ __('Paytab') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="hidden"
                                                                                name="is_paytab_enabled"
                                                                                value="off">
                                                                            <input type="checkbox"
                                                                                class="form-check-input"
                                                                                name="is_paytab_enabled"
                                                                                id="is_paytab_enabled"
                                                                                {{ isset($payment_detail['is_paytab_enabled']) && $payment_detail['is_paytab_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            <label
                                                                                class="custom-control-label form-control-label"
                                                                                for="is_paytab_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapse101" class="accordion-collapse collapse"
                                                                aria-labelledby="Paymentwall"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div class="row mt-2">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="paytabs_profile_id"
                                                                                    class="form-label">{{ __('Paytab Profile Id') }}</label>
                                                                                <input type="text"
                                                                                    name="paytabs_profile_id"
                                                                                    id="paytabs_profile_id"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['paytabs_profile_id']) ? $payment_detail['paytabs_profile_id'] : '' }}"
                                                                                    placeholder="{{ __('Paytabs Profile Id') }}" />
                                                                                @if ($errors->has('paytabs_profile_id'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('paytabs_profile_id') }}
                                                                                    </span>
                                                                                @endif

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="paytab_server_key"
                                                                                    class="form-label">{{ __('Paytab Server Key') }}</label>
                                                                                <input type="text"
                                                                                    name="paytab_server_key"
                                                                                    id="paytab_server_key"
                                                                                    class="form-control form-control-label"
                                                                                    value="{{ isset($payment_detail['paytab_server_key']) ? $payment_detail['paytab_server_key'] : '' }}"
                                                                                    placeholder="{{ __('Paytab Server Key') }}" />
                                                                                @if ($errors->has('paytab_server_key'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('paytab_server_key') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="paytabs_region"
                                                                                    class="form-label">{{ __('Paytab Region') }}</label>
                                                                                <input type="text"
                                                                                    name="paytabs_region"
                                                                                    id="paytabs_region"
                                                                                    class="form-control form-control-label"
                                                                                    value="{{ isset($payment_detail['paytabs_region']) ? $payment_detail['paytabs_region'] : '' }}"
                                                                                    placeholder="{{ __('Paytab Region') }}" />
                                                                                @if ($errors->has('paytabs_region'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('paytabs_region') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- benefit -->
                                                        <div id="" class="accordion-item ">
                                                            <h2 class="accordion-header" id="benefit">
                                                                <button class="accordion-button collapsed" type="button" >
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapse102" aria-expanded="false"
                                                                    aria-controls="collapse102"> 
                                                                        {{ __('Benefit') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="hidden"
                                                                                name="is_benefit_enabled"
                                                                                value="off">
                                                                            <input type="checkbox"
                                                                                class="form-check-input"
                                                                                name="is_benefit_enabled"
                                                                                id="is_benefit_enabled"
                                                                                {{ isset($payment_detail['is_benefit_enabled']) && $payment_detail['is_benefit_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            <label
                                                                                class="custom-control-label form-control-label"
                                                                                for="is_benefit_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapse102" class="accordion-collapse collapse"
                                                                aria-labelledby="Paymentwall"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div class="row mt-2">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="benefit_secret_key"
                                                                                    class="form-label">{{ __('Secret API Key') }}</label>
                                                                                <input type="text"
                                                                                    name="benefit_secret_key"
                                                                                    id="benefit_secret_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['benefit_secret_key']) ? $payment_detail['benefit_secret_key'] : '' }}"
                                                                                    placeholder="{{ __('Secret API Key') }}" />
                                                                                @if ($errors->has('benefit_secret_key'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('benefit_secret_key') }}
                                                                                    </span>
                                                                                @endif

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="benefit_publishable_key"
                                                                                    class="form-label">{{ __('Publishable API Key') }}</label>
                                                                                <input type="text"
                                                                                    name="benefit_publishable_key"
                                                                                    id="benefit_publishable_key"
                                                                                    class="form-control form-control-label"
                                                                                    value="{{ isset($payment_detail['benefit_publishable_key']) ? $payment_detail['benefit_publishable_key'] : '' }}"
                                                                                    placeholder="{{ __('Publishable API Key') }}" />
                                                                                @if ($errors->has('benefit_publishable_key'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('benefit_publishable_key') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- cashfree -->
                                                        <div id="" class="accordion-item ">
                                                            <h2 class="accordion-header" id="cashfree">
                                                                <button class="accordion-button collapsed" type="button" >
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapse103" aria-expanded="false"
                                                                    aria-controls="collapse103">
                                                                        {{ __('cashfree') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="hidden"
                                                                                name="is_cashfree_enabled"
                                                                                value="off">
                                                                            <input type="checkbox"
                                                                                class="form-check-input"
                                                                                name="is_cashfree_enabled"
                                                                                id="is_cashfree_enabled"
                                                                                {{ isset($payment_detail['is_cashfree_enabled']) && $payment_detail['is_cashfree_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            <label
                                                                                class="custom-control-label form-control-label"
                                                                                for="is_cashfree_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapse103" class="accordion-collapse collapse"
                                                                aria-labelledby="Paymentwall"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div class="row mt-2">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="cashfree_api_key"
                                                                                    class="form-label">{{ __('Cashfree Api Key') }}</label>
                                                                                <input type="text"
                                                                                    name="cashfree_api_key"
                                                                                    id="cashfree_api_key"
                                                                                    class="form-control"
                                                                                    value="{{ isset($payment_detail['cashfree_api_key']) ? $payment_detail['cashfree_api_key'] : '' }}"
                                                                                    placeholder="{{ __('Cashfree Api Key') }}" />
                                                                                @if ($errors->has('cashfree_api_key'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('cashfree_api_key') }}
                                                                                    </span>
                                                                                @endif

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="cashfree_secret_key"
                                                                                    class="form-label">{{ __('Cashfree Secret Key') }}</label>
                                                                                <input type="text"
                                                                                    name="cashfree_secret_key"
                                                                                    id="cashfree_secret_key"
                                                                                    class="form-control form-control-label"
                                                                                    value="{{ isset($payment_detail['cashfree_secret_key']) ? $payment_detail['cashfree_secret_key'] : '' }}"
                                                                                    placeholder="{{ __('Cashfree Secret Key') }}" />
                                                                                @if ($errors->has('cashfree_secret_key'))
                                                                                    <span
                                                                                        class="invalid-feedback d-block">
                                                                                        {{ $errors->first('cashfree_secret_key') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Aamarpay -->
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingTwenty-One">
                                                                <button class="accordion-button collapsed" type="button"
                                                                   >
                                                                    <span class="d-flex align-items-center"  data-bs-toggle="collapse" data-bs-target="#collapseTwenty-One"
                                                                    aria-expanded="true" aria-controls="collapseTwenty-One">
                                                                        {{ __('Aamarpay') }}
                                                                    </span>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="hidden"
                                                                                name="is_aamarpay_enabled"
                                                                                value="off">
                                                                            <input type="checkbox"
                                                                                class="form-check-input"
                                                                                name="is_aamarpay_enabled"
                                                                                id="is_aamarpay_enabled"
                                                                                {{ isset($payment_detail['is_aamarpay_enabled']) && $payment_detail['is_aamarpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            <label
                                                                                class="custom-control-label form-control-label"
                                                                                for="is_aamarpay_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapseTwenty-One" class="accordion-collapse collapse"
                                                                aria-labelledby="headingTwenty-One" data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 pb-4">
                                                                        <div class="row pt-2 form-group">
                                                                            <label class="pb-2"
                                                                                for="aamarpay_mode">{{ __('Aamarpay Mode') }}</label>
                                                                            <div class="col-lg-3">
                                                                                <div
                                                                                    class="border p-3 accordion-header">
                                                                                    <div class="form-check">
                                                                                        <input type="radio"
                                                                                            class="form-check-input input-primary "
                                                                                            name="aamarpay_mode"
                                                                                            value="sandbox"
                                                                                            {{ !isset($payment_detail['aamarpay_mode']) || empty($payment_detail['aamarpay_mode']) || $payment_detail['aamarpay_mode'] == 'sandbox' ? 'checked' : '' }}>
                                                                                        <label
                                                                                            class="form-check-label d-block"
                                                                                            for="">
                                                                                            <span>
                                                                                                <span
                                                                                                    class="h5 d-block"><strong
                                                                                                        class="float-end"></strong>{{ __('Sandbox') }}</span>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-3">
                                                                                <div
                                                                                    class="border p-3 accordion-header">
                                                                                    <div class="form-check">
                                                                                        <input type="radio"
                                                                                            class="form-check-input input-primary "name="aamarpay_mode"
                                                                                            value="live"
                                                                                            {{ isset($payment_detail['aamarpay_mode']) && $payment_detail['aamarpay_mode'] == 'live' ? 'checked' : '' }}>
                                                                                        <label
                                                                                            class="form-check-label d-block"
                                                                                            for="">
                                                                                            <span>
                                                                                                <span
                                                                                                    class="h5 d-block"><strong
                                                                                                        class="float-end"></strong>{{ __('Live') }}</span>
                                                                                            </span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row pt-2">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                {{ Form::label('aamarpay_store_id', __('Store Id'), ['class' => 'form-label']) }}
                                                                                {{ Form::text('aamarpay_store_id', isset($payment_detail['aamarpay_store_id']) ? $payment_detail['aamarpay_store_id'] : '', ['class' => 'form-control', 'placeholder' => __('Store Id')]) }}<br>
                                                                                @if ($errors->has('aamarpay_store_id'))
                                                                                    <span class="invalid-feedback d-block">
                                                                                        {{ $errors->first('aamarpay_store_id') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                {{ Form::label('aamarpay_signature_key', __('Signature Key'), ['class' => 'form-label']) }}
                                                                                {{ Form::text('aamarpay_signature_key', isset($payment_detail['aamarpay_signature_key']) ? $payment_detail['aamarpay_signature_key'] : '', ['class' => 'form-control', 'placeholder' => __('Signature Key')]) }}<br>
                                                                                @if ($errors->has('aamarpay_signature_key'))
                                                                                    <span class="invalid-feedback d-block">
                                                                                        {{ $errors->first('aamarpay_signature_key') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                {{ Form::label('aamarpay_description', __('Description'), ['class' => 'form-label']) }}
                                                                                {{ Form::text('aamarpay_description', isset($payment_detail['aamarpay_description']) ? $payment_detail['aamarpay_description'] : '', ['class' => 'form-control', 'placeholder' => __('Description')]) }}<br>
                                                                                @if ($errors->has('aamarpay_description'))
                                                                                    <span class="invalid-feedback d-block">
                                                                                        {{ $errors->first('aamarpay_description') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- PayTr -->
                                                        <div id="" class="accordion-item">
                                                            <h2 class="accordion-header" id="headingTwentyfour">
                                                                <button class="accordion-button collapsed" type="button"
                                                                    >
                                                                    <span class="d-flex align-items-center" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseTwentyfive"
                                                                    aria-expanded="true" aria-controls="collapseTwentyfive">
                                                                        {{ __('PayTR') }}
                                                                    </span>
                                                                    
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="me-2">Enable:</span>
                                                                        <div
                                                                            class="form-check form-switch custom-switch-v1">
                                                                            <input type="hidden"
                                                                                name="is_paytr_enabled"
                                                                                value="off">
                                                                            <input type="checkbox"
                                                                                class="form-check-input"
                                                                                name="is_paytr_enabled"
                                                                                id="is_paytr_enabled"
                                                                                {{ isset($payment_detail['is_paytr_enabled']) && $payment_detail['is_paytr_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                            <label
                                                                                class="custom-control-label form-control-label"
                                                                                for="is_paytr_enabled">{{ __('') }}</label>
                                                                        </div>
                                                                    </div>
                                                                </button>
                                                            </h2>
                                                            <div id="collapseTwentyfive" class="accordion-collapse collapse"
                                                                aria-labelledby="headingTwentyfour"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body">
                                                                    <div class="row pt-2">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                {{ Form::label('paytr_merchant_id', __('Merchant Id'), ['class' => 'form-label']) }}
                                                                                {{ Form::text('paytr_merchant_id', isset($payment_detail['paytr_merchant_id']) ? $payment_detail['paytr_merchant_id'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Id')]) }}<br>
                                                                                @if ($errors->has('paytr_merchant_id'))
                                                                                    <span class="invalid-feedback d-block">
                                                                                        {{ $errors->first('paytr_merchant_id') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                {{ Form::label('paytr_merchant_key', __('Merchant Key'), ['class' => 'form-label']) }}
                                                                                {{ Form::text('paytr_merchant_key', isset($payment_detail['paytr_merchant_key']) ? $payment_detail['paytr_merchant_key'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Key')]) }}<br>
                                                                                @if ($errors->has('paytr_merchant_key'))
                                                                                    <span class="invalid-feedback d-block">
                                                                                        {{ $errors->first('paytr_merchant_key') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                {{ Form::label('paytr_merchant_salt', __('Merchant Salt'), ['class' => 'form-label']) }}
                                                                                {{ Form::text('paytr_merchant_salt', isset($payment_detail['paytr_merchant_salt']) ? $payment_detail['paytr_merchant_salt'] : '', ['class' => 'form-control', 'placeholder' => __('Merchant Salt')]) }}<br>
                                                                                @if ($errors->has('paytr_merchant_salt'))
                                                                                    <span class="invalid-feedback d-block">
                                                                                        {{ $errors->first('paytr_merchant_salt') }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end py-3">
                                                <button type="submit"
                                                    class="btn-submit btn btn-primary">{{ __('Save Changes') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="invoice-settings" class="tab-pane">
                        <div class="">
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="text-start col-6">
                                                    <h5 class="">{{ __('Invoice Footer Settings') }}</h5>
                                                    <small class="d-block mt-2">{{ __('The following will be displayed in the invoice footer.') }}</small>
                                                </div>
                                                @if ($currentWorkspace->is_chagpt_enable())
                                                {{-- <div class="text-end col-6">
                                                    <a  data-size="lg" data-ajax-popup-over="true" class="btn btn-sm text-white btn-primary" data-url="{{ route('generate',['invoice footer']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate with AI') }}" data-title="{{ __('Generate Invoice Footer Title & Notes') }}">
                                                        <i class="fas fa-robot px-1"></i>{{ __('Generate with AI') }}</a>
                                                </div> --}}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-body p-4">
                                            <form method="post"
                                                action="{{ route('workspace.settings.store', $currentWorkspace->slug) }}">
                                                @csrf
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label for="invoice_footer_title"
                                                            class="form-label">{{ __('Invoice Footer Title') }}</label>
                                                        <input class="form-control" name="invoice_footer_title"
                                                            type="text"
                                                            value="{{ $currentWorkspace->invoice_footer_title }}"
                                                            id="invoice_footer_title">
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <label for="invoice_footer_notes"
                                                            class="form-label">{{ __('Invoice Footer Notes') }}</label>
                                                        <textarea rows="3" class="form-control" name="invoice_footer_notes">{{ $currentWorkspace->invoice_footer_notes }}</textarea>
                                                    </div>
                                                    <div class=" text-end">
                                                        <button type="submit" class="btn-submit btn btn-primary">
                                                            {{ __('Save Changes') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="">
                                                {{ __('Invoice Settings') }}
                                            </h5>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="">
                                                        <form
                                                            action="{{ route('workspace.settings.store', $currentWorkspace->slug) }}"
                                                            method="post">
                                                            @csrf
                                                            <div class="form-group">
                                                                <label for="address"
                                                                    class="form-label">{{ __('Invoice Template') }}</label>
                                                                <select class="form-control select2"
                                                                    name="invoice_template">
                                                                    <option value="template1"
                                                                        @if ($currentWorkspace->invoice_template == 'template1') selected @endif>
                                                                        New York</option>
                                                                    <option value="template2"
                                                                        @if ($currentWorkspace->invoice_template == 'template2') selected @endif>
                                                                        Toronto</option>
                                                                    <option value="template3"
                                                                        @if ($currentWorkspace->invoice_template == 'template3') selected @endif>
                                                                        Rio</option>
                                                                    <option value="template4"
                                                                        @if ($currentWorkspace->invoice_template == 'template4') selected @endif>
                                                                        London</option>
                                                                    <option value="template5"
                                                                        @if ($currentWorkspace->invoice_template == 'template5') selected @endif>
                                                                        Istanbul</option>
                                                                    <option value="template6"
                                                                        @if ($currentWorkspace->invoice_template == 'template6') selected @endif>
                                                                        Mumbai</option>
                                                                    <option value="template7"
                                                                        @if ($currentWorkspace->invoice_template == 'template7') selected @endif>
                                                                        Hong Kong</option>
                                                                    <option value="template8"
                                                                        @if ($currentWorkspace->invoice_template == 'template8') selected @endif>
                                                                        Tokyo</option>
                                                                    <option value="template9"
                                                                        @if ($currentWorkspace->invoice_template == 'template9') selected @endif>
                                                                        Sydney</option>
                                                                    <option value="template10"
                                                                        @if ($currentWorkspace->invoice_template == 'template10') selected @endif>
                                                                        Paris</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label
                                                                    class="form-control-label">{{ __('Color') }}</label>
                                                                <div class="row gutters-xs">
                                                                    @foreach ($colors as $key => $color)
                                                                        <div class="col-auto">
                                                                            <label class="colorinput">
                                                                                <input name="invoice_color"
                                                                                    type="radio"
                                                                                    value="{{ $color }}"
                                                                                    class="colorinput-input"
                                                                                    @if ($currentWorkspace->invoice_color == $color) checked @endif>
                                                                                <span class="colorinput-color mb-1"
                                                                                    style="background: #{{ $color }}"></span>
                                                                            </label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <div class="text-end">
                                                                <button class="btn-submit btn btn-primary"
                                                                    type="submit">
                                                                    {{ __('Save Changes') }}
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="col-sm-9">
                                                    <iframe  frameborder="0" width="100%" height="1080px"
                                                        src="{{ route('invoice.preview', [$currentWorkspace->slug, $currentWorkspace->invoice_template ? $currentWorkspace->invoice_template : 'template1', $currentWorkspace->invoice_color ? $currentWorkspace->invoice_color : 'fff']) }}">
                                                    </iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="" id="email-notification-settings">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('Email Notification Settings') }}</h5>
                            </div>
                            <div class="card-body table-border-style ">
                                <div class="col-md-12">

                                    <form method="post"
                                    action="{{ route('status.email.language', $currentWorkspace->slug) }}"
                                    class="payment-form row m-1" >
                                    @csrf
                                    @foreach ($EmailTemplates as $EmailTemplate)
                                            <div
                                                class="col-md-6  d-flex align-items-center justify-content-between list_colume_notifi">
                                                <div class="mb-3 mb-sm-0">
                                                    <h6>{{ $EmailTemplate->name }}
                                                    </h6>
                                                </div>
                                                <div class="text-end">
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input type="checkbox"
                                                                    class=" form-check-input"
                                                                    name="{{ $EmailTemplate->name }}"
                                                                    id="email_tempalte_{{ $EmailTemplate->template ? $EmailTemplate->template->id : '' }}"
                                                                    @if ($EmailTemplate->template ? $EmailTemplate->template->is_active == 1 : '') checked="checked" @endif
                                                                    type="checkbox"
                                                                    value={{ $EmailTemplate->template->id }}>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                            
                                            <div class="text-end py-3">
                                                <button type="submit"
                                                class="btn-submit btn btn-primary">{{ __('Save Changes') }}</button>
                                            </div>
                                        </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="time-tracker-settings" class="">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="">
                                            {{ __('Time Tracker Settings') }}
                                        </h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <form method="post"
                                            action="{{ route('workspace.settings.store', $currentWorkspace->slug) }}"
                                            class="payment-form">
                                            @csrf
                                            <div class="row mt-3">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label">{{ __('Application URL') }}</label><br>
                                                        <small>{{ __('Application URL to log into the app.') }}</small>
                                                        {{ Form::text('currency', URL::to('/'), ['class' => 'form-control mt-2', 'placeholder' => __('Enter Currency'), 'disabled' => 'true']) }}
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        {{ Form::label('interval_time', __('Tracking Interval'), ['class' => 'form-label']) }}<br>
                                                        <small>{{ __('Image Screenshot Take Interval time ( 1 = 1 min)') }}</small>
                                                        {{ Form::number('interval_time', $currentWorkspace->interval_time, ['class' => 'form-control mt-2', 'placeholder' => __('Enter Tracking Interval'), 'required' => 'required']) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <button type="submit"
                                                    class="btn-submit btn btn-primary">{{ __('Save Changes') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="zoom-meeting-settings" class="">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="">
                                            {{ __('Zoom Meeting Settings') }}
                                        </h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <form method="post"
                                            action="{{ route('workspace.settings.store', $currentWorkspace->slug) }}"
                                            class="payment-form">
                                            @csrf
                                            <div class="row mt-3">
                                                <div class="form-group col-md-6">
                                                    <label for="company"
                                                        class="form-label">{{ __('Zoom API Key') }}</label>
                                                    <input type="text" name="zoom_api_key" id="zoom_api_key"
                                                        class="form-control"
                                                        value="{{ $currentWorkspace->zoom_api_key }}"
                                                        required="required" />
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="address"
                                                        class="form-label">{{ __('Zoom API Secret') }}</label>
                                                    <input type="text" name="zoom_api_secret" id="zoom_api_secret"
                                                        class="form-control"
                                                        value="{{ $currentWorkspace->zoom_api_secret }}"
                                                        required="required" />
                                                </div>

                                            </div>
                                            <div class="text-end">
                                                <button type="submit"
                                                    class="btn-submit btn btn-primary">{{ __('Save Changes') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    @if (Auth::user()->type == 'user')
                        <div class="" id="slack-settings">
                            {{ Form::open(['route' => ['workspace.settings.Slack', $currentWorkspace->slug], 'method' => 'post', 'class' => 'd-contents']) }}
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="">
                                                {{ __('Slack Settings') }}
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row company-setting">
                                                <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                                    {{ Form::label('Slack Webhook URL', __('Slack Webhook URL'), ['class' => 'form-label']) }}
                                                    {{ Form::text('slack_webhook', isset($payment_detail['slack_webhook']) ? $payment_detail['slack_webhook'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Slack Webhook URL'), 'required' => 'required']) }}

                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 form-group mb-3">
                                                    {{ Form::label('Module Settings', __('Module Settings'), ['class' => 'form-label']) }}
                                                </div>


                                                <div class="col-md-4">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6> {{ Form::label('New Project', __('New Project'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('project_notificaation', '1', isset($payment_detail['project_notificaation']) && $payment_detail['project_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'project_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6> {{ Form::label('New Task', __('New Task'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('task_notificaation', '1', isset($payment_detail['task_notificaation']) && $payment_detail['task_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'task_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6>{{ Form::label('Task Stage Updated', __('Task Stage Updated'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('taskmove_notificaation', '1', isset($payment_detail['taskmove_notificaation']) && $payment_detail['taskmove_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'taskmove_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6> {{ Form::label('New Milestone', __('New Milestone'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('milestone_notificaation', '1', isset($payment_detail['milestone_notificaation']) && $payment_detail['milestone_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'milestone_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6> {{ Form::label('Milestone Status Updated', __('Milestone Status Updated'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('milestonest_notificaation', '1', isset($payment_detail['milestonest_notificaation']) && $payment_detail['milestonest_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'milestonest_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6> {{ Form::label('New Task Comment', __('New Task Comment'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('taskcom_notificaation', '1', isset($payment_detail['taskcom_notificaation']) && $payment_detail['taskcom_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'taskcom_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6>{{ Form::label('New Invoice', __('New Invoice'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('invoice_notificaation', '1', isset($payment_detail['invoice_notificaation']) && $payment_detail['invoice_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'invoice_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6> {{ Form::label('Invoice Status Updated', __('Invoice Status Updated'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('invoicest_notificaation', '1', isset($payment_detail['invoicest_notificaation']) && $payment_detail['invoicest_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'invoicest_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class=" text-end">
                                                    {{ Form::submit(__('Save Changes'), ['class' => 'btn btn-primary']) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    @endif
                    
                    @if (Auth::user()->type == 'user')
                        <div class="" id="telegram-settings">
                            {{ Form::open(['route' => ['workspace.settings.telegram', $currentWorkspace->slug], 'method' => 'post', 'class' => 'd-contents']) }}
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="">
                                                {{ __('Telegram Settings') }}
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row company-setting">
                                                <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                    {{ Form::label('Telegram Access Token', __('Telegram Access Token'), ['class' => 'form-label']) }}
                                                    {{ Form::text('telegram_token', isset($payment_detail['telegram_token']) ? $payment_detail['telegram_token'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Telegram Access Token'), 'required' => 'required']) }}

                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                    {{ Form::label('Telegram ChatID', __('Telegram ChatID'), ['class' => 'form-label']) }}
                                                    {{ Form::text('telegram_chatid', isset($payment_detail['telegram_chatid']) ? $payment_detail['telegram_chatid'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Telegram ChatID'), 'required' => 'required']) }}
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 form-group mb-3">
                                                    {{ Form::label('Module Settings', __('Module Settings'), ['class' => 'form-control-label']) }}
                                                </div>


                                                <div class="col-md-4 ">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6> {{ Form::label('New Project', __('New Project'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('telegram_project_notificaation', '1', isset($payment_detail['telegram_project_notificaation']) && $payment_detail['telegram_project_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_project_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6> {{ Form::label('New Task', __('New Task'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('telegram_task_notificaation', '1', isset($payment_detail['telegram_task_notificaation']) && $payment_detail['telegram_task_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_task_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6> {{ Form::label('Task Stage Updated', __('Task Stage Updated'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('telegram_taskmove_notificaation', '1', isset($payment_detail['telegram_taskmove_notificaation']) && $payment_detail['telegram_taskmove_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_taskmove_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6>{{ Form::label('New Milestone', __('New Milestone'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('telegram_milestone_notificaation', '1', isset($payment_detail['telegram_milestone_notificaation']) && $payment_detail['telegram_milestone_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_milestone_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6>{{ Form::label('Milestone Status Updated', __('Milestone Status Updated'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('telegram_milestonest_notificaation', '1', isset($payment_detail['telegram_milestonest_notificaation']) && $payment_detail['telegram_milestonest_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_milestonest_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6>{{ Form::label('New Task Comment', __('New Task Comment'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('telegram_taskcom_notificaation', '1', isset($payment_detail['telegram_taskcom_notificaation']) && $payment_detail['telegram_taskcom_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_taskcom_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6> {{ Form::label('New Invoice', __('New Invoice'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('telegram_invoice_notificaation', '1', isset($payment_detail['telegram_invoice_notificaation']) && $payment_detail['telegram_invoice_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_invoice_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="d-flex align-items-center justify-content-between list_colume_notifi">
                                                        <div class="mb-3 mb-sm-0">
                                                            <h6> {{ Form::label('Invoice Status Updated', __('Invoice Status Updated'), ['class' => 'form-label']) }}
                                                            </h6>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="form-check form-switch d-inline-block">
                                                                {{ Form::checkbox('telegram_invoicest_notificaation', '1', isset($payment_detail['telegram_invoicest_notificaation']) && $payment_detail['telegram_invoicest_notificaation'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_invoicest_notificaation']) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class=" text-end">
                                                    {{ Form::submit(__('Save Changes'), ['class' => 'btn btn-primary']) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    @endif

                    @if(Auth::user()->type == 'user')
                    <div class="" id="google-calender">
                        <div class="card">
                            {{ Form::open(['route' => ['google.calender.settings', $currentWorkspace->slug], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                            <div class="card-header">
                                <div class="row justify-content-between">
                                    <div class="col-10">
                                        <h5 class="">{{ __('Google Calendar') }}</h5>
                                    </div>
                                    <div class=" text-end  col-auto">
                                        <div class="col switch-width">
                                            <div class="form-group ml-2 mr-3 ">
                                                <div class="custom-control custom-switch">
                                                    
                                                    <input type="checkbox" data-toggle="switchbutton"
                                                        data-onstyle="primary" class="" name="is_googlecalendar_enabled"
                                                        id="is_googlecalendar_enabled"
                                                        {{ isset($currentWorkspace->is_googlecalendar_enabled) && $currentWorkspace->is_googlecalendar_enabled == 'on' ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                        {{Form::label('Google calendar id',__('Google Calendar Id'),['class'=>'col-form-label']) }}
                                        {{Form::text('google_calender_id',!empty($currentWorkspace['google_calender_id']) ? $currentWorkspace['google_calender_id'] : '' ,array('class'=>'form-control ' ,'placeholder'=>"Google Calendar Id"))}}
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                        {{Form::label('Google calendar json file',__('Google Calendar json File'),['class'=>'col-form-label']) }}
                                        <input type="file" class="form-control" name="google_calender_json_file" id="google_calender_json_file">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button class="btn-submit btn btn-primary" type="submit">
                                    {{__('Save Changes')}}
                                </button>
                            </div>
                            {{Form::close()}}
                        </div>
                    </div>
                    @endif

                    <!--Webhook_Setting-->
                    <div id="webhook-settings" class="card">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div class="card-header">
                                    <div class="row justify-content-between">
                                        <div class="col-10">
                                            <h5 class="">{{ __('Webhook Settings') }}</h5>
                                            <small class="text-secondary font-weight-bold">
                                                {{ __('Edit your Webhook Settings') }}
                                            </small>
                                        </div>
                                        <div class="text-end  col-auto">
                                            <div class="btn btn-sm btn-primary btn-icon m-1">
                                                <a href="#" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Create Webhook') }}" data-url="{{ route('webhook.create' , $currentWorkspace->slug) }}" data-size="md"
                                                    data-ajax-popup="true" data-title="{{ __('Create New Webhook') }}">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    {{-- <th scope="sort">{{ __('Id') }}</th> --}}
                                                    <th scope="sort">{{ __('Module') }}</th>
                                                    <th scope="sort">{{ __('Url') }}</th>
                                                    <th scope="sort">{{ __('Method') }}</th>
                                                    <th scope="sort" class="text-end">{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $webhook = App\Models\Webhook::where('created_by','=', Auth::user()->id)->get();
                                                @endphp
                                                    {{-- @if (!empty($webhook) && count($webhook) > 0) --}}
                                                    @foreach ($webhook as $data)
                                                        <tr>
                                                            <td>{{$data->module}}</td>
                                                            <td>{{$data->url}}</td>
                                                            <td>{{$data->method}}</td>
                                                            <td class="text-end">
                                                                <a href="#"
                                                                    class="action-btn btn-info  btn btn-sm d-inline-flex align-items-center"
                                                                    data-size="lg"
                                                                    data-ajax-popup="true"
                                                                    data-title="{{ __('Edit Webhook') }}"
                                                                    data-url="{{ route('webhook.edit', [$currentWorkspace->slug, $data->id]) }}"
                                                                    data-toggle="tooltip" title="{{ __('Edit Webhook') }}">
                                                                    <i class="ti ti-pencil text-white"></i>
                                                                </a>
                                                                <a href="#"
                                                                    class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                    data-confirm-yes="delete-form-{{ $data->id }}" data-toggle="tooltip"
                                                                    title="{{ __('Delete') }}">
                                                                    <i class="ti ti-trash text-white"></i>
                                                                </a>
                                                                <form id="delete-form-{{ $data->id }}"
                                                                    action="{{ route('webhook.destroy', [$currentWorkspace->slug, $data->id]) }}"
                                                                    method="POST" style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                {{-- @else
                                                    <tr>
                                                        <td colspan="4">
                                                            <div class="text-center">
                                                                <i class="fas fa-user-slash text-primary fs-40"></i>
                                                                <h2>{{ __('Opps...') }}</h2>
                                                                <h6> {!! __('No data request found...!') !!} </h6>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ sample-page ] end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/custom/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/custom/js/repeater.js') }}"></script>
    <script src="{{ asset('assets/custom/js/colorPick.js') }}"></script>
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
    <script>
        function check_theme(color_val) {
            $('input[value="' + color_val + '"]').prop('checked', true);
            $('input[value="' + color_val + '"]').attr('checked', true);
            $('a[data-value]').removeClass('active_color');
            $('a[data-value="' + color_val + '"]').addClass('active_color');
        }
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>

    <script src="{{ asset('assets/js/pages/wow.min.js') }}"></script>
    <script>
        // Start [ Menu hide/show on scroll ]
        let ost = 0;
        document.addEventListener("scroll", function() {
            let cOst = document.documentElement.scrollTop;
            if (cOst == 0) {
                //   document.querySelector(".navbar").classList.add("top-nav-collapse");
            } else if (cOst > ost) {
                document.querySelector(".navbar").classList.add("top-nav-collapse");
                document.querySelector(".navbar").classList.remove("default");
            } else {
                document.querySelector(".navbar").classList.add("default");
                document
                    .querySelector(".navbar")
                    .classList.remove("top-nav-collapse");
            }
            ost = cOst;
        });
        // End [ Menu hide/show on scroll ]
        var wow = new WOW({
            animateClass: "animate__animated", // animation css class (default is animated)
        });
        wow.init();
        // var scrollSpy = new bootstrap.ScrollSpy(document.body, {
        //   target: "#navbar-example",
        // });
    </script>
    <script>
        $(document).on("change", "select[name='invoice_template'], input[name='invoice_color']", function() {
            var template = $("select[name='invoice_template']").val();
            var color = $("input[name='invoice_color']:checked").val();
            $('iframe').attr('src', '{{ url($currentWorkspace->slug . '/invoices/preview') }}/' + template + '/' +
                color);
        });

        $(document).ready(function() {

            var $dragAndDrop = $("body .task-stages tbody").sortable({
                handle: '.sort-handler'
            });

            var $repeater = $('.task-stages').repeater({
                initEmpty: true,
                defaultValues: {},
                show: function() {
                    $(this).slideDown();
                },
                hide: function(deleteElement) {
                    if (confirm('{{ __('Are you sure ?') }}')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                ready: function(setIndexes) {
                    $dragAndDrop.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });


            var value = $(".task-stages").attr('data-value');
            if (typeof value != 'undefined' && value.length != 0) {
                value = JSON.parse(value);
                $repeater.setList(value);
            }

            var $dragAndDropBug = $("body .bug-stages tbody").sortable({
                handle: '.sort-handler'
            });

            var $repeaterBug = $('.bug-stages').repeater({
                initEmpty: true,
                defaultValues: {},
                show: function() {
                    $(this).slideDown();
                },
                hide: function(deleteElement) {
                    if (confirm('{{ __('Are you sure ?') }}')) {
                        $(this).slideUp(deleteElement);
                    }
                },
                ready: function(setIndexes) {
                    $dragAndDropBug.on('drop', setIndexes);
                },
                isFirstItemUndeletable: true
            });


            var valuebug = $(".bug-stages").attr('data-value');
            if (typeof valuebug != 'undefined' && valuebug.length != 0) {
                valuebug = JSON.parse(valuebug);
                $repeaterBug.setList(valuebug);
            }
            $(document).on('click', '.list-group-item', function() {
                $('.list-group-item').removeClass('active');
                $('.list-group-item').removeClass('text-primary');
                setTimeout(() => {
                    $(this).addClass('active').removeClass('text-primary');
                }, 10);
            });

            var type = window.location.hash.substr(1);
            $('.list-group-item').removeClass('active');
            $('.list-group-item').removeClass('text-primary');
            if (type != '') {
                $('a[href="#' + type + '"]').addClass('active').removeClass('text-primary');
            } else {
                $('.list-group-item:eq(0)').addClass('active').removeClass('text-primary');
            }
        });
    </script>


    <script>
        $('#logo').change(function() {

            let reader = new FileReader();
            reader.onload = (e) => {
                $('#dark_logo').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);

        });

        $('#logo_white').change(function() {

            let reader = new FileReader();
            reader.onload = (e) => {
                $('#image').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);

        });

        $('#small-favicon').change(function() {

            let reader = new FileReader();
            reader.onload = (e) => {
                $('#favicon').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);

        });
    </script>

    <script>
        $(document).ready(function() {
            if ($('.gdpr_fulltime').is(':checked')) {
                $('.fulltime').show();
            } else {
                $('.fulltime').hide();
            }
            $('#gdpr_cookie').on('change', function() {
                if ($('.gdpr_fulltime').is(':checked')) {
                    $('.fulltime').show();
                } else {
                    $('.fulltime').hide();
                }
            });
        });

        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })

        $('.themes-color-change').on('click', function() {
            var color_val = $(this).data('value');
            $('.theme-color').prop('checked', false);
            $('.themes-color-change').removeClass('active_color');
            $(this).addClass('active_color');
            $(`input[value=${color_val}]`).prop('checked', true);

        });

        function check_theme(color_val) {
            $('.theme-color').prop('checked', false);
            $('input[value="' + color_val + '"]').prop('checked', true);
            $('#color_value').val(color_val);
        }
    </script>

    <script>
        $(document).on("click", ".email-template-checkbox", function() {
            var chbox = $(this);
            $.ajax({
                url: chbox.attr('data-url'),
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    status: chbox.val()
                },
                type: 'POST',
                success: function(response) {
                    if (response.is_success) {
                        show_toastr('{{ __('Success') }}', response.success, 'success');
                        if (chbox.val() == 1) {
                            $('#' + chbox.attr('id')).val(0);
                        } else {
                            $('#' + chbox.attr('id')).val(1);
                        }
                    } else {
                        show_toastr('{{ __('Error') }}', response.error, 'error');
                    }
                },
                error: function(response) {
                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('{{ __('Error') }}', response.error, 'error');
                    } else {
                        show_toastr('{{ __('Error') }}', response, 'error');
                    }
                }
            })
        });
    </script>
    <script>
        function cust_theme_bg() {
            var custthemebg = document.querySelector("#cust-theme-bg");

            if (custthemebg.checked) {
                document.querySelector(".dash-sidebar").classList.add("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.add("transprent-bg");
            } else {
                document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
                document
                    .querySelector(".dash-header:not(.dash-mob-header)")
                    .classList.remove("transprent-bg");
            }

        }

        function cust_darklayout() {
            var custdarklayout = document.querySelector("#cust-darklayout");

            if (custdarklayout.checked) {
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "{{ asset('assets/images/logo.svg') }}");
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style-dark.css') }}");
            } else {
                document
                    .querySelector(".m-header > .b-brand > .logo-lg")
                    .setAttribute("src", "{{ asset('assets/images/logo-dark.svg') }}");
                document
                    .querySelector("#main-style-link")
                    .setAttribute("href", "{{ asset('assets/css/style.css') }}");
            }

        }
    </script>

@endpush
