@extends('layouts.admin')

@section('page-title')
    {{ __('Project Detail') }}
@endsection
@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item">
            <a href="{{ route('client.home') }}">{{ __('Home') }}</a>
        </li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a
                href="{{ route('client.projects.index', $currentWorkspace->slug) }}">{{ __('Project') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('projects.index', $currentWorkspace->slug) }}">{{ __('Project') }}</a>
        </li>
    @endif
    <li class="breadcrumb-item">{{ $project->name }}</li>
@endsection
@php
    $permissions = Auth::user()->getPermission($project->id);
    $client_keyword = Auth::user()->getGuard() == 'client' ? 'client.' : '';
    $logo = \App\Models\Utility::get_file('users-avatar/');
    $logo_project_files = \App\Models\Utility::get_file('project_files/');
    $logo_tasks=\App\Models\Utility::get_file('tasks/');


@endphp

@section('multiple-action-button')

    @if(
        (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner'))
        <div class="col-md-auto col-sm-4 pb-3">
        <a href="#" class="btn btn-xs btn-primary btn-icon-only col-12" data-toggle="popover"  title="Shared Project Settings" data-ajax-popup="true" data-size="md" data-title="{{ __('Shared Project Settings') }}" data-url="{{route('projects.copylink.setting.create',[$currentWorkspace->slug, $project->id])}}" data-toggle="tooltip" title="{{ __('Add Project') }}">
            <i class="ti ti-settings"></i>
        </a>
        </div>
    @endif

    {{-- <div class="col-md-auto col-sm-4 pb-3">
        <a href="#" class="btn btn-xs btn-primary btn-icon-only col-12 cp_link "
            data-link="{{ route('projects.link', [$currentWorkspace->slug, \Illuminate\Support\Facades\Crypt::encrypt($project->id)]) }}"
            data-toggle="popover"  title="Copy Project"
            ><span
                class=""></span><span class="btn-inner--text text-white"><i
                    class="ti ti-copy"></i></span></a>
        </a>
    </div> --}}
    @if (
        (isset($permissions) && in_array('show timesheet', $permissions)) ||
            (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner'))
        <div class="col-md-auto col-sm-4 pb-3">
            <a href="{{ route($client_keyword . 'projects.timesheet.index', [$currentWorkspace->slug, $project->id]) }}"
                class="btn btn-xs btn-primary btn-icon-only col-12 ">{{ __('Timesheet') }}</a>
        </div>
    @endif
    @if (
        (isset($permissions) && in_array('show gantt', $permissions)) ||
            (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner'))
        <div class="col-md-auto col-sm-4 pb-3">
            <a href="{{ route($client_keyword . 'projects.gantt', [$currentWorkspace->slug, $project->id]) }}"
                class="btn btn-xs btn-primary btn-icon-only col-12 ">{{ __('Gantt Chart') }}</a>
        </div>
    @endif
    @if (
        (isset($permissions) && in_array('show task', $permissions)) ||
            (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner'))
        <div class="col-md-auto col-sm-4 pb-3">
            <a href="{{ route($client_keyword . 'projects.task.board', [$currentWorkspace->slug, $project->id]) }}"
                class="btn btn-xs btn-primary btn-icon-only col-12 ">{{ __('Task Board') }}</a>
        </div>
    @endif
    @if (
        (isset($permissions) && in_array('show bug report', $permissions)) ||
            (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner'))
        {{-- <div class="col-md-auto col-sm-6 pb-3">
            <a href="{{ route($client_keyword . 'projects.bug.report', [$currentWorkspace->slug, $project->id]) }}"
                class="btn btn-xs btn-primary btn-icon-only col-12">{{ __('Bug Report') }}</a>
        </div> --}}
    @endif
    <div class="col-md-auto col-sm-6 pb-3">
        <a href="{{ route($client_keyword . 'projecttime.tracker', [$currentWorkspace->slug, $project->id]) }}"
            class="btn btn-xs btn-primary btn-icon-only col-12 ">{{ __('Tracker') }}</a>
    </div>
@endsection
<style type="text/css">
    .fix_img {
        width: 40px !important;
        border-radius: 50%;
    }

    @media (max-width: 1300px) {
        .header_breadcrumb {
            width: 100% !important;
        }

        .row1 {
            display: flex;
            flex-wrap: wrap;
        }
    }
</style>

@section('content')
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xxl-12">
                    <div class="card bg-primary">
                        <div class="card-body">
                            <div class="d-block d-sm-flex align-items-center justify-content-between">
                                <h4 class="text-white"> {{ $project->name }}</h4>
                                <div class="d-flex  align-items-center row1">
                                    <div class="px-3">
                                        <span class="text-white text-sm">{{ __('Start Date') }}:</span>
                                        <h5 class="text-white text-nowrap">
                                            {{ App\Models\Utility::dateFormat($project->start_date) }}</h5>
                                    </div>
                                    <div class="px-3">
                                        <span class="text-white text-sm">{{ __('Due Date') }}:</span>
                                        <h5 class="text-white">{{ App\Models\Utility::dateFormat($project->end_date) }}
                                        </h5>
                                    </div>
                                    <div class="px-3">
                                        <span class="text-white text-sm">{{ __('Total Members') }}:</span>
                                        <h5 class="text-white text-nowrap">
                                            {{ (int) $project->users->count() + (int) $project->clients->count() }}</h5>
                                    </div>
                                    <div class="px-3">

                                        @if ($project->status == 'Finished')
                                            <div class="badge  bg-success p-2 px-3 rounded"> {{ __('Finished') }}
                                            </div>
                                        @elseif($project->status == 'Ongoing')
                                            <div class="badge  bg-secondary p-2 px-3 rounded">{{ __('Ongoing') }}</div>
                                        @else
                                            <div class="badge bg-warning p-2 px-3 rounded">{{ __('OnHold') }}</div>
                                        @endif

                                    </div>
                                </div>

                                @if (!$project->is_active)
                                    <button class="btn btn-light d"> <a href="#" class=""
                                            title="{{ __('Locked') }}">
                                            <i data-feather="lock"> </i>
                                        </a></button>
                                @else
                                    @auth('web')
                                        @if ($currentWorkspace->permission == 'Owner')
                                            <div class="d-flex align-items-center ">
                                                <button class="btn btn-light d-flex align-items-between me-3">
                                                    <a href="#" class=""
                                                        data-url="{{ route('projects.edit', [$currentWorkspace->slug, $project->id]) }}"
                                                        data-ajax-popup="true" data-title="{{ __('Edit Project') }}"
                                                        data-toggle="popover" title="{{ __('Edit') }}">
                                                        <i class="ti ti-edit"> </i>
                                                    </a>
                                                </button>
                                                <button class="btn btn-light d"><a href="#" class="bs-pass-para"
                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $project->id }}"
                                                        data-toggle="popover" title="{{ __('Delete') }}"><i
                                                            class="ti ti-trash"> </i></a></button>

                                            </div>
                                            <form id="delete-form-{{ $project->id }}"
                                                action="{{ route('projects.destroy', [$currentWorkspace->slug, $project->id]) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @else
                                            <button class="btn btn-light d"> <a href="#" class="bs-pass-para"
                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                    data-toggle="popover" title="{{ __('Delete') }}"
                                                    data-confirm-yes="leave-form-{{ $project->id }}"><i class="ti ti-trash">
                                                    </i> </i></a></button>

                                            <form id="leave-form-{{ $project->id }}"
                                                action="{{ route('projects.leave', [$currentWorkspace->slug, $project->id]) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    @endauth
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="theme-avtar bg-primary">
                                            <i class="fas fas fa-calendar-day"></i>
                                        </div>
                                        <div class="col text-end">
                                            <h6 class="text-muted mb-1">{{ __('Days left') }}</h6>
                                            <span class="h6 font-weight-bold mb-0 ">{{ $daysleft }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-lg-3 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="theme-avtar bg-info">
                                            <i class="fas fa-money-bill-alt"></i>
                                        </div>
                                        <div class="col text-end">
                                            <h6 class="text-muted mb-1">{{ __('Budget') }}</h6>
                                            <span
                                                class="h6 font-weight-bold mb-0 ">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}{{ number_format($project->budget) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <div class="col-lg-3 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="theme-avtar bg-danger">
                                            <i class="fas fa-check-double"></i>
                                        </div>
                                        <div class="col text-end">
                                            <h6 class="text-muted mb-1">{{ __('Total Task') }}</h6>
                                            <span class="h6 font-weight-bold mb-0 ">{{ $project->countTask() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="theme-avtar bg-success">
                                            <i class="fas fa-comments"></i>
                                        </div>
                                        <div class="col text-end">
                                            <h6 class="text-muted mb-1">{{ __('Comment') }}</h6>
                                            <span
                                                class="h6 font-weight-bold mb-0 ">{{ $project->countTaskComments() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col text-left">
                                            <h6 class="text-muted mb-1">{{ __('Tags') }}</h6>
                                        </div>
                                        <div class="col-9">
                                            @isset($tags)
                                            @foreach ($tags as $tag)
                                            {{-- <h6 class="text-muted mb-1"> --}}
                                                <span class="badge bg-primary p-2 mb-1 rounded">{{ __($tag) }}</span>
                                                {{-- </h6> --}}
                                            @endforeach
                                            @endisset
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card ">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">{{ __('Team Members') }} ({{ count($project->users) }})
                                            </h5>
                                        </div>

                                        <div class="float-end">
                                            <p class="text-muted d-sm-flex align-items-center mb-0">
                                                @if ($currentWorkspace->permission == 'Owner')
                                                    <a href="#" class="btn btn-sm btn-primary "
                                                        data-ajax-popup="true" data-title="{{ __('Invite') }}"
                                                        data-toggle="popover" title="{{ __('Invite') }}"
                                                        data-url="{{ route('projects.invite.popup', [$currentWorkspace->slug, $project->id]) }}"><i
                                                            class="ti ti-brand-telegram"></i></a>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="px-3 top-10-scroll" style="max-height: 300px;">
                                        @foreach ($project->users as $user)
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item px-0">
                                                    <div class="row align-items-center justify-content-between">
                                                        <div class="col-sm-auto mb-3 mb-sm-0">
                                                            <div class="d-flex align-items-center px-2">
                                                                <a href="#" class=" text-start">
                                                                    <img class="fix_img"
                                                                        @if ($user->avatar) src="{{ asset($logo . $user->avatar) }}" @else avatar="{{ $user->name }}" @endif>
                                                                </a>
                                                                <div class="px-2">
                                                                    <h5 class="m-0">{{ $user->name }}</h5>
                                                                    <small class="text-muted">{{ $user->email }}<span
                                                                            class="text-primary "> -
                                                                            {{ (int) count($project->user_done_tasks($user->id)) }}/{{ (int) count($project->user_tasks($user->id)) }}</span></small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-auto text-sm-end d-flex align-items-center">
                                                            @auth('web')
                                                                @if ($currentWorkspace->permission == 'Owner' && $user->id != Auth::id())
                                                                    <a href="#"
                                                                        class="action-btn btn-primary mx-1  btn btn-sm d-inline-flex align-items-center"
                                                                        data-ajax-popup="true" data-size="lg"
                                                                        data-toggle="popover" title="{{ __('Permission') }}"
                                                                        data-title="{{ __('Edit Permission') }}"
                                                                        data-url="{{ route('projects.user.permission', [$currentWorkspace->slug, $project->id, $user->id]) }}"><i
                                                                            class="ti ti-lock"></i></a>

                                                                    <a href="#"
                                                                        class="action-btn btn-danger btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                        data-toggle="popover" title="{{ __('Delete') }}"
                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                        data-confirm-yes="delete-user-{{ $user->id }}"><i
                                                                            class="ti ti-trash ml-1"></i></a>
                                                                    <form id="delete-user-{{ $user->id }}"
                                                                        action="{{ route('projects.user.delete', [$currentWorkspace->slug, $project->id, $user->id]) }}"
                                                                        method="POST" style="display: none;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                @endif
                                                            @endauth
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="card">

                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">{{ __('Clients') }} ({{ count($project->clients) }})</h5>
                                        </div>

                                        <div class="float-end">
                                            <p class="text-muted d-none d-sm-flex align-items-center mb-0">
                                                @if ($currentWorkspace->permission == 'Owner')
                                                    <a href="#" class="btn btn-sm btn-primary"
                                                        data-ajax-popup="true" data-title="{{ __('Share to Client') }}"
                                                        data-toggle="popover" title="{{ __('Share to Client') }}"
                                                        data-url="{{ route('projects.share.popup', [$currentWorkspace->slug, $project->id]) }}"><i
                                                            class="ti ti-share"></i></a>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class=" px-3 top-10-scroll" style="max-height: 300px;">
                                        @foreach ($project->clients as $client)
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item px-0">
                                                    <div class="row align-items-center justify-content-between">
                                                        <div class="col-sm-auto mb-3 mb-sm-0">
                                                            <div class="d-flex align-items-center px-2">
                                                                <a href="#" class=" text-start">
                                                                    <img class="fix_img"
                                                                        @if ($client->avatar) src="{{ asset($logo . $client->avatar) }}" @else avatar="{{ $client->name }}" @endif>
                                                                </a>
                                                                <div class="px-2">
                                                                    <h5 class="m-0">{{ $client->name }}</h5>
                                                                    <small class="text-muted">{{ $client->email }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-auto text-sm-end d-flex align-items-center">
                                                            @auth('web')
                                                                @if ($currentWorkspace->permission == 'Owner')
                                                                    <a href="#"
                                                                        class="action-btn btn-primary mx-1  btn btn-sm d-inline-flex align-items-center"
                                                                        data-toggle="popover" title="{{ __('Permission') }}"
                                                                        data-ajax-popup="true" data-size="lg"
                                                                        data-title="{{ __('Edit Permission') }}"
                                                                        data-url="{{ route('projects.client.permission', [$currentWorkspace->slug, $project->id, $client->id]) }}"><i
                                                                            class="ti ti-lock"></i></a>

                                                                    <a href="#"
                                                                        class="action-btn btn-danger mx-1  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                        data-toggle="popover" title="{{ __('Delete') }}"
                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                        data-confirm-yes="delete-client-{{ $client->id }}"><i
                                                                            class="ti ti-trash"></i></a>

                                                                    <form id="delete-client-{{ $client->id }}"
                                                                        action="{{ route('projects.client.delete', [$currentWorkspace->slug, $project->id, $client->id]) }}"
                                                                        method="POST" style="display: none;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                @endif
                                                            @endauth
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card ">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">{{ __('Tasks') }}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <app :tasks='{{ json_encode($project->getTasksWithSubTasks()) }}'></app>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-xxl-12">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header" style="padding: 25px 35px !important;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="row">
                                            <h5 class="mb-0">{{ __('Progress') }}<span class="text-end"> (Last Week
                                                    Tasks) </span></h5>

                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
                                    </div>
                                </div>
                                <div id="task-chart"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">{{ __('Activity') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="timeline timeline-one-side top-10-scroll" data-timeline-content="axis"
                                data-timeline-axis-style="dashed">
                                @if ((isset($permissions) && in_array('show activity', $permissions)) || $currentWorkspace->permission == 'Owner')
                                    @foreach ($project->activities as $activity)
                                        <div class="timeline-block px-2 pt-3">
                                            @if ($activity->log_type == 'Upload File')
                                                <span
                                                    class="timeline-step timeline-step-sm border border-primary text-white">
                                                    <i class="fas fa-file"></i></span>
                                            @elseif($activity->log_type == 'Create Milestone')
                                                <span class="timeline-step timeline-step-sm border border-info text-white">
                                                    <i class="fas fa-cubes"></i></span>
                                            @elseif($activity->log_type == 'Create Task')
                                                <span
                                                    class="timeline-step timeline-step-sm border border-success text-white">
                                                    <i class="fas fa-tasks"></i></span>
                                            @elseif($activity->log_type == 'Create Bug')
                                                <span
                                                    class="timeline-step timeline-step-sm border border-warning text-white">
                                                    <i class="fas fa-bug"></i></span>
                                            @elseif($activity->log_type == 'Move' || $activity->log_type == 'Move Bug')
                                                <span
                                                    class="timeline-step timeline-step-sm border round border-danger text-white">
                                                    <i class="fas fa-align-justify"></i></span>
                                            @elseif($activity->log_type == 'Create Invoice')
                                                <span
                                                    class="timeline-step timeline-step-sm border border-bg-dark text-white">
                                                    <i class="fas fa-file-invoice"></i></span>
                                            @elseif($activity->log_type == 'Invite User')
                                                <span
                                                    class="timeline-step timeline-step-sm border border-success text-white">
                                                    <i class="fas fa-plus"></i></span>
                                            @elseif($activity->log_type == 'Share with Client')
                                                <span class="timeline-step timeline-step-sm border border-info text-white">
                                                    <i class="fas fa-share"></i></span>
                                            @elseif($activity->log_type == 'Create Timesheet')
                                                <span
                                                    class="timeline-step timeline-step-sm border border-success text-white">
                                                    <i class="fas fa-clock-o"></i></span>
                                            @endif

                                            <div class="last_notification_text">
                                                <!--   <p class="m-0"> <span>{{ $activity->log_type }} </span> </p> <br> -->
                                                <p class="m-0"> <span>{{ $activity->logType($activity->log_type) }}
                                                    </span> </p> <br>
                                                <p> {!! $activity->getRemark() !!} </p>
                                                <div class="notification_time_main">
                                                    <p>{{ $activity->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>

                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-lg-8">
                    @if ((isset($permissions) && in_array('show milestone', $permissions)) || $currentWorkspace->permission == 'Owner')
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">{{ __('Milestones') }} ({{ count($project->milestones) }})
                                        </h5>
                                    </div>
                                    <div class="float-end">
                                        @if ((isset($permissions) && in_array('create milestone', $permissions)) || $currentWorkspace->permission == 'Owner')
                                            <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true"
                                                data-title="{{ __('Create Milestone') }}"
                                                data-url="{{ route($client_keyword . 'projects.milestone', [$currentWorkspace->slug, $project->id]) }}"
                                                data-toggle="popover" title="{{ __('Create') }}"><i
                                                    class="ti ti-plus"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="" class="table table-bordered px-2">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Start Date') }}</th>
                                                <th>{{ __('End Date') }}</th>
                                                <th>{{ __('Cost') }}</th>
                                                <th>{{ __('Progress') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($project->milestones as $key => $milestone)
                                                <tr>
                                                    <td><a href="#" class="d-block font-weight-500 mb-0"
                                                            data-ajax-popup="true"
                                                            data-title="{{ __('Milestone Details') }}"
                                                            data-url="{{ route($client_keyword . 'projects.milestone.show', [$currentWorkspace->slug, $milestone->id]) }}">
                                                            <h5 class="m-0"> {{ $milestone->title }} </h5>
                                                        </a></td>
                                                    <td>
                                                        @if ($milestone->status == 'complete')
                                                            <label
                                                                class="badge bg-success p-2 px-3 rounded">{{ __('Complete') }}</label>
                                                        @else
                                                            <label
                                                                class="badge bg-warning p-2 px-3 rounded">{{ __('Incomplete') }}</label>
                                                        @endif
                                                    </td>
                                                    <td>{{ $milestone->start_date }}</td>
                                                    <td>{{ $milestone->end_date }}</td>
                                                    <td>{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}{{ $milestone->cost }}
                                                    </td>
                                                    <td>
                                                        <div class="progress_wrapper">
                                                            <div class="progress">
                                                                <div class="progress-bar" role="progressbar"
                                                                    style="width: {{ $milestone->progress }}%;"
                                                                    aria-valuenow="55" aria-valuemin="0"
                                                                    aria-valuemax="100"></div>
                                                            </div>
                                                            <div class="progress_labels">
                                                                <div class="total_progress">

                                                                    <strong> {{ $milestone->progress }}%</strong>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="text-right">
                                                        <div class="col-auto">
                                                            @if ($currentWorkspace->permission == 'Owner')
                                                                <a href="#"
                                                                    class="action-btn btn-info mx-1  btn btn-sm d-inline-flex align-items-center"
                                                                    data-ajax-popup="true" data-size="lg"
                                                                    data-toggle="popover" title="{{ __('Edit') }}"
                                                                    data-title="{{ __('Edit Milestone') }}"
                                                                    data-url="{{ route('projects.milestone.edit', [$currentWorkspace->slug, $milestone->id]) }}"><i
                                                                        class="ti ti-edit"></i></a>
                                                                <a href="#"
                                                                    class="action-btn btn-danger mx-1  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                    data-toggle="popover" title="{{ __('Delete') }}"
                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                    data-confirm-yes="delete-form1-{{ $milestone->id }}"><i
                                                                        class="ti ti-trash"></i></a>
                                                                <form id="delete-form1-{{ $milestone->id }}"
                                                                    action="{{ route('projects.milestone.destroy', [$currentWorkspace->slug, $milestone->id]) }}"
                                                                    method="POST" style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                </form>
                                                            @elseif(isset($permissions))
                                                                @if (in_array('edit milestone', $permissions))
                                                                    <a href="#"
                                                                        class="action-btn btn-info mx-1  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                        data-ajax-popup="true" data-size="lg"
                                                                        data-title="{{ __('Edit Milestone') }}"
                                                                        data-toggle="popover"
                                                                        title="{{ __('Edit') }}"
                                                                        data-url="{{ route($client_keyword . 'projects.milestone.edit', [$currentWorkspace->slug, $milestone->id]) }}"><i
                                                                            class="ti ti-edit"></i></a>
                                                                @endif
                                                                @if (in_array('delete milestone', $permissions))
                                                                    <a href="#"
                                                                        class="action-btn btn-danger mx-1  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                        data-toggle="popover"
                                                                        title="{{ __('Delete') }}"
                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                        data-confirm-yes="delete-form1-{{ $milestone->id }}"><i
                                                                            class="ti ti-trash"></i></a>
                                                                    <form id="delete-form1-{{ $milestone->id }}"
                                                                        action="{{ route($client_keyword . 'projects.milestone.destroy', [$currentWorkspace->slug, $milestone->id]) }}"
                                                                        method="POST" style="display: none;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    @endif
                </div> --}}
                @if (
                    (isset($permissions) && in_array('show uploading', $permissions)) ||
                        $currentWorkspace->permission == 'Owner' ||
                        $currentWorkspace->permission == 'Member')
                    <div class="col-lg-12">
                        <div class="card ">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0"> {{ __('Files') }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">

                                <div class="author-box-name form-control-label mb-4">

                                </div>
                                <div class="col-md-12 dropzone browse-file" id="dropzonewidget">
                                    <div class="dz-message" data-dz-message>
                                        <span>
                                            @if (Auth::user()->getGuard() == 'client')
                                                {{ __('No files available') }}
                                            @else
                                                {{ __('Drop files here to upload') }}
                                            @endif
                                        </span>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <!-- [ sample-page ] end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
@endsection

{{-- @dd(filesize('storage/logo/1_logo-light.png' )) --}}

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/custom/css/dropzone.min.css') }}">
@endpush
@push('scripts')
    <!--
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

         -->
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script>
        (function() {
            var options = {
                chart: {
                    type: 'area',
                    height: 60,
                    sparkline: {
                        enabled: true,
                    },
                },
                colors: {!! json_encode($chartData['color']) !!},
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2,
                },
                series: [
                    @foreach ($chartData['stages'] as $id => $name)
                        {
                            name: "{{ __($name) }}",
                            // data:
                            data: {!! json_encode($chartData[$id]) !!},
                        },
                    @endforeach
                ],
                xaxis: {
                    type: "category",
                    categories: {!! json_encode($chartData['label']) !!},
                    title: {
                        text: '{{ __('Days') }}'
                    },
                    tooltip: {
                        enabled: false,
                    }
                },
                yaxis: {
                    show: true,
                    position: "left",
                    title: {
                        text: '{{ __('Tasks') }}'
                    },
                },
                grid: {
                    show: true,
                    borderColor: "#EBEBEB",
                    strokeDashArray: 0,
                    position: "back",
                    xaxis: {
                        show: true,
                        lines: {
                            show: true,
                        },
                    },
                    yaxis: {
                        show: false,
                        lines: {
                            show: false,
                        },
                    },
                    row: {
                        colors: undefined,
                        opacity: 0.5,
                    },
                    column: {
                        position: "back",
                        colors: undefined,
                        opacity: 0.5,
                    },
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0,
                    },
                },
                tooltip: {
                    followCursor: false,
                    fixed: {
                        enabled: false
                    },
                    x: {
                        format: 'dd/MM/yy HH:mm'
                    },

                    marker: {
                        show: false
                    }
                }
            }
            var chart = new ApexCharts(document.querySelector("#task-chart"), options);
            chart.render();
        })();
    </script>
    <script>
        $(document).ready(function() {
            if ($(".top-10-scroll").length) {
                $(".top-10-scroll").css({
                    "max-height": 300
                }).niceScroll();
            }
        });
    </script>
    <script src="{{ asset('assets/custom/js/dropzone.min.js') }}"></script>
    <script>
        Dropzone.autoDiscover = false;
        myDropzone = new Dropzone("#dropzonewidget", {
            maxFiles: 20,
            // maxFilesize: 209715200,
            parallelUploads: 1,
            //acceptedFiles: ".jpeg,.jpg,.png,.gif,.svg,.pdf,.txt,.doc,.docx,.zip,.rar",
            url: "{{ route('projects.file.upload', [$currentWorkspace->slug, $project->id]) }}",
            success: function(file, response) {
                if (response.is_success) {
                    dropzoneBtn(file, response);
                    show_toastr('{{ __('Success') }}', 'File Successfully Uploaded', 'success');
                } else {
                    myDropzone.removeFile(file);
                    // show_toastr('error', 'File type must be match with Storage setting.');
                    show_toastr('{{ __('Error') }}',
                        'File type and size must be match with Storage setting.', 'error');
                }
            },
            error: function(file, response) {
                myDropzone.removeFile(file);
                if (response.error) {
                    show_toastr('{{ __('Error') }}',
                        'File type and size must be match with Storage setting.', 'error');
                } else {
                    show_toastr('{{ __('Error') }}',
                        'File type and size must be match with Storage setting.', 'error');
                }
            }
        });

        myDropzone.on("sending", function(file, xhr, formData) {
            formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            formData.append("project_id", {{ $project->id }});
        });

        @if (isset($permisions) && in_array('show uploading', $permisions))
            $(".dz-hidden-input").prop("disabled", true);
            myDropzone.removeEventListeners();
        @endif

        function dropzoneBtn(file, response) {

            var html = document.createElement('span');
            var download = document.createElement('a');
            download.setAttribute('href', response.download);
            download.setAttribute('class', "action-btn btn-primary mx-1  btn btn-sm d-inline-flex align-items-center");
            download.setAttribute('data-toggle', "popover");
            download.setAttribute('download', "");
            download.setAttribute('title', "{{ __('Download') }}");
            // download.innerHTML = "<i class='fas fa-download mt-2'></i>";
            download.innerHTML = "<i class='ti ti-download'> </i>";
            html.appendChild(download);

            @if (isset($permisions) && in_array('show uploading', $permisions))
            @else
                var del = document.createElement('a');
                del.setAttribute('href', response.delete);
                del.setAttribute('class', "action-btn btn-danger mx-1  btn btn-sm d-inline-flex align-items-center");
                del.setAttribute('data-toggle', "popover");
                del.setAttribute('title', "{{ __('Delete') }}");
                del.innerHTML = "<i class='ti ti-trash '></i>";

                del.addEventListener("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (confirm("Are you sure ?")) {
                        var btn = $(this);
                        $.ajax({
                            url: btn.attr('href'),
                            type: 'DELETE',
                            success: function(response) {
                                if (response.is_success) {
                                    btn.closest('.dz-image-preview').remove();
                                    show_toastr('{{ __('Success') }}', 'File Successfully Deleted',
                                        'success');
                                } else {
                                    show_toastr('{{ __('Error') }}', 'Something Wents Wrong.',
                                        'error');
                                }
                            },
                            error: function(response) {
                                response = response.responseJSON;
                                if (response.is_success) {
                                    show_toastr('{{ __('Error') }}', 'Something Wents Wrong.',
                                        'error');
                                } else {
                                    show_toastr('{{ __('Error') }}', 'Something Wents Wrong.',
                                        'error');
                                }
                            }
                        })
                    }
                });
                html.appendChild(del);
            @endif

            file.previewTemplate.appendChild(html);
        }
        @php($setting = App\Models\Utility::getAdminPaymentSettings())

        @php($files = $project->files)
        @foreach ($files as $file)
            @php($storage_file = asset($logo_project_files . $file->file_path))

            // Create the mock file:
            @if (Storage::disk($setting['storage_setting'])->exists('/project_files/' . $file->file_path))

                var mockFile = {
                    name: "{{ $file->file_name }}",
                    size: {{ filesize('storage/project_files/' . $file->file_path) }}
                };
            @endif
            // Call the default addedfile event handler
            myDropzone.emit("addedfile", mockFile);
            // And optionally show the thumbnail of the file:
            myDropzone.emit("thumbnail", mockFile, "{{ asset($logo_project_files . $file->file_path) }}");
            myDropzone.emit("complete", mockFile);

            dropzoneBtn(mockFile, {
                download: "{{ route($client_keyword . 'projects.file.download', [$currentWorkspace->slug, $project->id, $file->id]) }}",
                delete: "{{ route($client_keyword . 'projects.file.delete', [$currentWorkspace->slug, $project->id, $file->id]) }}"
            });
        @endforeach
    </script>
@endpush

@push('scripts')
<!-- third party js -->
<script src="{{ asset('assets/custom/js/dragula.min.js') }}"></script>
<script>
    !function (a) {
        "use strict";
        var t = function () {
            this.$body = a("body")
        };
        t.prototype.init = function () {
            a('[data-toggle="dragula"]').each(function () {
                var t = a(this).data("containers"), n = [];
                if (t) for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]); else n = [a(this)[0]];
                var r = a(this).data("handleclass");
                r ? dragula(n, {
                    moves: function (a, t, n) {
                        return n.classList.contains(r)
                    }
                }) : dragula(n).on('drop', function (el, target, source, sibling) {
                    var sort = [];
                    $("#" + target.id + " > div").each(function (key) {
                        sort[key] = $(this).attr('id');
                    });
                    var id = el.id;
                    var old_status = $("#" + source.id).data('status');
                    var new_status = $("#" + target.id).data('status');
                    var project_id = '{{$project->id}}';

                    $("#" + source.id).parents('.card-list').find('.count').text($("#" + source.id + " > div").length);
                    $("#" + target.id).parents('.card-list').find('.count').text($("#" + target.id + " > div").length);
                    $.ajax({
                        url: '{{route($client_keyword.'tasks.update.order',[$currentWorkspace->slug,$project->id])}}',
                        type: 'POST',
                        data: {
                            id: id,
                            sort: sort,
                            new_status: new_status,
                            old_status: old_status,
                            project_id: project_id,
                        },
                        success: function (data) {
                            // console.log(data);
                        }
                    });
                });
            })
        }, a.Dragula = new t, a.Dragula.Constructor = t
    }(window.jQuery), function (a) {
        "use strict";
        @if((isset($permissions) && in_array('move task',$permissions)) || ($currentWorkspace && $currentWorkspace->permission == 'Owner'))
            a.Dragula.init();
        @endif
    }(window.jQuery);
</script>
<!-- third party js ends -->
<script>
    $(document).on('click', '#form-comment button', function (e) {
        var comment = $.trim($("#form-comment textarea[name='comment']").val());
        if (comment != '') {
            $.ajax({
                url: $("#form-comment").data('action'),
                data: {comment: comment},
                type: 'POST',
                success: function (data) {
                    data = JSON.parse(data);

                    if (data.user_type == 'Client') {
                        var avatar = "avatar='" + data.client.name + "'";
                        var html = "<li class='media border-bottom mb-3'>" +
                            "                    <img class='mr-3 avatar-sm rounded-circle img-thumbnail hight_img' width='60' " + avatar + " alt='" + data.client.name + "'>" +
                            "                    <div class='media-body mb-2'>" +
                                "                    <div class='float-left'>" +
                                "                        <h5 class='mt-0 mb-1 form-control-label'>" + data.client.name + "</h5>" +
                                "                        " + data.comment +
                                "                    </div>" +
                            "                    </div>" +
                            "                </li>";
                    } else {
                        var avatar = (data.user.avatar) ? "src='{{$logo}}/" + data.user.avatar + "'" : "avatar='" + data.user.name + "'";
                        var html = "<li class='media border-bottom mb-3'>" +
                            "                    <img class='mr-3 avatar-sm rounded-circle img-thumbnail hight_img ' width='60' " + avatar + " alt='" + data.user.name + "'>" +
                            "                    <div class='media-body mb-2'>" +
                                "                    <div class='float-left'>" +
                            "                        <h5 class='mt-0 mb-1 form-control-label'>" + data.user.name + "</h5>" +
                            "                        " + data.comment +
                            "                           </div>" +
                            "                           <div class='text-end'>" +
                            "                               <a href='#' class='delete-icon action-btn btn-danger  btn btn-sm d-inline-flex align-items-center delete-comment' data-url='" + data.deleteUrl + "'>" +
                            "                                   <i class='ti ti-trash'></i>" +
                            "                               </a>" +
                            "                           </div>" +
                            "                    </div>" +
                            "                </li>";
                    }

                    $("#task-comments").prepend(html);
                    LetterAvatar.transform();
                    $("#form-comment textarea[name='comment']").val('');
                    show_toastr('{{__('Success')}}', '{{ __("Comment Added Successfully!")}}', 'success');
                },
                error: function (data) {
                    show_toastr('{{__('Error')}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                }
            });
        } else {
            show_toastr('{{__('Error')}}', '{{ __("Please write comment!")}}', 'error');
        }
    });
    $(document).on("click", ".delete-comment", function () {
        if (confirm('{{__('Are you sure ?')}}')) {
            var btn = $(this);
            $.ajax({
                url: $(this).attr('data-url'),
                type: 'DELETE',
                dataType: 'JSON',
                success: function (data) {
                    show_toastr('{{__('Success')}}', '{{ __("Comment Deleted Successfully!")}}', 'success');
                    btn.closest('.media').remove();
                },
                error: function (data) {
                    data = data.responseJSON;
                    if (data.message) {
                        show_toastr('{{__('Error')}}', data.message, 'error');
                    } else {
                        show_toastr('{{__('Error')}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                    }
                }
            });
        }
    });
    $(document).on('click', '#form-subtask button', function (e) {
        e.preventDefault();

        var name = $.trim($("#form-subtask input[name=name]").val());
        var due_date = $.trim($("#form-subtask input[name=due_date]").val());
        if (name == '' || due_date == '') {
            show_toastr('{{__('Error')}}', '{{ __("Please enter fields!")}}', 'error');
            return false;
        }

        $.ajax({
            url: $("#form-subtask").data('action'),
            type: 'POST',
            data: {
                name: name,
                due_date: due_date,
            },
            dataType: 'JSON',
            success: function (data) {
                show_toastr('{{__('Success')}}', '{{ __("Sub Task Added Successfully!")}}', 'success');

                var html = '<li class="list-group-item py-3">' +
                    '    <div class="form-check form-switch d-inline-block">' +
                    '        <input type="checkbox" class="form-check-input" name="option" id="option' + data.id + '" value="' + data.id + '" data-url="' + data.updateUrl + '">' +
                    '        <label class="custom-control-label form-control-label" for="option' + data.id + '">' + data.name +'</label>' +
                    '    </div>' +
                    '    <div class="text-end">' +
                    '        <a href="#" class=" action-btn btn-danger  btn btn-sm d-inline-flex align-items-center  delete-icon delete-subtask" data-url="' + data.deleteUrl + '">' +
                    '            <i class="ti ti-trash"></i>' +
                    '        </a>' +
                    '    </div>' +
                    '</li>';

                $("#subtasks").prepend(html);
                $("#form-subtask input[name=name]").val('');
                $("#form-subtask input[name=due_date]").val('');
                $("#form-subtask").collapse('toggle');
            },
            error: function (data) {
                data = data.responseJSON;
                if (data.message) {
                    show_toastr('{{__('Error')}}', data.message, 'error');
                    $('#file-error').text(data.errors.file[0]).show();
                } else {
                    show_toastr('{{__('Error')}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                }
            }
        });
    });
    $(document).on("change", "#subtasks input[type=checkbox]", function () {
        $.ajax({
            url: $(this).attr('data-url'),
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
                show_toastr('{{__('Success')}}', '{{ __("Subtask Updated Successfully!")}}', 'success');
                // console.log(data);
            },
            error: function (data) {
                data = data.responseJSON;
                if (data.message) {
                    show_toastr('{{__('Error')}}', data.message, 'error');
                } else {
                    show_toastr('{{__('Error')}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                }
            }
        });
    });
    $(document).on("click", ".delete-subtask", function () {
        // if (confirm('{{__('Are you sure ?')}}')) {
            var btn = $(this);
            $.ajax({
                url: $(this).attr('data-url'),
                type: 'DELETE',
                dataType: 'JSON',
                success: function (data) {
                    show_toastr('{{__('Success')}}', '{{ __("Subtask Deleted Successfully!")}}', 'success');
                    btn.closest('.list-group-item').remove();
                },
                error: function (data) {
                    data = data.responseJSON;
                    if (data.message) {
                        show_toastr('{{__('Error')}}', data.message, 'error');
                    } else {
                        show_toastr('{{__('Error')}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                    }
                }
            });
        // }
    });
    // $("#form-file").submit(function(e){
   $(document).on('submit', '#form-file', function (e) {
        e.preventDefault();

        $.ajax({
            url: $("#form-file").data('url'),
            type: 'POST',
            data: new FormData(this),
            dataType: 'JSON',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                show_toastr('{{__('Success')}}', '{{ __("File Upload Successfully!")}}', 'success');
                // console.log(data);
                var delLink = '';

                if (data.deleteUrl.length > 0) {
                    delLink = "<a href='#' class=' action-btn btn-danger  btn btn-sm d-inline-flex align-items-center  delete-icon delete-comment-file'  data-url='" + data.deleteUrl + "'>" +
                        "                                        <i class='ti ti-trash'></i>" +
                        "                                    </a>";
                }

                var html = "<div class='card mb-1 shadow-none border'>" +

                    "                        <div class='card-body p-3'>" +

                    "                            <div class='row align-items-center'>" +

                    "                                <div class='col-auto'>" +

                    "                                    <div class='avatar-sm'>" +

                    "                                        <span class='avatar-title rounded text-uppercase'>" +
                     "<img class='preview_img_size' " + "src='{{$logo_tasks}}/" + data.file + "'>"
                   +
                    "                                        </span>" +
                    "                                    </div>" +
                    "                                </div>" +
                    "                                <div class='col pl-0'>" +
                    "                                    <a href='#' class='text-muted form-control-label'>" + data.name + "</a>" +
                    "                                    <p class='mb-0'>" + data.file_size + "</p>" +
                    "                                </div>" +
                    "                                <div class='col-auto'>" +
                    "                                    <a download href='{{$logo_tasks}}/" + data.file + "' class='edit-icon action-btn btn-primary  btn btn-sm d-inline-flex align-items-center'>" +
                    "                                        <i class='ti ti-download'></i>" +
                    "                                    </a>" +


                    "                                   <a  href='{{$logo_tasks}}/" + data.file + "' class='edit-icon action-btn btn-secondary  btn btn-sm d-inline-flex align-items-center mx-1'>" +
                    "                                        <i class='ti ti-crosshair text-white'></i>" +
                    "                                    </a>" +









                    delLink +
                    "                                </div>" +
                    "                            </div>" +
                    "                        </div>" +
                    "                    </div>";
                $("#comments-file").prepend(html);
            },
            error: function (data) {
                data = data.responseJSON;

                if (data) {
                     show_toastr('{{__('Error')}}', 'File type and size must be match with Storage setting.', 'error');
                    //show_toastr('{{__('Error')}}', data.message, 'error');
                    $('#file-error').text(data.errors.file[0]).show();
                } else {
                   show_toastr('{{__('Error')}}', 'File type and size must be match with Storage setting.', 'error');
                }
            }
        });
    });
    $(document).on("click", ".delete-comment-file", function () {
        if (confirm('{{__('Are you sure ?')}}')) {
            var btn = $(this);
            $.ajax({
                url: $(this).attr('data-url'),
                type: 'DELETE',
                dataType: 'JSON',
                success: function (data) {
                    show_toastr('{{__('Success')}}', '{{ __("File Deleted Successfully!")}}', 'success');
                    btn.closest('.border').remove();
                },
                error: function (data) {
                    data = data.responseJSON;
                    if (data.message) {
                        show_toastr('{{__('Error')}}', data.message, 'error');
                    } else {
                        show_toastr('{{__('Error')}}', '{{ __("Some Thing Is Wrong!")}}', 'error');
                    }
                }
            });
        }
    });
</script>
@endpush


