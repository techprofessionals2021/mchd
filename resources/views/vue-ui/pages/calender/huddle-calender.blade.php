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

    .text-muted::after{
    content: "\a";
    white-space: pre;
    }

    .calendar-root{
        border: 3px solid rgb(224 224 224) !important;
    }

    .calendar-week__event{
        height: auto !important;
    }
</style>
@section('page-title')
    {{ __('Huddle Calendar') }}
@endsection
@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('Huddle Calendar') }}</li>
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
        <div class="col-lg-12">
            <div class="card">

                <div class="card-body tasks-body"
                    style="display:block;
               transform-origin: top;
               transition: transform .4s ease-in-out;">


                    {{-- <calender></calender> --}}
                    <huddle-qalendar :users='{{ json_encode($WSUsers) }}' :meetings='{{ json_encode($meetingCollection) }}'>
                    </huddle-qalendar>
                </div>
            </div>
        </div>

        {{-- <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">{{ __('New Meetings') }}</h4>
                    <ul class="event-cards list-group list-group-flush mt-3 w-100">

                            @foreach ($pendingMeetings as $meeting)
                                    <li class="list-group-item card mb-3">
                                        <div class="row align-items-center justify-content-between">
                                            <div class="col-7 mb-3 mb-sm-0">
                                                <div class="ms-3">
                                                    <h6 class="m-0 mb-1">{{ $meeting->title }}</h6>
                                                   <small class="text-muted">{{ $meeting->meeting_date }}</small>
                                                   <small class="text-muted">{{ $meeting->time_in }}</small>
                                                </div>
                                            </div>
                                            <div class="col-5 mb-3 mb-sm-0 row justify-content-between">

                                                <div class="theme-avtar bg-secondary accept-icon col-6">
                                                    <a href="{{route('meeting.decision',[$meeting->id,0])}}" class="text-white">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                </div>
                                                <div class="theme-avtar bg-secondary accept-icon col-6" >
                                                    <a href="{{route('meeting.decision',[$meeting->id,1])}}" class="text-white">
                                                        <i class="fa fa-check"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                            @endforeach
                    </ul>
                </div>
            </div>
        </div> --}}
    @endsection
    @push('scripts')
        <script>
        </script>
    @endpush
