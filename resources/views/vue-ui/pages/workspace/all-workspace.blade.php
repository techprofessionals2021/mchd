@extends('layouts.admin')

@section('page-title')
    {{ __('Every Thing') }}
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
        <li class="breadcrumb-item">{{ __('Spaces') }}
        </li>
    @endif
    <li class="breadcrumb-item">Every thing</li>
@endsection
@php
    // $permissions = Auth::user()->getPermission($project->id);
    $client_keyword = Auth::user()->getGuard() == 'client' ? 'client.' : '';
    $logo = \App\Models\Utility::get_file('users-avatar/');
    $logo_project_files = \App\Models\Utility::get_file('project_files/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');

@endphp

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


                    <div class="row">
                        <div class="col-md-12">
                            <div class="card ">
                                <div class="card-body">
                                    <div class="row grey-border-bottom py-2">
                                        {{-- <div class="col-8 mb-4">
                                            <custom-avatar></custom-avatar>
                                            <p class="h-text d-inline ms-2 text-primary">Team Space</p>
                                        </div>
                                        <div class="col-4">
                                            <custom-menu
                                            :routes="{{ json_encode([
                                            'list' => route('projects.show', [$currentWorkspace->slug,'id'=>$project->id]),
                                            'calender' => route('projects.calender', [$currentWorkspace->slug,'id'=>$project->id]),
                                            'board' => route($client_keyword .'projects.task.board.custom', [$currentWorkspace->slug, $project->id]),
                                            'gantt' => route($client_keyword . 'projects.gantt.custom', [$currentWorkspace->slug, $project->id]) ]) }}"></custom-menu>
                                        </div> --}}
                                    </div>
                                    <div class="row grey-border-bottom">
                                        <div class="col-6 ">
                                            {{-- @dd($currentStatus) --}}
                                            <form
                                                action="{{ route('searchAllTasks', [$currentWorkspace->slug, $currentStatus]) }}"
                                                method="Get" class="m-t-15">
                                                <div class="input-group w-50">
                                                    <input type="text" class="form-control"
                                                        placeholder="Search Tasks By Task Title" aria-label="Search"
                                                        style="width: 20%" name="search">
                                                    <div class="input-group-append"
                                                        style="border: 1px solid #ced4da;border-radius: 0px 8px 8px 0px">
                                                        <button class="btn btn-outline-secondary" style="border: none"
                                                            type="submit">
                                                            <svg width="1em" height="1em" viewBox="0 0 16 16"
                                                                class="bi bi-search" fill="currentColor"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd"
                                                                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-1.414 0zM10 6.5a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0z" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>

                                            {{-- <custom-input-search></custom-input-search> --}}

                                        </div>
                                        <div class="col-6 my-3 text-end d-flex justify-content-end align-items-center">
                                            {{-- <div class="m-r-10">
                                                <a href="#" class=""
                                                data-url="{{ route('projects.edit', [$currentWorkspace->slug, $project->id]) }}"
                                                data-ajax-popup="true" data-title="{{ __('Edit Project') }}"
                                                data-toggle="popover" title="{{ __('Edit') }}">
                                                <img
                                                src="{{ asset('custom-ui/images/note.svg') }}" class="m-r-5 icon-image" />
                                                <span class="p-text">Edit</span>
                                                </a>

                                            </div> --}}
                                            <div class="filterTaskBtn cursor-pointer">
                                                <img src='{{ asset('custom-ui/images/filter.svg') }}' class="m-r-5" />
                                                <span class="p-text">Filter</span>
                                            </div>
                                            <div class="filterDropdown w-25 m-l-10" style="display:none;">
                                                <select class="form-select status-dropdown"
                                                    aria-label="Default select example">
                                                    @foreach ($taskStatus as $status)
                                                        <option
                                                            value=@if ($status == 'In Progress') "In Progress" @else {{ $status }} @endif
                                                            @if ($status == $currentStatus) selected @endif>
                                                            {{ $status }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        @foreach ($projects->groupBy('workspaceData.name') as  $key=>$singleWPProjects)
                                            <div class="col-md-12">
                                                <div class="card ">
                                                    <div class="card-header">
                                                        {{-- <ul class="breadcrumb">
                                                            <li class="breadcrumb-item text-common"><span
                                                                    href="{{ route('home') }}">{{ $key }}</span>
                                                            </li>
                                                            <li class="breadcrumb-item custom-bc text-common"><span
                                                                    href="{{ route('home') }}">{{ $key }}</span>
                                                            </li>
                                                        </ul> --}}
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="d-flex tasks">
                                                                <span class="dash-arrow arrow-style p-1"><i
                                                                        data-feather="chevron-right"></i></span>
                                                                <h4 class="mb-0 m-l-10">{{ $key }}
                                                                </h4>
                                                            </div>
                                                            <div>
                                                                {{-- <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create New Task') }}" data-url="{{route($client_keyword.'tasks.create',[$currentWorkspace->slug,$project->id])}}" data-toggle="tooltip" title="{{ __('Add Task') }}"><i class="ti ti-plus"></i></a> --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body tasks-body"
                                                        style="display:none;
                                                    transform-origin: top;
                                                    transition: transform .4s ease-in-out;">

                                                        @forelse  ($singleWPProjects as $project)
                                                            <div class="col-md-12">
                                                                <div class="card ">
                                                                    <div class="card-header">
                                                                        <ul class="breadcrumb">
                                                                            <li class="breadcrumb-item text-common"><span
                                                                                    href="{{ route('home') }}">{{ @$project->workspaceData->name }}</span>
                                                                            </li>
                                                                            <li
                                                                                class="breadcrumb-item custom-bc text-common">
                                                                                <span
                                                                                    href="{{ route('home') }}">{{ $project->name }}</span>
                                                                            </li>
                                                                        </ul>
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center">
                                                                            <div class="d-flex tasks">
                                                                                <span class="dash-arrow arrow-style p-1"><i
                                                                                        data-feather="chevron-right"></i></span>
                                                                                <h4 class="mb-0 m-l-10">
                                                                                    {{ $project->name }}
                                                                                </h4>
                                                                            </div>
                                                                            <div>
                                                                                {{-- <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create New Task') }}" data-url="{{route($client_keyword.'tasks.create',[$currentWorkspace->slug,$project->id])}}" data-toggle="tooltip" title="{{ __('Add Task') }}"><i class="ti ti-plus"></i></a> --}}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-body tasks-body"
                                                                        style="display:none;
            transform-origin: top;
            transition: transform .4s ease-in-out;">
                                                                        {{-- @dd($currentStatus) --}}
                                                                        @isset($searchQuery)
                                                                            <app
                                                                                :tasks='{{ json_encode($project->custom_user_tasks($searchQuery, $currentStatus)) }}'>
                                                                            </app>
                                                                        @else
                                                                            <app
                                                                                :tasks='{{ json_encode($project->custom_user_tasks('', $currentStatus)) }}'>
                                                                            </app>
                                                                        @endisset



                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <h1 class="text-center text-secondary">Record Not Found</h1>
                                                        @endforelse


                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>

                </div>


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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script></script>
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
    <script></script>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.tasks').on('click', function() {
                // console.log($(this).closest('.card').find('.tasks-body:first-child'));
                $(this).closest('.card').find('.tasks-body:first').animate({
                    height: 'toggle'
                });
                $(this).closest('.card').find('.arrow-style:first').toggleClass('rotate-90');

            })
        });
    </script>
    <!-- third party js -->
    <script src="{{ asset('assets/custom/js/dragula.min.js') }}"></script>
    <script></script>
    <!-- third party js ends -->
    <script>
        $(document).on('click', '#form-comment button', function(e) {
            var comment = $.trim($("#form-comment textarea[name='comment']").val());
            if (comment != '') {
                $.ajax({
                    url: $("#form-comment").data('action'),
                    data: {
                        comment: comment
                    },
                    type: 'POST',
                    success: function(data) {
                        data = JSON.parse(data);

                        if (data.user_type == 'Client') {
                            var avatar = "avatar='" + data.client.name + "'";
                            var html = "<li class='media border-bottom mb-3'>" +
                                "                    <img class='mr-3 avatar-sm rounded-circle img-thumbnail hight_img' width='60' " +
                                avatar + " alt='" + data.client.name + "'>" +
                                "                    <div class='media-body mb-2'>" +
                                "                    <div class='float-left'>" +
                                "                        <h5 class='mt-0 mb-1 form-control-label'>" +
                                data.client.name + "</h5>" +
                                "                        " + data.comment +
                                "                    </div>" +
                                "                    </div>" +
                                "                </li>";
                        } else {
                            var avatar = (data.user.avatar) ? "src='{{ $logo }}/" + data.user
                                .avatar + "'" : "avatar='" + data.user.name + "'";
                            var html = "<li class='media border-bottom mb-3'>" +
                                "                    <img class='mr-3 avatar-sm rounded-circle img-thumbnail hight_img ' width='60' " +
                                avatar + " alt='" + data.user.name + "'>" +
                                "                    <div class='media-body mb-2'>" +
                                "                    <div class='float-left'>" +
                                "                        <h5 class='mt-0 mb-1 form-control-label'>" +
                                data.user.name + "</h5>" +
                                "                        " + data.comment +
                                "                           </div>" +
                                "                           <div class='text-end'>" +
                                "                               <a href='#' class='delete-icon action-btn btn-danger  btn btn-sm d-inline-flex align-items-center delete-comment' data-url='" +
                                data.deleteUrl + "'>" +
                                "                                   <i class='ti ti-trash'></i>" +
                                "                               </a>" +
                                "                           </div>" +
                                "                    </div>" +
                                "                </li>";
                        }

                        $("#task-comments").prepend(html);
                        LetterAvatar.transform();
                        $("#form-comment textarea[name='comment']").val('');
                        show_toastr('{{ __('Success') }}', '{{ __('Comment Added Successfully!') }}',
                            'success');
                    },
                    error: function(data) {
                        show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                            'error');
                    }
                });
            } else {
                show_toastr('{{ __('Error') }}', '{{ __('Please write comment!') }}', 'error');
            }
        });
        $(document).on("click", ".delete-comment", function() {
            if (confirm('{{ __('Are you sure ?') }}')) {
                var btn = $(this);
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'DELETE',
                    dataType: 'JSON',
                    success: function(data) {
                        show_toastr('{{ __('Success') }}',
                            '{{ __('Comment Deleted Successfully!') }}', 'success');
                        btn.closest('.media').remove();
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('{{ __('Error') }}', data.message, 'error');
                        } else {
                            show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                'error');
                        }
                    }
                });
            }
        });
        $(document).on('click', '#form-subtask button', function(e) {
            e.preventDefault();

            var name = $.trim($("#form-subtask input[name=name]").val());
            var due_date = $.trim($("#form-subtask input[name=due_date]").val());
            if (name == '' || due_date == '') {
                show_toastr('{{ __('Error') }}', '{{ __('Please enter fields!') }}', 'error');
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
                success: function(data) {
                    show_toastr('{{ __('Success') }}', '{{ __('Sub Task Added Successfully!') }}',
                        'success');

                    var html = '<li class="list-group-item py-3">' +
                        '    <div class="form-check form-switch d-inline-block">' +
                        '        <input type="checkbox" class="form-check-input" name="option" id="option' +
                        data.id + '" value="' + data.id + '" data-url="' + data.updateUrl + '">' +
                        '        <label class="custom-control-label form-control-label" for="option' +
                        data.id + '">' + data.name + '</label>' +
                        '    </div>' +
                        '    <div class="text-end">' +
                        '        <a href="#" class=" action-btn btn-danger  btn btn-sm d-inline-flex align-items-center  delete-icon delete-subtask" data-url="' +
                        data.deleteUrl + '">' +
                        '            <i class="ti ti-trash"></i>' +
                        '        </a>' +
                        '    </div>' +
                        '</li>';

                    $("#subtasks").prepend(html);
                    $("#form-subtask input[name=name]").val('');
                    $("#form-subtask input[name=due_date]").val('');
                    $("#form-subtask").collapse('toggle');
                },
                error: function(data) {
                    data = data.responseJSON;
                    if (data.message) {
                        show_toastr('{{ __('Error') }}', data.message, 'error');
                        $('#file-error').text(data.errors.file[0]).show();
                    } else {
                        show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                            'error');
                    }
                }
            });
        });
        $(document).on("change", "#subtasks input[type=checkbox]", function() {
            $.ajax({
                url: $(this).attr('data-url'),
                type: 'POST',
                dataType: 'JSON',
                success: function(data) {
                    show_toastr('{{ __('Success') }}', '{{ __('Subtask Updated Successfully!') }}',
                        'success');
                    // console.log(data);
                },
                error: function(data) {
                    data = data.responseJSON;
                    if (data.message) {
                        show_toastr('{{ __('Error') }}', data.message, 'error');
                    } else {
                        show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                            'error');
                    }
                }
            });
        });
        $(document).on("click", ".delete-subtask", function() {
            // if (confirm('{{ __('Are you sure ?') }}')) {
            var btn = $(this);
            $.ajax({
                url: $(this).attr('data-url'),
                type: 'DELETE',
                dataType: 'JSON',
                success: function(data) {
                    show_toastr('{{ __('Success') }}', '{{ __('Subtask Deleted Successfully!') }}',
                        'success');
                    btn.closest('.list-group-item').remove();
                },
                error: function(data) {
                    data = data.responseJSON;
                    if (data.message) {
                        show_toastr('{{ __('Error') }}', data.message, 'error');
                    } else {
                        show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                            'error');
                    }
                }
            });
            // }
        });
        // $("#form-file").submit(function(e){
        $(document).on('submit', '#form-file', function(e) {
            e.preventDefault();

            $.ajax({
                url: $("#form-file").data('url'),
                type: 'POST',
                data: new FormData(this),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    show_toastr('{{ __('Success') }}', '{{ __('File Upload Successfully!') }}',
                        'success');
                    // console.log(data);
                    var delLink = '';

                    if (data.deleteUrl.length > 0) {
                        delLink =
                            "<a href='#' class=' action-btn btn-danger  btn btn-sm d-inline-flex align-items-center  delete-icon delete-comment-file'  data-url='" +
                            data.deleteUrl + "'>" +
                            "                                        <i class='ti ti-trash'></i>" +
                            "                                    </a>";
                    }

                    var html = "<div class='card mb-1 shadow-none border'>" +

                        "                        <div class='card-body p-3'>" +

                        "                            <div class='row align-items-center'>" +

                        "                                <div class='col-auto'>" +

                        "                                    <div class='avatar-sm'>" +

                        "                                        <span class='avatar-title rounded text-uppercase'>" +
                        "<img class='preview_img_size' " + "src='{{ $logo_tasks }}/" + data.file +
                        "'>" +
                        "                                        </span>" +
                        "                                    </div>" +
                        "                                </div>" +
                        "                                <div class='col pl-0'>" +
                        "                                    <a href='#' class='text-muted form-control-label'>" +
                        data.name + "</a>" +
                        "                                    <p class='mb-0'>" + data.file_size +
                        "</p>" +
                        "                                </div>" +
                        "                                <div class='col-auto'>" +
                        "                                    <a download href='{{ $logo_tasks }}/" +
                        data.file +
                        "' class='edit-icon action-btn btn-primary  btn btn-sm d-inline-flex align-items-center'>" +
                        "                                        <i class='ti ti-download'></i>" +
                        "                                    </a>" +


                        "                                   <a  href='{{ $logo_tasks }}/" + data
                        .file +
                        "' class='edit-icon action-btn btn-secondary  btn btn-sm d-inline-flex align-items-center mx-1'>" +
                        "                                        <i class='ti ti-crosshair text-white'></i>" +
                        "                                    </a>" +









                        delLink +
                        "                                </div>" +
                        "                            </div>" +
                        "                        </div>" +
                        "                    </div>";
                    $("#comments-file").prepend(html);
                },
                error: function(data) {
                    data = data.responseJSON;

                    if (data) {
                        show_toastr('{{ __('Error') }}',
                            'The file must be a file of type: jpg, jpeg, png, xlsx, xls, csv, pdf.', 'error');
                        //show_toastr('{{ __('Error') }}', data.message, 'error');
                        $('#file-error').text(data.errors.file[0]).show();
                    } else {
                        show_toastr('{{ __('Error') }}',
                            'The file must be a file of type: jpg, jpeg, png, xlsx, xls, csv, pdf.', 'error');
                    }
                }
            });
        });
        $(document).on("click", ".delete-comment-file", function() {
            if (confirm('{{ __('Are you sure ?') }}')) {
                var btn = $(this);
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'DELETE',
                    dataType: 'JSON',
                    success: function(data) {
                        show_toastr('{{ __('Success') }}', '{{ __('File Deleted Successfully!') }}',
                            'success');
                        btn.closest('.border').remove();
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('{{ __('Error') }}', data.message, 'error');
                        } else {
                            show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                'error');
                        }
                    }
                });
            }
        });
        $('.filterTaskBtn').on('click', function() {
            $('.filterDropdown').slideToggle(500);
        })
        $('.status-dropdown').on('change', function() {
            console.log();
            let currentWorkSpace = <?php echo json_encode($currentWorkspace->slug); ?>

            //   console.log(my_variable);

            location.href = window.location.origin + '/' + currentWorkSpace + '/filterAllTasksByStatus/' + $(this)
                .val()
        })
    </script>
@endpush
