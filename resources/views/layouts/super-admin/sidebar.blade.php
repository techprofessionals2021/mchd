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
<nav class="dash-sidebar light-sidebar {{ isset($cust_theme_bg) && $cust_theme_bg == 'on' ? 'transprent-bg' : '' }}">
    <div class="navbar-wrapper">
        <div class="m-header main-logo">
            <a href="{{ route('superadmin.home') }}" class="b-brand">
                <!-- ========   change your logo hear   ============ -->

                {{-- <img
            src="{{$logo.$company_logo.'?timestamp='.strtotime(isset($currentWorkspace) ? $currentWorkspace->updated_at : '')}}" alt="logo" class="sidebar_logo_size" /> --}}

                <img src="{{ asset('custom-auth/uploads/logo/logo/20230320172321.png') }}" alt="logo"
                    class="sidebar_logo_size" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="dash-navbar">


                <li class="dash-item dash-hasmenu">
                    <a href="{{route('superadmin.home')}}"
                        class="dash-link {{ Request::route()->getName() == 'superadmin.home' ? ' active' : '' }}"><span
                            class="dash-micon"> <i data-feather="user"></i></span><span
                            class="dash-mtext">{{ __('Dashboard') }}</span></a>
                </li> 
                <li class="dash-item dash-hasmenu {{ Request::route()->getName() == 'getWorkSpaces' ? ' active' : '' }}">
                <a href="#" class="dash-link"><span class="dash-micon"><i
                            class="ti ti-device-floppy"></i></span><span
                        class="dash-mtext">{{ __('Workspaces') }}</span><span class="dash-arrow"><i
                            data-feather="chevron-right"></i></span></a>
                <ul
                    class="dash-submenu collapse  {{ Request::route()->getName() == 'contracts.index' ? ' active' : '' }}">
                  
                    <li class="dash-item ">
                        <a class="dash-link"
                            href="{{route('superadmin.workspace')}}">{{ __('Workspaces') }}</a>
                    </li>
                </ul>
               </li>


                        <li class="dash-item dash-hasmenu">
                            <a href="{{route('superadmin.user')}}"
                                class="dash-link {{ Request::route()->getName() == 'users.index' ? ' active' : '' }}"><span
                                    class="dash-micon"> <i data-feather="user"></i></span><span
                                    class="dash-mtext">{{ __('Users') }}</span></a>
                        </li>

                        <li class="dash-item dash-hasmenu">
                            <a href="{{route('superadmin.project')}}"
                                class="dash-link {{ Request::route()->getName() == 'projects.index' ? ' active' : '' }}"><span
                                    class="dash-micon"> <i data-feather="briefcase"></i></span><span
                                    class="dash-mtext">{{ __('Projects') }}</span></a>
                        </li>


                      <li class="dash-item {{ Request::route()->getName() == 'tasks.index' ? ' active' : '' }}">
                            <a href="{{route('superadmin.task')}}" class="dash-link "><span
                                    class="dash-micon"><i data-feather="list"></i></span><span
                                    class="dash-mtext">{{ __('Tasks') }}</span></a>
                        </li>

                     
                            {{-- <li class="dash-item dash-hasmenu">
                                <a href="{{ route('clients.index', $currentWorkspace->slug) }}"
                                    class="dash-link {{ Request::route()->getName() == 'clients.index' ? ' active' : '' }}"><span
                                        class="dash-micon"> <i class="ti ti-brand-python"></i></span><span
                                        class="dash-mtext"> {{ __('Clients') }}</span></a>
                            </li> --}}
                    

                

     
        </div>
    </div>
</nav>


