@extends('layouts.admin')
@section('page-title')
{{__(' Notifications')}}
@endsection

@section('action-button')
    <div class="row">

        <div class="text-end mb-3">
            <div class="text-end">
                <div class="d-flex justify-content-end drp-languages">
                    @if ($currentWorkspace->is_chagpt_enable())
                    <ul class="list-unstyled mb-0 m-3">
                        <a href="#" data-size="lg" data-ajax-popup-over="true" class="btn btn-sm btn-primary" data-url="{{ route('generate',['notification template',$notification_template->id]) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate with AI') }}" data-title="{{ __('Generate Notification Message') }}">
                            <i class="fas fa-robot px-1"></i>{{ __('Generate with AI') }}</a>
                    </ul>
                    @endif
                    <ul class="list-unstyled mb-0 m-2">
                        <li class="dropdown dash-h-item drp-language">
                            <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                            href="#" role="button" aria-haspopup="false" aria-expanded="false"
                            id="dropdownLanguage">
                                <span
                                    class="drp-text hide-mob text-primary">{{ ucfirst( \App\Models\Utility::getlang_fullname($curr_noti_tempLang->lang)) }}</span>
                                <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                            </a>
                            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end"
                                aria-labelledby="dropdownLanguage">
                                @foreach ($languages as $lang)
                                    <a href="{{ route('notification-templates.index', [$currentWorkspace->slug , $notification_template->id, $lang]) }}"
                                    class="dropdown-item {{ $curr_noti_tempLang->lang == $lang ? 'text-primary' : '' }}">{{ ucfirst( \App\Models\Utility::getlang_fullname($lang)) }}</a>
                                @endforeach
                            </div>
                        </li>
                    </ul>
                    <ul class="list-unstyled mb-0 m-2">
                        <li class="dropdown dash-h-item drp-language">
                            <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                            href="#" role="button" aria-haspopup="false" aria-expanded="false"
                            id="dropdownLanguage">
                                <span
                                    class="drp-text hide-mob text-primary">{{ __('Template: ') }}{{ $notification_template->name }}</span>
                                <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                            </a>
                            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end" aria-labelledby="dropdownLanguage">
                                @foreach ($notification_templates as $notification_template1)
                                    <a href="{{ route('notification-templates.index', [$currentWorkspace->slug , $notification_template1->id,(Request::segment(4)?Request::segment(4):\Auth::user()->lang)]) }}"
                                    class="dropdown-item {{$notification_template->name == $notification_template1->name ? 'text-primary' : '' }}">{{ $notification_template1->name }}
                                    </a>
                                @endforeach
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('links')
@if(\Auth::guard('client')->check())   
<li class="breadcrumb-item"><a href="{{route('client.home')}}">{{__('Home')}}</a></li>
 @else
 <li class="breadcrumb-item"><a href="{{route('home')}}">{{__('Home')}}</a></li>
 @endif
<li class="breadcrumb-item"> <a href="{{route('notification-templates.index',$currentWorkspace->slug)}}">{{ __('Notifications') }}</a></li>
<li class="breadcrumb-item">{{ $notification_template->name }}</li>
 @endsection

@push('css-page')
    <link rel="stylesheet" href="{{asset('assets/custom/libs/summernote/summernote-bs4.css')}}">
@endpush
@push('scripts')
<script src="{{asset('assets/custom/libs/summernote/summernote-bs4.js')}}"></script>
<script>
 if ($(".summernote-simple").length) {
        $('.summernote-simple').summernote({
            dialogsInBody: !0,
            minHeight: 200,
            toolbar: [
                ['style', ['style']],
                ["font", ["bold", "italic", "underline", "clear", "strikethrough"]],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ["para", ["ul", "ol", "paragraph"]],
            ]
        });
    }
</script>
@endpush
 @section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body ">
                <h5 class= "font-weight-bold pb-3">{{ __('Placeholders') }}</h5>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card">
                                <div class="card-header card-body">
                                    <div class="row text-xs">
                                        <h6 class="font-weight-bold mb-4">{{__('Variables')}}</h6>
                                        @php
                                            $variables = json_decode($curr_noti_tempLang->variables);
                                        @endphp
                                        @if(!empty($variables) > 0)
                                        @foreach  ($variables as $key => $var)
                                        <div class="col-6 pb-1">
                                            <p class="mb-1">{{__($key)}} : <span class="pull-right text-primary">{{ '{'.$var.'}' }}</span></p>
                                        </div>
                                        @endforeach
                                        @endif
                                    </div>

                                </div>
                            </div>
                    </div>
                    {{Form::model($curr_noti_tempLang,array('route' => array('notification-templates.update', [$currentWorkspace->slug,  $curr_noti_tempLang->parent_id]), 'method' => 'PUT')) }}
                        <div class="row">
                            <div class="form-group col-12">
                                {{Form::label('content',__('Notification Message'),['class'=>'form-label text-dark'])}}
                                {{Form::textarea('content',$curr_noti_tempLang->content,array('class'=>'form-control','required'=>'required','rows'=>'04','placeholder'=>'EX. Hello, {company_name}'))}}
                                <small>{{ __('A variable is to be used in such a way.')}} <span class="text-primary">{{ __('Ex. Hello, {company_name}')}}</span></small>
                            </div>
                            {{-- <div class="form-group col-12">
                                {{Form::label('content',__('Notification Message'),['class'=>'form-label text-dark'])}}
                                {{Form::textarea('content',$curr_noti_tempLang->content,array('class'=>'summernote-simple','required'=>'required'))}}
                            </div> --}}
                        </div>
                        <hr>
                        <div class="col-md-12 text-end">
                            {{Form::hidden('lang',null)}}
                            <input type="submit" value="{{__('Save Changes')}}" class="btn btn-print-invoice  btn-primary m-r-10">
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
