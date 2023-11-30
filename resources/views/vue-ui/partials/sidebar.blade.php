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
        $company_logo = App\Models\Utility::get_logo();
    } else {
        $setting = App\Models\Utility::getcompanySettings($currentWorkspace->id);
        $color = $setting->theme_color;
        $dark_mode = $setting->cust_darklayout;
        $SITE_RTL = $setting->site_rtl;
        $cust_theme_bg = $setting->cust_theme_bg;
        $company_logo = App\Models\Utility::getcompanylogo($currentWorkspace->id);

        if ($company_logo == '' || $company_logo == null) {
            $company_logo = App\Models\Utility::get_logo();
        }

        $user = \App\Models\User::find(Auth::id());
        $permissions = $user->getPermissionWorkspace($currentWorkspace->id);
        // dd($permissions);
        if (!$permissions) {
            $permissions = [];
        }
    }

    if ($color == '' || $color == null) {
        $settings = App\Models\Utility::getAdminPaymentSettings();
        $color = $settings['color'];
    }

    if ($dark_mode == '' || $dark_mode == null) {
        $company_logo = App\Models\Utility::get_logo();
        $dark_mode = $settings['cust_darklayout'];
    }

    if ($cust_theme_bg == '' || $dark_mode == null) {
        $cust_theme_bg = $settings['cust_theme_bg'];
    }

    if ($SITE_RTL == '' || $SITE_RTL == null) {
        $SITE_RTL = env('SITE_RTL');
    }
@endphp
{{-- {{dd(auth()->user()->hasRole(('HOD')))}} --}}


{{-- @if (auth()->user()->hasRole('Ceo')) --}}
{{-- <nav class="dash-sidebar light-sidebar {{ isset($cust_theme_bg) && $cust_theme_bg == 'on' ? 'transprent-bg' : '' }}"
style="border-right: 1px solid ">
<div class="navbar-wrapper">
    <div class="m-header main-logo">
        <a href="{{ route('home') }}" class="b-brand">
            <!-- ========   change your logo hear   ============ -->


            <img src="{{ asset('custom-auth/uploads/logo/logo/20230320172321.png') }}" alt="logo"
                class="sidebar_logo_size" />
        </a>
    </div>
    <div class="navbar-content scroll-container">
        <ul class="dash-navbar content">
            @if (\Auth::guard('client')->check())
                <li class="dash-item dash-hasmenu">
                    <a href="{{ route('client.home') }}"
                        class="dash-link {{ Request::route()->getName() == 'home' || Request::route()->getName() == null || Request::route()->getName() == 'client.home' ? ' active' : '' }} side-item">
                        <span class="dash-micon"><i class="ti ti-home"></i></span>
                        <span class="dash-mtext">{{ __('Dashboard') }}</span>


                    </a>
                </li>
            @else
                <li class="dash-item dash-hasmenu">
                    <a href="{{ route('home') }}"
                        class="dash-link  {{ Request::route()->getName() == 'home' || Request::route()->getName() == null || Request::route()->getName() == 'client.home' ? ' active' : '' }} side-item">
                        @if (Auth::user()->type == 'admin')
                            <span class="dash-micon"><i class="ti ti-user"></i></span>
                        <span class="dash-mtext">{{ __('Users') }}</span>@else<span
                                class="dash-micon mr-3 mr-3"><img
                                    src="{{ asset('custom-ui/images/home.svg') }}" class="icon-image" /></span>
                            <span class="dash-mtext side-nav-text">{{ __('Home') }}</span>
                        @endif
                    </a>
                </li>
            @endif


            <li class="dash-item dash-hasmenu">
                <a href="{{ route('ceo.executives',$currentWorkspace->slug) }}"
                    class="dash-link  {{ Request::route()->getName() == 'home' || Request::route()->getName() == null || Request::route()->getName() == 'client.home' ? ' active' : '' }} side-item">
                    @if (Auth::user()->type == 'admin')
                        <span class="dash-micon"><i class="ti ti-user"></i></span>
                    <span class="dash-mtext">{{ __('Executives') }}</span>@else<span
                            class="dash-micon mr-3 mr-3"><img
                                src="{{ asset('custom-ui/images/home.svg') }}" class="icon-image" /></span>
                        <span class="dash-mtext side-nav-text">{{ __('Executives') }}</span>
                    @endif
                </a>
            </li>


            @if (isset($currentWorkspace) && $currentWorkspace)
                @auth('web')
                    <li class="dash-item dash-hasmenu">
                        <a href=""
                            class="dash-link {{ Request::route()->getName() == 'users.index' ? ' active' : '' }} side-item"><span
                                class="dash-micon mr-3"> <img
                                    src="{{ asset('custom-ui/images/notification.svg') }}" class="icon-image" /></span><span
                                class="dash-mtext side-nav-text">{{ __('Notification') }}</span></a>
                    </li>

                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('notes.index', $currentWorkspace->slug) }}"
                            class="dash-link{{ Request::route()->getName() == 'notes.index' ? ' active' : '' }} side-item"><span
                                class="dash-micon mr-3"> <img
                                    src="{{ asset('custom-ui/images/note.svg') }}" class="icon-image" /></span><span
                                class="dash-mtext side-nav-text">{{ __('Notes') }}</span></a>
                    </li>
                @endauth
            @endif





    </div>
</div>
</nav> --}}

{{-- @else --}}
    <nav class="dash-sidebar light-sidebar {{ isset($cust_theme_bg) && $cust_theme_bg == 'on' ? 'transprent-bg' : '' }}"
        style="border-right: 1px solid ">
        <div class="navbar-wrapper">
            <div class="m-header main-logo">
                <a href="{{ route('home') }}" class="b-brand">
                    <!-- ========   change your logo hear   ============ -->


                    <img src="{{ asset('custom-auth/uploads/logo/logo/20230320172321.png') }}" alt="logo"
                        class="sidebar_logo_size" />
                </a>
            </div>
            <div class="navbar-content scroll-container">
                <ul class="dash-navbar content">
                    @if (\Auth::guard('client')->check())
                        <li class="dash-item dash-hasmenu">
                            <a href="{{ route('client.home') }}"
                                class="dash-link {{ Request::route()->getName() == 'home' || Request::route()->getName() == null || Request::route()->getName() == 'client.home' ? ' active' : '' }} side-item">
                                <span class="dash-micon"><i class="ti ti-home"></i></span>
                                <span class="dash-mtext">{{ __('Dashboard') }}</span>


                            </a>
                        </li>
                    @else
                        <li class="dash-item dash-hasmenu">
                            <a href="{{ route('home') }}"
                                class="dash-link  {{ Request::route()->getName() == 'home' || Request::route()->getName() == null || Request::route()->getName() == 'client.home' ? ' active' : '' }} side-item">
                                @if (Auth::user()->type == 'admin')
                                    <span class="dash-micon"><i class="ti ti-user"></i></span>
                                <span class="dash-mtext">{{ __('Users') }}</span>@else<span
                                        class="dash-micon mr-3 mr-3"><img
                                            src="{{ asset('custom-ui/images/home.svg') }}" class="icon-image" /></span>
                                    <span class="dash-mtext side-nav-text">{{ __('Home') }}</span>
                                @endif
                            </a>
                        </li>
                    @endif






                    @if (isset($currentWorkspace) && $currentWorkspace)
                        @auth('web')

                        {{-- @if ($currentWorkspace->created_by ===  Auth::id()  && $currentWorkspace->is_default == 1  ) --}}
                        @if ($currentWorkspace->created_by !=  Auth::id()  && $currentWorkspace->is_default == 1  )
                 
                        
                        @else
                        <li class="dash-item dash-hasmenu">
                            <a href="{{ route('users.index', $currentWorkspace->slug) }}"
                                class="dash-link{{ Request::route()->getName() == 'users.index' ? ' active' : '' }} side-item"><span
                                    class="dash-micon mr-3"> <img
                                        src="{{ asset('custom-ui/images/user-icon-image2.png') }}" class="icon-image" /></span><span
                                    class="dash-mtext side-nav-text">{{ __('Users') }}</span></a>
                        </li>
                        @endif



                            <li class="dash-item dash-hasmenu">
                                <a href=""
                                    class="dash-link {{ Request::route()->getName() == 'users.index' ? ' active' : '' }} side-item"><span
                                        class="dash-micon mr-3"> <img
                                            src="{{ asset('custom-ui/images/notification.svg') }}" class="icon-image" /></span><span
                                        class="dash-mtext side-nav-text">{{ __('Notification') }}</span></a>
                            </li>

                            <li class="dash-item dash-hasmenu">
                                <a href="{{ route('notes.index', $currentWorkspace->slug) }}"
                                    class="dash-link{{ Request::route()->getName() == 'notes.index' ? ' active' : '' }} side-item"><span
                                        class="dash-micon mr-3"> <img
                                            src="{{ asset('custom-ui/images/note.svg') }}" class="icon-image" /></span><span
                                        class="dash-mtext side-nav-text">{{ __('Notes') }}</span></a>
                            </li>

                        @endauth
                    @endif

                    <li
                        class="dash-item {{ Request::route()->getName() == 'getWorkSpaces' ? ' active' : '' }} ">
                        <span href="#" class="dash-link side-item mb-0 space-nav"
                            style="border-radius: 12px 12px 0px 0px ;"><span class="dash-micon mr-3">
                                <img src="{{ asset('custom-ui/images/space.svg') }}" class="icon-image" /></span><span
                                class="dash-mtext side-nav-text">{{ __('Spaces') }}</span><span
                                class="nav-arrow"><img
                                    src="{{ asset('custom-ui/images/arrow-nav.svg') }}" class="icon-image m-t-10" /></span> </span>
                        <div class="dash-link side-item mt-0 space-body "
                            style="border-radius: 0px 0px 12px 12px ; display:none;
                                 text-align: center;">
                             <a href="{{route('getAllProjectAndTasks',$currentWorkspace->slug)}}" class="btn side-nav-text font-bold">
                                {{-- <i class="ti ti-plus"></i> --}}
                                <span>Every Thing</span>
                            </a>
                            <button class="btn btn-light space-btn openAddWorkSpace">
                                <i class="ti ti-plus"></i>
                                <span>New Space</span>
                            </button>



                            @foreach (auth()->user()->workspace as $workspace)
                            <div class="mt-2 p-l-15">
                                <div class="d-flex {{ $workspace->id == $currentWorkspace->id ? 'c-slider' : ''}}">
                                    <div class="ws-li-block"></div>
                                    @if ($workspace->id == $currentWorkspace->id)
                                    <p class="m-l-5 side-nav-text cursor-pointer overflow-hiddenn {{ $workspace->id == $currentWorkspace->id ? 'font-extrabold' : ''}}">{{ $workspace->name}} {!! $workspace->id == $currentWorkspace->id ? '<span class="badge badge-success">Current</span>' : ''!!} </p>
                                    @else

                                    <a href={{route('change-workspace', $workspace->id)}}>
                                        <p class="m-l-5 side-nav-text cursor-pointer overflow-hiddenn {{ $workspace->id == $currentWorkspace->id ? 'font-extrabold' : ''}}">{{ $workspace->name}} {!! $workspace->id == $currentWorkspace->id ? '<span class="badge badge-success">Current</span>' : ''!!} </p>
                                      </a>

                                    @endif

                                </div>
                                <div class="c-slided" style="display: none">
                                    <ul class='project-list'>
                                        @foreach ($workspace->projects as $project)
                                        <li ><a href="{{route('projects.show',[$workspace->slug,$project->id])}}" class="side-nav-project-text">{{$project->name}} </a></li>
                                        @endforeach
                                        <a class="btn btn-light add-project-btn-sidebar m-t-10" href="#" data-ajax-popup="true" data-size="md" data-title="{{ __('Create New Project') }}" data-url="{{route('projects.create',$currentWorkspace->slug)}}">
                                            <i class="ti ti-plus"></i>
                                            <span>Add Project</span>
                                        </a>
                                    </ul>
                                </div>
                            </div>
                            @endforeach



                        </div>


                        {{-- <ul
                        class="dash-submenu collapse  {{ Request::route()->getName() == 'contracts.index' ? ' active' : '' }} side-item">
                        <li
                            class="openAddWorkSpace dash-item {{ Request::route()->getName() == 'contracts.index' || Request::route()->getName() == 'contracts.show' ? 'active' : '' }}">
                                <a href="#!" class="dash-link" data-toggle="modal"
                                data-target="#modelCreateWorkspace">
                                <i class="ti ti-circle-plus"></i>
                                <span>{{ __('Create Workspace') }}</span>
                            </a>
                        </li>
                        <li class="dash-item ">
                            <a class="dash-link"
                                href="{{ route('getWorkSpaces', $currentWorkspace->slug) }}">{{ __('Workspaces') }}</a>
                        </li>
                    </ul> --}}

                    </li>

                     <li class="dash-item dash-hasmenu">
                        <a href="{{ route('custom.calender',[$currentWorkspace->slug]) }}"
                            class="dash-link{{ Request::route()->getName() == 'custom.calender' ? ' active' : '' }} side-item"><span
                                class="dash-micon mr-3"> <img
                                    src="{{ asset('custom-ui/images/calendar.png') }}" class="icon-image" /></span><span
                                class="dash-mtext side-nav-text">{{ __('Calendar') }}</span></a>
                    </li>
                     <li class="dash-item dash-hasmenu">
                        <a href="{{ route('custom.huddles',[$currentWorkspace->slug]) }}"
                            class="dash-link{{ Request::route()->getName() == 'custom.huddles' ? ' active' : '' }} side-item"><span
                                class="dash-micon mr-3"> <img
                                    src="{{ asset('custom-ui/images/calendar.png') }}" class="icon-image" /></span><span
                                class="dash-mtext side-nav-text">{{ __('Committees') }}</span></a>
                    </li>

                @if (auth()->user()->hasRole(('HOD')) )
                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('index_report') }}"
                            class="dash-link{{ Request::route()->getName() == 'custom.calender' ? ' active' : '' }} side-item"><span
                                class="dash-micon mr-3"> <img
                                    src="{{ asset('custom-ui/images/calendar.png') }}" class="icon-image" /></span><span
                                class="dash-mtext side-nav-text">{{ __('Workspace Report') }}</span></a>
                    </li>
                @elseif (auth()->user()->hasRole(('EXECUTIVE')))

                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('index_report',[$currentWorkspace->slug]) }}"
                            class="dash-link{{ Request::route()->getName() == 'custom.calender' ? ' active' : '' }} side-item"><span
                                class="dash-micon mr-3"> <img
                                    src="{{ asset('custom-ui/images/calendar.png') }}" class="icon-image" /></span><span
                                class="dash-mtext side-nav-text">{{ __('HODS Report') }}</span></a>
                    </li>
                @elseif (auth()->user()->hasRole(('CEO')))

                    <li class="dash-item dash-hasmenu">
                        <a href="{{ route('index_report',[$currentWorkspace->slug]) }}"
                            class="dash-link{{ Request::route()->getName() == 'custom.calender' ? ' active' : '' }} side-item"><span
                                class="dash-micon mr-3"> <img
                                    src="{{ asset('custom-ui/images/calendar.png') }}" class="icon-image" /></span><span
                                class="dash-mtext side-nav-text">{{ __('Executive Report') }}</span></a>
                    </li>

                @endif


                    </li>

                    {{-- <li
                    class="dash-item {{ Request::route()->getName() == 'project_report.index' || Request::segment(2) == 'project_report' ? ' active' : '' }}">
                    <a href="{{ route('project_report.index', $currentWorkspace->slug) }}"
                        class="dash-link "><span class="dash-micon"><i class="ti ti-chart-line"></i></span><span
                            class="dash-mtext">{{ __('Project Report') }}</span></a>
                    </li> --}}

                    {{-- <li class="dash-item dash-hasmenu">
                        <a href="{{ route('report.index',[$currentWorkspace->slug])}}"
                            class="dash-link{{ Request::route()->getName() == 'report.index' ? ' active' : '' }} side-item"><span
                                class="dash-micon mr-3"> <img
                                    src="{{ asset('custom-ui/images/calendar.png') }}" class="icon-image" /></span><span
                                class="dash-mtext side-nav-text">{{ __('Reports') }}</span></a>
                    </li>  --}}

                    @if (Auth::user()->type == 'admin')
                        <li
                            class="dash-item {{ Request::route()->getName() == 'lang_workspace' ? ' active' : '' }} side-item">
                            <a href="{{ route('lang_workspace') }}" class="dash-link "><span class="dash-micon"><i
                                        class="ti ti-world nocolor"></i></span><span
                                    class="dash-mtext">{{ __('Languages') }}</span></a>
                        </li>


                        <li
                            class="dash-item {{ Request::route()->getName() == 'email_template*' || Request::segment(1) == 'email_template_lang' ? ' active' : '' }}">
                            <a class="dash-link" href="{{ route('email_template.index') }}">
                                <span class="dash-micon"><i class="ti ti-mail"></i></span><span
                                    class="dash-mtext">{{ __('Email Templates') }}</span>
                            </a>
                        </li>
                        @include('landingpage::menu.landingpage')
                    @endif





            </div>
        </div>
    </nav>
{{-- @endif --}}




<script>
    $(function() {
        $('.openAddWorkSpace').on('click', function() {
            $('#modelCreateWorkspace').modal('show')
        });
        $('.btn-close').on('click', function() {
            $('#modelCreateWorkspace').modal('hide')
        });
    })
</script>

<script>
    $(document).ready(function() {
        $('.space-nav').on('click', () => {
            $('.space-body').slideToggle();
            $('.nav-arrow').toggleClass('rotate-90');

        })

        $('.c-slider').on('click', function () {
            // console.log();
            $(this).next().slideToggle()
            // $('c-slider').siblings( ".c-slided" ).addClass('dsdd');
        })
    });
</script>
