@extends('layouts.admin')

@section('page-title') {{__('Projects')}} @endsection
@section('links')
@if(\Auth::guard('client')->check())
<li class="breadcrumb-item"><a href="{{route('client.home')}}">{{__('Home')}}</a></li>
 @else
 <li class="breadcrumb-item"><a href="{{route('home')}}">{{__('Home')}}</a></li>
 @endif
<li class="breadcrumb-item"> {{ __('Projects') }}</li>
@endsection

@php
    $logo=\App\Models\Utility::get_file('users-avatar/');

@endphp
@section('action-button')
    @auth('web')

     <a href="{{ route('project.export') }}"  class="btn btn-sm btn-primary "  data-toggle="tooltip" title="{{ __('Export Project') }}"
                > <i class="ti ti-file-x"></i></a>

                <a href="#"  class="btn btn-sm btn-primary mx-1" data-ajax-popup="true" data-title="{{__('Import Project')}}" data-url="{{ route('project.file.import' ,$currentWorkspace->slug) }}"  data-toggle="tooltip" title="{{ __('Import Project') }}"><i class="ti ti-file-import"></i> </a>

        @if(isset($currentWorkspace) && $currentWorkspace->creater->id == Auth::id())
            <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create New Project') }}" data-url="{{route('projects.create',$currentWorkspace->slug)}}" data-toggle="tooltip" title="{{ __('Add Project') }}">
                <i class="ti ti-plus"></i>
            </a>
          @endif

    @endauth
@endsection

@section('content')
    <section class="section">
        @if($projects && $currentWorkspace)
            <div class="row mb-2">
                <div class="col-xl-12 col-lg-12 col-md-12 col-12 d-flex align-items-center justify-content-end">
                    <div class="text-sm-right status-filter">
                        <div class="btn-group mb-3">
                            <button type="button" class="btn btn-light  text-white btn_tab  bg-primary active" data-filter="*" data-status="All">{{ __('All')}}</button>
                            <button type="button" class="btn btn-light bg-primary text-white btn_tab" data-filter=".Ongoing">{{ __('Ongoing')}}</button>
                            <button type="button" class="btn btn-light bg-primary text-white btn_tab" data-filter=".Finished">{{ __('Finished')}}</button>
                            <button type="button" class="btn btn-light bg-primary text-white btn_tab" data-filter=".OnHold">{{ __('OnHold')}}</button>
                        </div>
                    </div>
                </div><!-- end col-->
            </div>

            <div class="filters-content">
                <div class="row grid">
                    @foreach ($projects as $project)
                      <div class="col-xl-3 col-lg-4 col-sm-6 All {{ $project->status }}">
                                <div class="card">
                                    <div class="card-header border-0 pb-0">
                                        <div class="d-flex align-items-center">
                                    @if($project->is_active)
                                        <a href="@auth('web'){{route('projects.show',[$currentWorkspace->slug,$project->id])}}@elseauth{{route('client.projects.show',[$currentWorkspace->slug,$project->id])}}@endauth" class="">
                                            <img alt="{{ $project->name }}" class="img-fluid wid-30 me-2 fix_img" avatar="{{ $project->name }}">
                                        </a>
                                    @else
                                        <a href="#" class="">
                                            <img alt="{{ $project->name }}" class="img-fluid wid-30 me-2 fix_img" avatar="{{ $project->name }}">
                                        </a>
                                    @endif

                                            <h5 class="mb-0">
                                             @if($project->is_active)
                                            <a href="@auth('web'){{route('projects.show',[$currentWorkspace->slug,$project->id])}}@elseauth{{route('client.projects.show',[$currentWorkspace->slug,$project->id])}}@endauth" title="{{ $project->name }}" class="">{{ $project->name }}</a>
                                            @else
                                            <a href="#" title="{{ __('Locked') }}" class="">{{ $project->name }}</a>
                                           @endif
                                          </h5>
                                        </div>
                                        <div class="card-header-right">
                                            <div class="btn-group card-option">
                                                @auth('web')
                                                <button type="button" class="btn dropdown-toggle"
                                                    data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <i class="feather icon-more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">


                                        @if($project->is_active)

                                                @if($currentWorkspace->permission == 'Owner')
                                                    <a href="#" class="dropdown-item" data-ajax-popup="true" data-size="md" data-title="{{ __('Invite Users') }}" data-url="{{route('projects.invite.popup',[$currentWorkspace->slug,$project->id])}}">
                                                        <i class="ti ti-user-plus"></i> <span>{{ __('Invite Users') }}</span>
                                                    </a>
                                                    <a href="#" class="dropdown-item" data-ajax-popup="true" data-size="lg" data-title="{{ __('Edit Project') }}" data-url="{{route('projects.edit',[$currentWorkspace->slug,$project->id])}}">
                                                       <i class="ti ti-edit"></i> <span>{{ __('Edit') }}</span>
                                                    </a>
                                                    <a href="#" class="dropdown-item" data-ajax-popup="true" data-size="md" data-title="{{ __('Share to Clients') }}" data-url="{{route('projects.share.popup',[$currentWorkspace->slug,$project->id])}}">
                                                       <i class="ti ti-share"></i> <span>{{ __('Share to Clients')}}</span>
                                                    </a>
                                                    <a href="#" class="dropdown-item" data-ajax-popup="true"
                                                        data-size="md" data-title="{{ __('Duplicate Project') }}"
                                                        data-url="{{ route('project.copy', [$currentWorkspace->slug,$project->id]) }}">
                                                        <i class="ti ti-copy"></i> <span>{{ __('Duplicate') }}</span>
                                                    </a>
                                                    <a href="#" class="dropdown-item text-danger delete-popup bs-pass-para" data-confirm="{{__('Are You Sure?')}}" data-text="{{__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="delete-form-{{$project->id}}" >
                                                       <i class="ti ti-trash"></i>  <span>{{ __('Delete')}}</span>
                                                    </a>
                                                    <form id="delete-form-{{$project->id}}" action="{{ route('projects.destroy',[$currentWorkspace->slug,$project->id]) }}" method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @else
                                                    <a href="#" class="dropdown-item text-danger bs-pass-para" data-confirm="{{__('Are You Sure?')}}" data-text="{{__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="leave-form-{{$project->id}}">
                                                         <i class="ti ti-trash"></i>  <span>{{ __('Delete')}}</span>
                                                    </a>
                                                    <form id="leave-form-{{$project->id}}" action="{{ route('projects.leave',[$currentWorkspace->slug,$project->id]) }}" method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endif

                                        @else
                                            <a href="#" class="dropdown-item" title="{{__('Locked')}}">
                                                <i data-feather="lock"></i> <span>{{__('Locked')}}</span>
                                            </a>
                                        @endif

                                                </div>
                                                 @endauth
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2 justify-content-between">
                                            @if($project->status == 'Finished')
                                                <div class="col-auto"><span class="badge rounded-pill bg-success">{{ __('Finished')}}</span>
                                            </div>
                                            @elseif($project->status == 'Ongoing')
                                                <div class="col-auto"><span class="badge rounded-pill bg-secondary">{{ __('Ongoing')}}</span>
                                            </div>
                                            @else
                                               <div class="col-auto"><span class="badge rounded-pill bg-warning">{{ __('OnHold')}}</span>
                                            </div>
                                            @endif

                                           <div class="col-auto">
                                                <p class="mb-0"><b>{{ __('Due Date:')}}</b> {{$project->end_date}}</p>
                                            </div>
                                        </div>
                                        <p class="text-muted text-sm mt-3">{{ $project->description }}</p>
                                        <h6 class="text-muted">MEMBERS</h6>
                                        <div class="user-group mx-2">
                                              @foreach($project->users as $user)
                                            @if($user->pivot->is_active)

                                                 <a href="#" class="img_group" data-toggle="tooltip" data-placement="top" title="{{$user->name}}">
                                                    <img alt="{{$user->name}}" @if($user->avatar) src="{{asset($logo.$user->avatar)}}" @else avatar="{{ $user->name }}" @endif>
                                                </a>
                                             @endif
                                        @endforeach

                                        </div>
                                        <div class="card mb-0 mt-3">
                                            <div class="card-body p-3">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <h6 class="mb-0">{{$project->countTask()}}</h6>
                                                        <p class="text-muted text-sm mb-0">{{ __('Tasks')}}</p>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <h6 class="mb-0">{{$project->countTaskComments()}}</h6>
                                                        <p class="text-muted text-sm mb-0">{{ __('Comments')}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    @endforeach

                          @auth('web')
                            @if(isset($currentWorkspace) && $currentWorkspace->creater->id == Auth::id())
                             <div class="col-xl-3 col-lg-4 col-sm-6 All add_projects">
                                <a href="#" class="btn-addnew-project " style="padding: 90px 10px;" data-ajax-popup="true" data-size="md" data-title="{{ __('Create New Project') }}" data-url="{{route('projects.create',$currentWorkspace->slug)}}">
                                <div class="bg-primary proj-add-icon">
                                <i class="ti ti-plus"></i>
                                </div>
                                <h6 class="mt-4 mb-2">Add Project</h6>
                                <p class="text-muted text-center">Click here to add New Project</p>
                                </a>
                                </div>
                            @endif
                           @endauth

                </div>
            </div>
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
                                    <p class="text-muted mt-3">{{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.")}}</p>
                                    <div class="mt-3">
                                        <a class="btn-return-home badge-blue" href="{{route('home')}}"><i class="fas fa-reply"></i> {{ __('Return Home')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection

@push('css-page')
@endpush

@push('scripts')
    <script src="{{asset('assets/custom/js/isotope.pkgd.min.js')}}"></script>
    <script>
        $(document).ready(function () {

            $('.status-filter button').click(function () {
                $('.status-filter button').removeClass('active');
                $(this).addClass('active');

                var data = $(this).attr('data-filter');
                $grid.isotope({
                    filter: data
                })
            });

            var $grid = $(".grid").isotope({
                itemSelector: ".All",
                percentPosition: true,
                masonry: {
                    columnWidth: ".All"
                }
            })
        });
    </script>

@endpush
