@extends('layouts.admin')

@section('page-title')
    {{ __('Users') }}
@endsection

@php
    $logo = \App\Models\Utility::get_file('avatars/');
@endphp

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('users') }}</li>
@endsection
@section('action-button')
    @auth('web')
        @if (Auth::user()->type == 'admin')
            <a href="{{ route('user.export') }}" class="btn btn-sm btn-primary" data-toggle="tooltip" title="{{ __('Export') }}">
                <i class="ti ti-file-x"></i>
            </a>
            <a href="#" class="btn btn-sm btn-primary mx-1" data-ajax-popup="true" data-size="md"
                data-title="{{ __('Add User') }}" data-url="{{ route('user.file.import') }}" data-toggle="tooltip"
                title="{{ __('Import') }}">
                <i class="ti ti-file-import"></i>
            </a>
            <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md"
                data-title="{{ __('Add User') }}" data-url="{{ route('users.create') }}" data-toggle="tooltip"
                title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
     
        @endif
    @endauth
@endsection

@section('content')
    <form action="{{ route('users.filter', $currentWorkspace->slug) }}" method="POST">
        @csrf
        <div class="row justify-content-end align-items-baseline d-none"  id="show_filter">
            <div class="col-sm-6 col-xl-11 pb-2 ">
                <input type="text" name="tags" class="tags form-control" value="{{ old('tags') }}"
                    data-role="tagsinput" placeholder="Filter User By Tag" />
            </div>
            <div class="d-flex col-1 justify-content-xl-center">
                {{-- <button class=" btn btn-primary  btn-filter apply">{{ __('Apply') }}</button> --}}
                <button type="submit" class="btn btn-light bg-primary text-white apply">{{ __('Apply') }}</button>
            </div>
        </div>
    </form>
    @if ((isset($currentWorkspace) && $currentWorkspace) || Auth::user()->type == 'admin')
        <div class="row">
            @foreach ($users as $user)
                @php($workspace_id = isset($currentWorkspace) && $currentWorkspace ? $currentWorkspace->id : '')
                <div class="col-xl-3 col-lg-4 col-sm-6">
                    <div class="card   text-center">
                        <div class="card-header border-0 pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    @if (Auth::user()->type != 'admin')
                                     
                                            <div class="badge p-2 px-3 rounded bg-warning">{{ __('Executive') }}</div>
                                    
                                    @endif
                                </h6>
                            </div>
                            {{-- @if (isset($currentWorkspace) &&
                                    $currentWorkspace &&
                                    $currentWorkspace->permission == 'Owner' &&
                                    Auth::user()->id != $user->id) --}}
                                <div class="card-header-right">
                                    <div class="btn-group card-option">
                                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            {{-- @if (isset($currentWorkspace) &&
                                                    $currentWorkspace &&
                                                    $currentWorkspace->permission == 'Owner' &&
                                                    Auth::user()->id != $user->id) --}}
                                                {{-- <a href="#" class="dropdown-item" data-ajax-popup="true"
                                                    data-size="md" data-title="{{ __('Edit') }}"
                                                    data-url="{{ route('users.edit', [$currentWorkspace->slug, $user->id]) }}"><i
                                                        class="ti ti-edit"></i> <span>{{ __('Edit') }}</span></a>

                                                <a href="#" class="dropdown-item" data-ajax-popup="true"
                                                    data-size="md" data-title="{{ __('Reset Password') }}"
                                                    data-url="{{ route('users.reset.password', $user->id) }}"><i
                                                        class="ti ti-pencil"></i>
                                                    <span>{{ __('Reset Password') }}</span></a> --}}

                                                <a href="{{route('ceo.executive_report',[
                                                    'id' => $user->id,
                                                    'slug' => $currentWorkspace->slug
                                                ])}}" class="dropdown-item"><i
                                                        class="ti ti-edit"></i>
                                                    <span>{{ __('Details') }}</span></a>


                                                {{-- <a href="{{ route('work-space-permission', [
                                                        'workspace_id' => $currentWorkspace->id,
                                                        'slug' => $currentWorkspace->slug,
                                                        'user_id' => $user->id
                                                    ]) }}"
                                                    class="action-btn btn-primary mx-1  btn btn-sm d-inline-flex align-items-center"

                                                    title="{{ __('Permission') }}"
                                                    data-title="{{ __('Edit Permission') }}"
                                                    ><i
                                                        class="ti ti-lock"></i></a> --}}

                                            {{-- @endif --}}

                                        </div>
                                    </div>
                                </div>
                            {{-- @endif --}}
                        </div>
                        @if (Auth::user()->type == 'admin' && Auth::user()->id != $user->id)
                            <div class="text-end"
                                @if (Auth::user()->type == 'admin') style="margin: -10px -10px -30px;" @endif>
                                <div class="btn-group card-option">
                                    <button type="button" class="btn " data-bs-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">

                                        <a href="#" class="dropdown-item" data-ajax-popup="true" data-size="md"
                                            data-title="{{ __('Reset Password') }}"
                                            data-url="{{ route('users.reset.password', $user->id) }}"><i
                                                class="ti ti-edit"></i> <span>{{ __('Reset Password') }}</span></a>
                                    </div>
                                </div>
                            </div>
                        @endif





                        <div class="card-body">
                            <div class="avatar">
                                <img alt="user-image" class=" rounded-circle img_users_fix_size"
                                    @if ($user->avatar) src="{{ $logo . $user->avatar }}" @else avatar="{{ $user->name }}" @endif>
                            </div>
                            <h4 class="mt-2">{{ $user->name }}</h4>
                            <small>{{ $user->email }}</small>

                            <div class=" mb-0 mt-3">
                                <div class=" p-3">
                                    <div class="row px-2">
                                        @if (Auth::user()->type == 'admin')
                                            <div class="col-6 text-start">

                                                <h6 class="mb-0 px-3">{{ $user->countWorkspace() }}</h6>
                                                <p class="text-muted text-sm mb-0">{{ __('Workspaces') }}</p>
                                            </div>
                                            <div
                                                class="col-6 {{ Auth::user()->type == 'admin' ? 'text-end' : 'text-start' }}  ">
                                                <h6 class="mb-0 px-3">{{ $user->countUsers($workspace_id) }}</h6>
                                                <p class="text-muted text-sm mb-0">{{ __('Users') }}</p>
                                            </div>
                                            <div class="col-6 text-start mt-2">
                                                <h6 class="mb-0 px-3">{{ $user->countClients($workspace_id) }}</h6>
                                                <p class="text-muted text-sm mb-0">{{ __('Clients') }}</p>
                                            </div>
                                        @endif

                                        <div
                                            class="col-6  {{ Auth::user()->type == 'admin' ? 'text-end mt-2' : 'text-start' }} ">
                                            <h6 class="mb-0 px-3">{{ $user->countProject($workspace_id) }}</h6>
                                            <p class="text-muted text-sm mb-0">{{ __('Projects') }}</p>
                                        </div>
                                        @if (Auth::user()->type != 'admin')
                                            <div class="col-6 text-end">
                                                <h6 class="mb-0 px-3">{{ $user->countTask($workspace_id) }}</h6>
                                                <p class="text-muted text-sm mb-0">{{ __('Tasks') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach



        @else
            <div class="container mt-5">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="page-error">
                            <div class="page-inner">
                                <h1>404</h1>
                                <div class="page-description">
                                    {{ __('Page Not Found') }}
                                </div>
                                <div class="page-search">
                                    <p class="text-muted mt-3">
                                        {{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.") }}
                                    </p>
                                    <div class="mt-3">
                                        <a class="btn-return-home badge-blue" href="{{ route('home') }}"><i
                                                class="fas fa-reply"></i> {{ __('Return Home') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    @endif

    </div>
    </div>

    <!-- [ sample-page ] end -->
    </div>
@endsection

@push('scripts')
    <script>
        $(".fc-daygrid-event fc-daygrid-block-event fc-h-event fc-event fc-event-draggable fc-event-resizable fc-event-end fc-event-past bg-danger border-danger")
            .click(function() {
                alert("Handler for .click() called.");
            });
    </script>


    <script type="text/javascript">
        $(".filter").click(function() {
            $("#show_filter").toggleClass('d-none');
        });
    </script>

<script>
    var inputElement = document.querySelector('.tags')
    new Tagify(inputElement)
</script>
@endpush
