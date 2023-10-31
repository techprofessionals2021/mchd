@extends('layouts.admin')
<style type="text/css">
    .modal-body {
        background: #ffffff !important;
        padding: 25px !important;
    }

    @media (max-width: 576px) {
        .header_breadcrumb {
            width: 100% !important;
        }
    }

    .accept-icon {
        width: 30px !important;
        height: 30px !important;
    }
</style>
@section('page-title')
    {{ __('Calendar') }}
@endsection
@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('Calendar') }}</li>
@endsection

@php
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');
@endphp

@section('multiple-action-button')
    {{-- <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-8 pt-lg-3 pt-xl-2">
        <div class=" form-group col-auto">
            <select class="  form-select select2" id="project_id" onchange="get_data()">
                <option value="">{{ __('All Projects') }}</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @if ($project_id == $project->id) selected @endif>
                        {{ $project->name }} </option>
                @endforeach
            </select>
        </div>
    </div> --}}
@endsection
@section('content')
    <div class="row">
        <!-- [ sample-page] start -->
        <div class="col-lg-9">
            <div class="card">

                <div class="card-body tasks-body"
                    style="display:block;
               transform-origin: top;
               transition: transform .4s ease-in-out;">


                    {{-- <calender></calender> --}}
                    <qalendar :users='{{ json_encode($WSUsers) }}' :meetings='{{ json_encode($meetingCollection) }}'>
                    </qalendar>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">{{ __('New Meetings') }}</h4>
                    <ul class="event-cards list-group list-group-flush mt-3 w-100">
                        @php
                            $date = Carbon\Carbon::now()->format('m');
                            $date1 = Carbon\Carbon::now()->format('y');
                            $this_month_task = App\Models\project::where('workspace', $currentWorkspace->id)->get();
                        @endphp
                        @foreach ($this_month_task as $task)
                            @php
                                $task_get = App\Models\task::where('project_id', $task->id)->get();
                            @endphp
                            @foreach ($task_get as $t)
                                @php
                                    $month = date('m', strtotime($t->start_date));
                                    $year = date('y', strtotime($t->start_date));
                                @endphp
                                @if ($date == $month && $date1 == $year)
                                    <li class="list-group-item card mb-3">
                                        <div class="row align-items-center justify-content-between">
                                            <div class="col-auto mb-3 mb-sm-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="ms-3">
                                                        <h6 class="m-0">{{ $t->title }}</h6>
                                                        <small class="text-muted">{{ $t->start_date }} to
                                                            {{ $t->due_date }}</small>
                                                    </div>
                                                    <div class="theme-avtar bg-secondary accept-icon">
                                                        <i class="fa fa-times"></i>
                                                    </div>
                                                    <div class="theme-avtar bg-secondary accept-icon">
                                                        <i class="fa fa-check"></i>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endsection
    @push('scripts')
        <script></script>
    @endpush
