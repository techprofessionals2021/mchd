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
    $logo_tasks = \App\Models\Utility::get_file('tasks/');

@endphp

{{-- @section('multiple-action-button')
    @if (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner')
        <div class="col-md-auto col-sm-4 pb-3">
            <a href="#" class="btn btn-xs btn-primary btn-icon-only col-12" data-toggle="popover"
                title="Shared Project Settings" data-ajax-popup="true" data-size="md"
                data-title="{{ __('Shared Project Settings') }}"
                data-url="{{ route('projects.copylink.setting.create', [$currentWorkspace->slug, $project->id]) }}"
                data-toggle="tooltip" title="{{ __('Add Project') }}">
                <i class="ti ti-settings"></i>
            </a>
        </div>
    @endif

    <div class="col-md-auto col-sm-4 pb-3">
        <a href="#" class="btn btn-xs btn-primary btn-icon-only col-12 cp_link "
            data-link="{{ route('projects.link', [$currentWorkspace->slug, \Illuminate\Support\Facades\Crypt::encrypt($project->id)]) }}"
            data-toggle="popover"  title="Copy Project"
            ><span
                class=""></span><span class="btn-inner--text text-white"><i
                    class="ti ti-copy"></i></span></a>
        </a>
    </div>
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
        <div class="col-md-auto col-sm-6 pb-3">
            <a href="{{ route($client_keyword . 'projects.bug.report', [$currentWorkspace->slug, $project->id]) }}"
                class="btn btn-xs btn-primary btn-icon-only col-12">{{ __('Bug Report') }}</a>
        </div>
    @endif
    <div class="col-md-auto col-sm-6 pb-3">
        <a href="{{ route($client_keyword . 'projecttime.tracker', [$currentWorkspace->slug, $project->id]) }}"
            class="btn btn-xs btn-primary btn-icon-only col-12 ">{{ __('Tracker') }}</a>
    </div>
@endsection --}}
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
                                {{-- <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0">{{ __('Tasks') }}
                                            </h5>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="card-body">
                                    {{-- <app :tasks='{{ json_encode($project->getTasksWithSubTasks()) }}'></app> --}}
                                    <div class="row grey-border-bottom py-2">
                                        <div class="col-8 mb-4">
                                            <custom-avatar></custom-avatar>
                                            <p class="h-text d-inline ms-2 text-primary">Team Space</p>
                                        </div>
                                        <div class="col-4">
                                            {{-- <custom-menu :routes="{{ json_encode(['calender' => route('projects.calender', [$currentWorkspace->slug,'id'=>$project->id]),'list' => route('projects.show', [$currentWorkspace->slug,'id'=>$project->id])]) }}"></custom-menu> --}}
                                            <custom-menu
                                            :routes="{{ json_encode([
                                            'list' => route('projects.show', [$currentWorkspace->slug,'id'=>$project->id]),
                                            'calender' => route('projects.calender', [$currentWorkspace->slug,'id'=>$project->id]),
                                            'board' => route($client_keyword .'projects.task.board.custom', [$currentWorkspace->slug, $project->id]),
                                            'gantt' => route($client_keyword . 'projects.gantt.custom', [$currentWorkspace->slug, $project->id]) ]) }}"></custom-menu>
                                        </div>
                                    </div>
                                    {{-- <div class="row grey-border-bottom">
                                        <div class="col-6 my-3">
                                            <custom-input-search></custom-input-search>
                                        </div>
                                        <div class="col-6 my-3 text-end">
                                            <img src='{{ asset('custom-ui/images/filter.svg') }}' class="m-r-5" />
                                            <span class="p-text">Filter</span>
                                        </div>
                                    </div>
                                    <br> --}}
                                    {{-- <div class="row">
                                        <div class="col-md-12">
                                            <div class="card ">
                                                <div class="card-header">
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
                                                </div>
                                                <div class="card-body tasks-body"
                                                    style="display:none;
                                                   transform-origin: top;
                                                   transition: transform .4s ease-in-out;">
                                                    <app :tasks='{{ json_encode($project->getTasksWithSubTasks()) }}'>
                                                    </app>

                                                    <calender></calender>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}

                                    @yield('contentt')


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
                        'The file must be a file of type: jpg, jpeg, png, xlsx, xls, csv, pdf.', 'error');
                }
            },
            error: function(file, response) {
                myDropzone.removeFile(file);
                if (response.error) {
                    show_toastr('{{ __('Error') }}',
                        'The file must be a file of type: jpg, jpeg, png, xlsx, xls, csv, pdf.', 'error');
                } else {
                    show_toastr('{{ __('Error') }}',
                        'The file must be a file of type: jpg, jpeg, png, xlsx, xls, csv, pdf.', 'error');
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
@stack('scriptss')

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.tasks').on('click', () => {
                $('.tasks-body').animate({
                    height: 'toggle'
                });
                $('.arrow-style').toggleClass('rotate-90');

            })
        });
    </script>
    <!-- third party js -->
    <script src="{{ asset('assets/custom/js/dragula.min.js') }}"></script>
    <script>
        ! function(a) {
            "use strict";
            var t = function() {
                this.$body = a("body")
            };
            t.prototype.init = function() {
                a('[data-toggle="dragula"]').each(function() {
                    var t = a(this).data("containers"),
                        n = [];
                    if (t)
                        for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]);
                    else n = [a(this)[0]];
                    var r = a(this).data("handleclass");
                    r ? dragula(n, {
                        moves: function(a, t, n) {
                            return n.classList.contains(r)
                        }
                    }) : dragula(n).on('drop', function(el, target, source, sibling) {
                        var sort = [];
                        $("#" + target.id + " > div").each(function(key) {
                            sort[key] = $(this).attr('id');
                        });
                        var id = el.id;
                        var old_status = $("#" + source.id).data('status');
                        var new_status = $("#" + target.id).data('status');
                        var project_id = '{{ $project->id }}';

                        $("#" + source.id).parents('.card-list').find('.count').text($("#" + source.id +
                            " > div").length);
                        $("#" + target.id).parents('.card-list').find('.count').text($("#" + target.id +
                            " > div").length);
                        $.ajax({
                            url: '{{ route($client_keyword . 'tasks.update.order', [$currentWorkspace->slug, $project->id]) }}',
                            type: 'POST',
                            data: {
                                id: id,
                                sort: sort,
                                new_status: new_status,
                                old_status: old_status,
                                project_id: project_id,
                            },
                            success: function(data) {
                                // console.log(data);
                            }
                        });
                    });
                })
            }, a.Dragula = new t, a.Dragula.Constructor = t
        }(window.jQuery),
        function(a) {
            "use strict";
            @if (
                (isset($permissions) && in_array('move task', $permissions)) ||
                    ($currentWorkspace && $currentWorkspace->permission == 'Owner'))
                a.Dragula.init();
            @endif
        }(window.jQuery);
    </script>
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
                      //  "                                    <p class='mb-0'>" + data.file_size +
                     //   "</p>" +
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
    </script>
@endpush
