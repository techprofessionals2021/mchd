@php
    $unseenCounter = App\Models\ChMessage::where('to_id', Auth::user()->id)
        ->where('seen', 0)
        ->count();
    $logo = \App\Models\Utility::get_file('users-avatar/');
@endphp
@php
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
    $currantLang = basename(App::getLocale());
    if ($currantLang == ''){
        $currantLang = 'en';

    }
@endphp


<style type="text/css">
    .top_header {
        left: auto !important;
        top: 60px !important;
    }
</style>
<header class="dash-header {{ isset($cust_theme_bg) && $cust_theme_bg == 'on' ? 'transprent-bg' : '' }}">

    <div class="header-wrapper">
        <div class="dash-mob-drp">
            <ul class="list-unstyled">
                <li class="dash-h-item mob-hamburger">
                    <a href="#!" class="dash-head-link" id="mobile-collapse">
                        <div class="hamburger hamburger--arrowturn">
                            <div class="hamburger-box">
                                <div class="hamburger-inner"></div>
                            </div>
                        </div>
                    </a>
                </li>
                @if (Auth::user()->type != 'admin')
                    <li class="dropdown dash-h-item">
                        {{-- <a class="dash-head-link dropdown-toggle arrow-none ms-0" data-bs-toggle="dropdown"
                            href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="ti ti-search"></i>
                        </a> --}}
                        <div class="dropdown-menu dash-h-dropdown drp-search drp-search-custom">
                            <form class="form-inline mr-auto mb-0">
                                <div class="search-element">
                                    <input class="" type="type here" placeholder="Search here. . ."
                                        aria-label="Search">

                                    <div class="search-backdrop"></div>
                                </div>
                            </form>
                        </div>
                    </li>
                @endif
                <li class="dropdown dash-h-item drp-company">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <img class="theme-avtar"@if (Auth::user()->avatar) src="{{ asset($logo . Auth::user()->avatar) }}" @else avatar="{{ Auth::user()->name }}" @endif
                            alt="{{ Auth::user()->name }}">
                        <span class="hide-mob ms-2">{{ __('Hi') }},{{ Auth::user()->name }} !</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown">
                        @if (isset($currentWorkspace) && $currentWorkspace)
                            @auth('web')
                                @if (Auth::user()->id == $currentWorkspace->created_by)
                                    {{-- <a href="#" class="dropdown-item bs-pass-para"
                                        data-confirm="{{ __('Are You Sure?') }}"
                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                        data-confirm-yes="remove-workspace-form">
                                        <i class="ti ti-circle-x"></i>
                                        <span>{{ __('Remove Me From This Workspace') }}</span>
                                    </a>
                                    <form id="remove-workspace-form"
                                        action="{{ route('delete-workspace', ['id' => $currentWorkspace->id]) }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form> --}}
                                @else
                                    {{-- <a href="#" class="dropdown-item bs-pass-para"
                                        data-confirm="{{ __('Are You Sure?') }}"
                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                        data-confirm-yes="remove-workspace-form">
                                        <i class="ti ti-circle-x"></i>
                                        <span>{{ __('Leave Me From This Workspace') }}</span>
                                    </a>
                                    <form id="remove-workspace-form"
                                        action="{{ route('leave-workspace', ['id' => $currentWorkspace->id]) }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form> --}}
                                @endif
                            @endauth
                        @endif

                        <a href="@auth('web'){{ route('users.my.account') }}@elseauth{{ route('client.users.my.account') }}@endauth"
                            class="dropdown-item">
                            <i class="ti ti-user"></i>
                            <span>{{ __('My Profile') }}</span>
                        </a>
                                                    <!--   @if (env('CHAT_MODULE') == 'on')
                                            @if (\Auth::user()->type == 'user')
                            <a href="{{ url('chats') }}" class="dropdown-item">
                                            <i class="ti ti-message-circle"></i>
                                            <span>{{ __('Chats') }}</span>
                                            </a>
                            @endif
                                            @endif -->
                        <a href="#"
                            class="dropdown-item "onclick="event.preventDefault();document.getElementById('logout-form1').submit();">
                            <i class="ti ti-power"></i>
                            <span>{{ __('Logout') }}</span>
                        </a>
                        <form id="logout-form1"
                            action="@auth('web'){{ route('logout') }}@elseauth{{ route('client.logout') }}@endauth"
                            method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
                <li class="dropdown dash-h-item drp-company">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="hide-mob ms-2 py-2" id="selected-item"></span>
                        <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown">
                        @foreach (Auth::user()->workspace as $workspace)
                            @if ($workspace->is_active)
                                <a href="@if ($currentWorkspace->id == $workspace->id) #@else @auth('web'){{ route('change-workspace', $workspace->id) }}@elseauth{{ route('client.change-workspace', $workspace->id) }}@endauth @endif"
                                    title="{{ $workspace->name }}" class="dropdown-item">
                                    @if ($currentWorkspace->id == $workspace->id)
                                        <i class="ti ti-checks text-success"></i>
                                        <span id="current-workplace">{{ $workspace->name }}</span>
                                    @else
                                    <span>{{ $workspace->name }}</span>

                                    @endif

                                    @if (isset($workspace->pivot->permission))
                                        @if ($workspace->pivot->permission == 'Owner')
                                            <span
                                                class="badge bg-primary">{{ __($workspace->pivot->permission) }}</span>
                                        @else
                                            <span class="badge bg-dark">{{ __('Shared') }}</span>
                                        @endif
                                    @endif
                                </a>
                            @else
                                <a href="#" class="dropdown-item" title="{{ __('Locked') }}">
                                    <i class="ti ti-lock"></i>
                                    <span>{{ $workspace->name }}</span>
                                    @if (isset($workspace->pivot->permission))
                                        @if ($workspace->pivot->permission == 'Owner')
                                            <span
                                                class="badge badge-success-primary">{{ __($workspace->pivot->permission) }}</span>
                                        @else
                                            <span class="badge bg-dark">{{ __('Shared') }}</span>
                                        @endif
                                    @endif
                                </a>
                            @endif
                        @endforeach

                        <!--   <hr class="dropdown-divider" /> -->
                        @auth('web')
                            @if (Auth::user()->type == 'user')
                                <a href="#!" class="dropdown-item" data-toggle="modal"
                                    data-target="#modelCreateWorkspace">
                                    <i class="ti ti-circle-plus"></i>
                                    <span>{{ __('Create New Workspace') }}</span>
                                </a>
                            @endif
                        @endauth


                        @if (isset($currentWorkspace) && $currentWorkspace)
                            @auth('web')
                                @if (Auth::user()->id == $currentWorkspace->created_by)
                                    {{-- <a href="#" class="dropdown-item bs-pass-para"
                                        data-confirm="{{ __('Are You Sure?') }}"
                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                        data-confirm-yes="remove-workspace-form">
                                        <i class="ti ti-circle-x"></i>
                                        <span>{{ __('Remove Me From This Workspace') }}</span>
                                    </a>
                                    <form id="remove-workspace-form"
                                        action="{{ route('delete-workspace', ['id' => $currentWorkspace->id]) }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form> --}}
                                @else
                                    {{-- <a href="#" class="dropdown-item bs-pass-para"
                                        data-confirm="{{ __('Are You Sure?') }}"
                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                        data-confirm-yes="remove-workspace-form">
                                        <i class="ti ti-circle-x"></i>
                                        <span>{{ __('Leave Me From This Workspace') }}</span>
                                    </a>
                                    <form id="remove-workspace-form"
                                        action="{{ route('leave-workspace', ['id' => $currentWorkspace->id]) }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form> --}}
                                @endif
                            @endauth
                        @endif


                    </div>
                </li>


            </ul>
        </div>
        <!-- Brand + Toggler (for mobile devices) -->

        <div class="ms-auto">
            <ul class="list-unstyled">

                @if (env('CHAT_MODULE') == 'on')
                    @if (\Auth::user()->type == 'user')
                        <li class="dash-h-item">
                            <a class="dash-head-link me-0" href="{{ url('chats') }}">
                                <i class="ti ti-message-circle"></i>
                                <span
                                    class="bg-danger dash-h-badge message-counter custom_messanger_counter">{{ $unseenCounter }}<span
                                        class="sr-only"></span>
                                </span></a>
                        </li>
                    @endif
                @endif




                @if (\Auth::user()->type == 'user')
                    <li class="dropdown dash-h-item drp-notification">
                        @if (isset($currentWorkspace) && $currentWorkspace)
                            @auth('web')
                                @php($notifications = Auth::user()->notifications($currentWorkspace->id))

                                @php($all_notifications = Auth::user()->all_notifications($currentWorkspace->id))
                                <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                                    href="#" role="button" aria-haspopup="false" aria-expanded="false">

                                    <i class="ti ti-bell"></i>
                                    <span
                                        class="@if (count($notifications) > 0) bg-danger dash-h-badge dots @endif"><span
                                            class="sr-only"></span></span>
                                </a>
                                <div class="dropdown-menu dash-h-dropdown dropdown-menu-end notification_menu_all">
                                    <div class="noti-header">
                                        <h5 class="m-0">Notification</h5>
                                        <a href="#"
                                            data-url="{{ route('delete_all.notifications', $currentWorkspace->slug) }}"
                                            class="dash-head-link clear_all_notifications">Clear All</a>
                                    </div>
                                    <div class="noti-body">

                                        <div class="limited">
                                            @foreach ($notifications as $notification)
                                                {!! $notification->toHtml() !!}
                                            @endforeach

                                        </div>

                                        <div class="all_notification" style="display:none !important;">
                                            @foreach ($all_notifications as $notification)
                                                {!! $notification->toHtml() !!}
                                            @endforeach

                                        </div>

                                    </div>
                                    <div class="noti-footer">
                                        <div class="d-grid">
                                            <a href="#"
                                                class="btn dash-head-link justify-content-center text-primary mx-0 view_all_notification">View
                                                all</a>
                                            <a href="#"
                                                class="btn dash-head-link justify-content-center text-primary mx-0 view_less"
                                                style="display:none !important;">View less</a>

                                        </div>
                                    </div>
                                </div>
                            @endauth
                        @endif
                    </li>
                @endif



                {{-- @dd($currantLang) --}}
                {{-- <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-world nocolor"></i>
                        <span class="drp-text hide-mob">{{ Str::upper($currantLang) }}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                        @if (\Auth::guard('client')->check())
                            @foreach (\App\Models\Utility::languages() as $lang)
                                <a href="{{ route('change_lang_workspace1', [$currentWorkspace->id, $lang]) }}"
                                    class="dropdown-item {{ $currantLang == $lang ? 'text-danger' : '' }}">
                                    <span>{{ Str::upper($lang) }}</span>
                                </a>
                            @endforeach
                        @endif
                        @if (\Auth::user()->type == 'admin')
                            @foreach (\App\Models\Utility::languages() as $lang)
                                <a href="{{ route('change_lang_admin', $lang) }}"
                                    class="dropdown-item {{ $currantLang == $lang ? 'text-danger' : '' }}">
                                    <span>{{ Str::upper($lang) }}</span>
                                </a>
                            @endforeach
                            <div class="dropdown-divider m-0"></div>
								<a href="{{ route('lang_workspace') }}" class="dropdown-item text-primary"><span
                                class="dash-mtext">{{ __('Manage Language') }}</span></a>
                        @elseif(isset($currentWorkspace) && $currentWorkspace && (\Auth::guard('web')->check()))
                            @foreach (\App\Models\Utility::languages() as $lang)
                                <a href="{{ route('change_lang_workspace', [$currentWorkspace->id, $lang]) }}"
                                    class="dropdown-item {{ $currantLang == $lang ? 'text-danger' : '' }}">
                                    <span>{{ Str::upper($lang) }}</span>
                            @endforeach
                        @endif
                        </a>
                    </div>
                </li> --}}

                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-world nocolor"></i>
                        <span class="drp-text hide-mob">{{ ucfirst( \App\Models\Utility::getlang_fullname($currantLang)) }}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                        @if (\Auth::guard('client')->check())
                            @foreach (\App\Models\Utility::languages() as $lang)
                            @if(ucfirst( \App\Models\Utility::getlang_fullname($lang) == 'Arabic') or ucfirst( \App\Models\Utility::getlang_fullname($lang) == 'English'))
                                    <a href="{{ route('change_lang_workspace1', [$currentWorkspace->id, $lang]) }}"
                                        class="dropdown-item {{ $currantLang == $lang ? 'text-danger' : '' }}">
                                        <span>{{ ucfirst( \App\Models\Utility::getlang_fullname($lang))  }}</span>
                                    </a>
                            @endif
                            @endforeach
                        @endif
                        @if (\Auth::user()->type == 'admin')
                            @foreach (\App\Models\Utility::languages() as $lang)
                                        <a href="{{ route('change_lang_admin', $lang) }}"
                                        class="dropdown-item {{ $currantLang == $lang ? 'text-danger' : '' }}">
                                        <span>{{ ucfirst( \App\Models\Utility::getlang_fullname($lang))  }}</span></a>
                            @endforeach
                            <div class="dropdown-divider m-0"></div>
                                <a href="#" class="dropdown-item text-primary" data-ajax-popup="true" data-size="md"
                                data-title="{{ __('Create Language') }}" data-toggle="tooltip" title="{{ __('Create Language') }}"
                                data-url="{{ route('create_lang_workspace') }}">
                                <span class="dash-mtext">{{ __('Create Language') }}</span></a>
                            <div class="dropdown-divider m-0"></div>
								<a href="{{ route('lang_workspace') }}" class="dropdown-item text-primary"><span
                                class="dash-mtext">{{ __('Manage Language') }}</span></a>
                        @elseif(isset($currentWorkspace) && $currentWorkspace && (\Auth::guard('web')->check()))
                            @foreach (\App\Models\Utility::languages() as $lang)
                            @if(ucfirst( \App\Models\Utility::getlang_fullname($lang) == 'Arabic') or ucfirst( \App\Models\Utility::getlang_fullname($lang) == 'English'))
                                    <a href="{{ route('change_lang_workspace', [$currentWorkspace->id, $lang]) }}"
                                        class="dropdown-item {{ $currantLang == $lang ? 'text-danger' : '' }}">
                                        <span>{{ ucfirst( \App\Models\Utility::getlang_fullname($lang))  }}</span>
                            @endif
                            @endforeach
                        @endif
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>

<script>
    $(document).ready(function() {
      let current_workplace = $('#current-workplace').text();
      $('#selected-item').text(current_workplace)
    });
</script>
