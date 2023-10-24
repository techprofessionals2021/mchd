@extends('vue-ui.pages.project.layout.app')

@section('contentt')
<br>
<div class="row">
    <div class="col-md-12">
        <div class="card ">
            {{-- <div class="card-header">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item text-common"><span href="{{ route('home') }}">{{ __('Project') }}</span></li>
                    <li class="breadcrumb-item custom-bc text-common"><span href="{{ route('home') }}">{{ $project->name }}</span></li>
                </ul>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex tasks">
                        <span class="dash-arrow arrow-style p-1"><i
                            data-feather="chevron-right"></i></span>
                        <h4 class="mb-0 m-l-10">{{ __('Tasks') }}
                        </h4>
                    </div>
                </div>
            </div> --}}
            <div class="card-body tasks-body"
                style="display:block;
               transform-origin: top;
               transition: transform .4s ease-in-out;">


                {{-- <calender></calender> --}}
                <qalendar :users='{{ json_encode($WSUsers) }}' :meetings='{{ json_encode($meetingCollection)}}' ></qalendar>
            </div>
        </div>
    </div>
</div>
@endsection
