@extends('layouts.admin')

@section('page-title')
    {{ __('Dashboard') }}
@endsection
@php
    $client_keyword = Auth::user()->getGuard() == 'client' ? 'client.' : '';
@endphp
@section('content')

    <section class="section">
        @if (Auth::user()->type == 'admin')
            <div class="row">
                <div class="col-12">
                    @if (empty(env('PUSHER_APP_ID')) ||
                            empty(env('PUSHER_APP_KEY')) ||
                            empty(env('PUSHER_APP_SECRET')) ||
                            empty(env('PUSHER_APP_CLUSTER')))
                        <div class="alert alert-warning"><i class="fas fa-warning"></i>
                            {{ __('Please Add Pusher Detail in Setting Page ') }}<u><a
                                    href="{{ route('settings.index') }}">{{ __('here') }}</a></u></div>
                    @endif
                    @if (empty(env('MAIL_DRIVER')) ||
                            empty(env('MAIL_HOST')) ||
                            empty(env('MAIL_PORT')) ||
                            empty(env('MAIL_USERNAME')) ||
                            empty(env('MAIL_PASSWORD')) ||
                            empty(env('MAIL_PASSWORD')))
                        <div class="alert alert-warning"><i class="fas fa-warning"></i>
                            {{ __('Please Add Mail Details in Setting Page ') }} <u><a
                                    href="{{ route('settings.index') }}">{{ __('here') }}</a></u></div>
                    @endif
                </div>
                <div class="col-lg-7 col-md-7 col-sm-7">
                    <div class="row">

                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-info">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">
                                        {{ __('Paid User') }} : <strong>{{ $totalPaidUsers }}</strong></p>
                                    <h6 class="mb-3">{{ __('Total Users') }}</h6>
                                    <h3 class="mb-0">{{ $totalUsers }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-success">
                                        <i class="fas fa-cash-register"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">

                                        {{ __('Order Amount') }} :
                                        <strong>{{ (env('CURRENCY_SYMBOL') != '' ? env('CURRENCY_SYMBOL') : '$') . $totalOrderAmount }}</strong>
                                    </p>
                                    <h6 class="mb-3">{{ __('Total Orders') }}</h6>
                                    <h3 class="mb-0">{{ $totalOrders }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body total_plan">
                                    <div class="theme-avtar bg-danger">
                                        <i class="fas fa-trophy"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">
                                        {{ __('Most purchase plan') }} : <strong>
                                            @if ($mostPlans)
                                                {{ $mostPlans->name }}
                                            @else
                                                -
                                            @endif
                                        </strong>
                                    </p>
                                    <h6 class="mb-3">{{ __('Total Plans') }}</h6>
                                    <h3 class="mb-0">{{ $totalPlans }} <span class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-md-5 col-sm-5">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-10">
                                    <h5>{{ __('Recent Orders') }}</h5>
                                </div>
                                <div class=" col-2"><small class="text-end"></small></div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="task-area-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($currentWorkspace)
            <div class="row">
                <div class="col-lg-12 col-md-7 row">
                    <div class="col-lg-12">
                        <div class="row mt-3">
                            <div class="col-xl-3 col-md-6 col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-primary">
                                            <i class="fas fa-trophy  bg-primary text-white"></i>
                                        </div>
                                        <p class="text-muted text-sm"></p>
                                        <h6 class="">{{ __('Completed Tasks') }}</h6>
                                        <h3 class="mb-0">{{ $completeTask }} <span class="text-success text-sm"></span>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-cyan-800">
                                            <i class="fas fa-adjust  text-white"></i>
                                        </div>
                                        <p class="text-muted text-sm "></p>
                                        <h6 class="">{{ __('In Completed Tasks') }}</h6>
                                        <h3 class="mb-0">{{ $inProgressTask }} <span class="text-success text-sm"></span>
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="col-xl-3 col-md-6 col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-danger">
                                            <i class="fas fa-bug bg-danger text-white"></i>
                                        </div>
                                        <p class="text-muted text-sm"></p>
                                        <h6 class="">{{ __('Total Bug') }}</h6>
                                        <h3 class="mb-0">{{ $totalBugs }} <span
                                                class="text-success text-sm"></span></h3>
                                    </div>
                                </div>
                            </div> --}}



                            <div class="col-xl-3 col-md-6 col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-danger">
                                            <i class="fas fa-info-circle bg-danger text-white"></i>
                                        </div>
                                        <p class="text-muted text-sm"></p>
                                        <h6 class="">{{ __('Over Due Tasks') }}</h6>
                                        <h3 class="mb-0">{{ $dueDateTask }} <span class="text-success text-sm"></span>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6 col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-info">
                                            <i class="fas fa-tasks  text-white"></i>
                                        </div>
                                        <p class="text-muted text-sm"></p>
                                        <h6 class="">{{ __('Total Tasks') }}</h6>
                                        <h3 class="mb-0">{{ $totalTask }} <span
                                                class="text-success text-sm"></span>
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="col-xl-4 col-md-6 col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-pink-600">
                                            <i class="fas fa-calendar-alt bg-primary text-white"></i>
                                        </div>
                                        <p class="text-muted text-sm"></p>
                                        <h6 class="">{{ __('Due Date Project') }}</h6>
                                        <h3 class="mb-0">{{ $dueDateProjects }} <span
                                                class="text-success text-sm"></span>
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6 col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-gray-700">
                                            <i class="fas fa-calendar text-white"></i>
                                        </div>
                                        <p class="text-muted text-sm"></p>
                                        <h6 class="">{{ __('Due Date Tasks') }}</h6>
                                        <h3 class="mb-0">{{ $dueDateTask }} <span class="text-success text-sm"></span>
                                        </h3>
                                    </div>
                                </div>
                            </div> --}}








                        </div>

                    </div>

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                             <div class="card-header">
                                 <h5>{{ __('Monthly Tasks Report') }}</h5>
                                 <div class="text-end"><small class=""></small></div>
                             </div>
                             <div class="card-body">
                                 <div id="column-chart"></div>
                             </div>
                            </div>
                        </div>

                         <div class="col-lg-4 h-100">

                             <div class="h-100">
                                 <div class="card" style="height: 95%;">
                                     <div class="card-header">
                                         <div class="float-end">
                                             <a href="#" data-bs-toggle="tooltip" data-bs-placement="top"
                                                 title="Refferals"><i class=""></i></a>
                                         </div>

                                         <h5>{{ __('Task Statistics ') }}</h5>
                                     </div>
                                     <div class="card-body">
                                         <div class="row align-items-center">
                                             <div class="col-sm-12">
                                                 <div id="projects-chart"></div>
                                             </div>
                                             {{-- <div class="col-sm-6  pb-5 px-3">
                                                 <div class="col-12 col-sm-10">
                                                     <span class="d-flex justify-content-center align-items-center mb-2">
                                                         <i class="f-10 lh-1 fas fa-circle" style="color:#3cb8d9"></i>
                                                         <span class="ms-2 text-sm">On Going</span>
                                                     </span>
                                                 </div>

                                                 <div class="col-12 col-sm-10">
                                                     <span class="d-flex justify-content-center align-items-center mb-2">
                                                         <i class="f-10 lh-1 fas fa-circle" style="color: #3d5a72; "></i>
                                                         <span class="ms-2 text-sm">Finished</span>
                                                     </span>
                                                 </div>
                                             </div> --}}
                                             @php

                                                 $taskStatisticsColors = ['Todo' => '#008ffb','In Progress' => '#00e396','Review' => '#feb019','Done' => '#ff4560']
                                             @endphp

                                             <div class="row text-center">

                                                 @foreach ($taskPercentages as $index => $value)
                                                     <div class="col-6">
                                                         <i class="fas fa-chart {{ $index }}  h3"></i>
                                                         {{-- <span class="font-weight-bold">
                                                             <span>{{ $value }}%</span>
                                                         </span> --}}
                                                         <span class="status_badge_dash badge  p-2 px-3 rounded  text-whte mt-2" style="background-color: {{$taskStatisticsColors[$index]}}">{{ $index }}</span>
                                                     </div>
                                                 @endforeach

                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>

                    </div>

                    <div class="card ">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-9">
                                    <h5 class="">
                                        {{ __('Recent Tasks') }}
                                    </h5>
                                </div>
                                <div class="col-auto d-flex justify-content-end">
                                    <div class="">
                                        <small><b>{{ $completeTask }}</b> {{ __('Tasks completed out of') }}
                                            {{ $totalTask }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body ">
                            <div class="table-responsive">
                                <table class="table table-centered table-hover mb-0 animated">
                                    <thead>
                                        <th>Title</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Project</th>
                                        <th>Assign To</th>
                                        <th>Task completion</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($tasks as $task)
                                            <tr>
                                                <td>
                                                    <div class="font-14 my-1"><a
                                                            href="{{ route($client_keyword . 'projects.task.board', [$currentWorkspace->slug, $task->project_id]) }}"
                                                            class="text-body">{{ $task->title }}</a></div>


                                                </td>
                                                <td>
                                                    @php($due_date = '<span class="text-' . ($task->due_date < date('Y-m-d') ? 'danger' : 'success') . '">' . date('Y-m-d', strtotime($task->due_date)) . '</span> ')

                                                    <span class="text-muted font-13">
                                                        {!! $due_date !!}</span>
                                                </td>
                                                <td>
                                                    @if ($task->complete == '1')
                                                        <span
                                                            class="status_badge_dash badge bg-success p-2 px-3 rounded">{{ __($task->status) }}</span>
                                                    @else
                                                        <span
                                                            class="status_badge_dash badge bg-primary p-2 px-3 rounded">{{ __($task->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>

                                                    <div class="font-14 mt-1 font-weight-normal">
                                                        {{ $task->project->name }}</div>
                                                </td>
                                                @if ($currentWorkspace->permission == 'Owner' || Auth::user()->getGuard() == 'client')
                                                    <td>

                                                        <div class="font-14 mt-1 font-weight-normal">
                                                            @foreach ($task->users() as $user)
                                                                <span
                                                                    class="badge p-2 px-2 rounded bg-secondary">{{ isset($user->name) ? $user->name : '-' }}</span>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                @endif
                                                <td>

                                                    <div class="font-14 mt-1 font-weight-normal">
                                                        <div class="progress mt-1" style="height: 20px">
                                                            <div class="progress-bar" role="progressbar" style="width: {{$task->subTaskPercentage()}}%;" aria-valuenow="{{$task->subTaskPercentage()}}" aria-valuemin="0" aria-valuemax="100">{{$task->subTaskPercentage()}}%</div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div>


                    {{-- <div class="card">
                        <div class="card-header">
                            <h5>{{ __('Project Status') }}</h5>
                            <div class="text-end"><small class=""></small></div>
                        </div>
                        <div class="card-body">
                            <div id="project-status"></div>
                        </div>
                    </div> --}}

                    {{-- <div class="card">
                        <div class="card-header">
                            <h5>{{ __('Project Overview') }}</h5>
                            <div class="text-end"><small class=""></small></div>
                        </div>
                        <div class="card-body">
                            <div id="project-area-chart"></div>
                        </div>
                    </div>



                    <div class="card ">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-9">
                                    <h5 class="">
                                        {{ __('Projects') }}
                                    </h5>
                                </div>
                                <div class="col-auto d-flex justify-content-end">
                                    <div class="">
                                        <small><b>{{ $completeTask }}</b> {{ __('Project completed out of') }}
                                            {{ $totalTask }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body ">
                            <div class="table-responsive">
                                <table class="table table-centered table-hover mb-0 animated">
                                    <tbody>
                                        @foreach ($projects as $project)
                                            <tr>
                                                <td>
                                                    <div class="font-14 my-1"><a
                                                            href="{{ route('projects.show', [$currentWorkspace->slug, $project->id]) }}"
                                                            class="text-body">{{ $project->name }}</a></div>

                                                    @php($due_date = '<span class="text-' . ($project->end_date < date('Y-m-d') ? 'danger' : 'success') . '">' . date('Y-m-d', strtotime($project->end_date)) . '</span> ')

                                                    <span class="text-muted font-13">{{ __('Due Date') }} :
                                                        {!! $due_date !!}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted font-13">{{ __('Status') }}</span> <br />
                                                    @if ($project->status == 'Ongoing')
                                                        <span
                                                            class="status_badge_dash badge bg-success p-2 px-3 rounded">{{ __($project->status) }}</span>
                                                    @elseif ($project->status == 'Finished')
                                                        <span
                                                            class="status_badge_dash badge bg-primary p-2 px-3 rounded">{{ __($project->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="text-muted font-13">{{ __('Project') }}</span>
                                                    <div class="font-14 mt-1 font-weight-normal">
                                                        {{ $task->project->name }}</div>
                                                </td>
                                                @if ($currentWorkspace->permission == 'Owner' || Auth::user()->getGuard() == 'client')
                                                    <td>
                                                        <span class="text-muted font-13">{{ __('Assigned to') }}</span>
                                                        <div class="font-14 mt-1 font-weight-normal">
                                                            @foreach ($project->users as $user)
                                                                <span
                                                                    class="badge p-2 px-2 rounded bg-secondary">{{ isset($user->name) ? $user->name : '-' }}</span>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div> --}}

                    {{-- <div class="card">
                        <div class="card-header">
                            <h5>{{ __('Tasks Overview') }}</h5>
                            <div class="text-end"><small class=""></small></div>
                        </div>
                        <div class="card-body">
                            <div id="task-area-chart"></div>
                        </div>
                    </div> --}}





                    {{-- <div class="card">
                        <div class="card-header">
                            <h5>{{ __('Workspace Report') }}</h5>
                            <div class="text-end"><small class=""></small></div>
                        </div>
                        <div class="card-body">
                            <div id="workspace-chart"></div>
                        </div>
                    </div> --}}

                </div>





            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-0 mt-3 text-center text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title mb-0">
                                {{ __('There is no active Workspace. Please create Workspace from right side menu.') }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>

@endsection


@push('scripts')
    <script src="{{ asset('assets/custom/js/apexcharts.min.js') }}"></script>

    @if (Auth::user()->type == 'admin')
    @elseif(isset($currentWorkspace) && $currentWorkspace)
        <script>
            (function() {


                //workspace chart

                var workspaceChartoptions = {
                    chart: {
                        type: 'bar', // Specify the type of chart (e.g., 'bar', 'line', etc.)
                    },
                    series: [{
                        name: 'Total Projects',
                        data: {!! json_encode($chartData['total_projects']) !!}, // Your report data here

                    }, ],
                    xaxis: {
                        categories: {!! json_encode($chartData['workspaces']) !!},
                    },
                };

                var chart = new ApexCharts(document.querySelector('#workspace-chart'), workspaceChartoptions);
                chart.render();

                //workspace chart


                // var projectsChartoptions = {
                //     chart: {
                //         height: 280,
                //         type: 'pie',
                //     },
                //     dataLabels: {
                //         enabled: false,
                //     },
                //     // plotOptions: {
                //     //     pie: {
                //     //         donut: {
                //     //             size: '100%',
                //     //         }
                //     //     }
                //     // },
                //     series: {!! json_encode($arrProcessPer) !!},
                //     // series: [77,11,11],

                //     colors: {!! json_encode($chartData['color']) !!},
                //     labels: {!! json_encode($arrProcessLabel) !!},
                //     grid: {
                //         borderColor: '#e7e7e7',
                //         row: {
                //             colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                //             opacity: 0.5
                //         },
                //     },
                //     markers: {
                //         size: 1
                //     },
                //     legend: {
                //         show: false
                //     }
                // };
                // var chart = new ApexCharts(document.querySelector("#projects-chart"), projectsChartoptions);
                // chart.render();

                var projectsChartoptions = {
                    chart: {
                        height: 280,
                        type: 'pie',
                    },
                    // dataLabels: {
                    //     enabled: false,
                    // },
                    // plotOptions: {
                    //     pie: {
                    //         donut: {
                    //             size: '100%',
                    //         }
                    //     }
                    // },
                    series: {!! json_encode($taskStatistics)!!},
                    // series: [77,11,11],

                    // colors: {!! json_encode($chartData['color']) !!},
                    labels: ['Todo', 'In Progress', 'Review', 'Done'],
                    grid: {
                        borderColor: '#e7e7e7',
                        row: {
                            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                            opacity: 0.5
                        },
                    },
                    markers: {
                        size: 1
                    },
                    legend: {
                        show: false
                    }
                };
                var chart = new ApexCharts(document.querySelector("#projects-chart"), projectsChartoptions);
                chart.render();

                var columnChartoptions = {
                    series: [{
                        name: 'Completed Tasks',
                        data: {!! json_encode($result['CompletedTaskArr']) !!}
                    }, {
                        name: 'New Tasks',
                        data: {!! json_encode($result['CreatedTaskArr']) !!}
                    }, {
                        name: 'OnGoing Tasks',
                        data: {!! json_encode($result['PendingTaskArr']) !!}
                    }],
                    chart: {
                        type: 'bar',
                        height: 350
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            endingShape: 'rounded'
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: {!! json_encode($result['MonthArr']) !!},
                    },
                    yaxis: {
                        title: {
                            text: 'Tasks'
                        }
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return  val + " Tasks"
                            }
                        }
                    }
                };

                var chart = new ApexCharts(document.querySelector("#column-chart"), columnChartoptions);
                chart.render();
            })();

            setTimeout(function() {
                var taskAreaChart = new ApexCharts(document.querySelector(""), taskAreaOptions);
                taskAreaChart.render();
            }, 100);

            var projectStatusOptions = {
                series: {!! json_encode($arrProcessPer) !!},

                chart: {
                    height: '350px',
                    width: '450px',
                    type: 'pie',
                },
                colors: ["#00B8D9", "#36B37E", "#2359ee"],
                labels: {!! json_encode($arrProcessLabel) !!},

                plotOptions: {
                    pie: {
                        dataLabels: {
                            offset: -5
                        }
                    }
                },
                title: {
                    text: ""
                },
                dataLabels: {},
                legend: {
                    display: false
                },

            };
            var projectStatusChart = new ApexCharts(document.querySelector("#project-status-chart"), projectStatusOptions);
            projectStatusChart.render();
        </script>
    @endif


    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    @if (Auth::user()->type == 'admin')
        <script>
            (function() {
                var options = {
                    chart: {
                        height: 150,
                        type: 'area',
                        toolbar: {
                            show: false,
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 2,
                        curve: 'smooth'
                    },
                    series: {!! json_encode($chartData['data']) !!},
                    xaxis: {
                        categories: {!! json_encode($chartData['label']) !!},
                    },
                    colors: ['#ffa21d', '#FF3A6E'],

                    grid: {
                        strokeDashArray: 4,
                    },
                    legend: {
                        show: false,
                    },
                    markers: {
                        size: 4,
                        colors: ['#ffa21d', '#FF3A6E'],
                        opacity: 0.9,
                        strokeWidth: 2,
                        hover: {
                            size: 7,
                        }
                    },
                    yaxis: {
                        tickAmount: 3,
                        min: 10,
                        max: 70,
                    }
                };
                var chart = new ApexCharts(document.querySelector("#task-area-chart"), options);
                chart.render();
            })();
        </script>
    @elseif(isset($currentWorkspace) && $currentWorkspace)
        <script>
            (function() {

                //project status chart
                var options = {
                    series: [{
                        name: 'Ongoing Projects',
                        data: {!! json_encode($chartData['total_ongoing_projects']) !!}
                    }, {
                        name: 'Finished Projects',
                        data: {!! json_encode($chartData['total_finished_projects']) !!}
                    }],
                    chart: {
                        type: 'bar',
                        height: 430
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            dataLabels: {
                                position: 'top',
                            },
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        offsetX: -6,
                        style: {
                            fontSize: '12px',
                            colors: ['#fff']
                        }
                    },
                    stroke: {
                        show: true,
                        width: 1,
                        colors: ['#fff']
                    },
                    tooltip: {
                        shared: true,
                        intersect: false
                    },
                    xaxis: {
                        categories: [2017, 2018, 2019, 2020, 2021, 2022, 2023],
                    },
                };

                var chart = new ApexCharts(document.querySelector("#project-status"), options);
                chart.render();

                //project status chart

                var options = {
                    chart: {
                        height: 150,
                        type: 'area',
                        toolbar: {
                            show: false,
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 2,
                        curve: 'smooth'
                    },
                    // series: [
                    //     @foreach ($chartData['stages'] as $id => $name)
                    //         {
                    //             name: "{{ __($name) }}",
                    //             data: {!! json_encode($chartData[$id]) !!}
                    //         },
                    //     @endforeach
                    // ],

                    series: [{
                            name: 'todo',
                            data: [20, 50, 30, 60, 40, 50, 40]
                        },
                        {
                            name: 'In Progress',
                            data: [30, 60, 40, 70, 50, 60, 50]
                        },
                        {
                            name: 'Review',
                            data: [40, 70, 50, 80, 60, 70, 60]
                        }, {
                            name: 'Done',
                            data: [50, 80, 60, 90, 70, 80, 70]
                        }
                    ],
                    xaxis: {
                        categories: {!! json_encode($chartData['label']) !!},
                        title: {
                            text: '{{ __('Days') }}'
                        }
                    },
                    colors: {!! json_encode($chartData['color']) !!},

                    grid: {
                        strokeDashArray: 4,
                    },
                    legend: {
                        show: false,
                    },
                    markers: {
                        size: 4,
                        colors: ['#ffa21d', '#FF3A6E'],
                        opacity: 0.9,
                        strokeWidth: 2,
                        hover: {
                            size: 7,
                        }
                    },
                    yaxis: {
                        tickAmount: 3,
                        min: 10,
                        max: 70,
                    },
                    title: {
                        text: '{{ __('Tasks') }}'
                    },
                };
                var chart = new ApexCharts(document.querySelector("#task-area-chart"), options);
                chart.render();



                var options = {
                    chart: {
                        height: 150,
                        type: 'area',
                        toolbar: {
                            show: false,
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 2,
                        curve: 'smooth'
                    },
                    // series: [
                    //     @foreach ($chartData['stages'] as $id => $name)
                    //         {
                    //             name: "{{ __($name) }}",
                    //             data: {!! json_encode($chartData[$id]) !!}
                    //         },
                    //     @endforeach
                    // ],

                    series: [{
                            name: 'Ongoing',
                            data: [20, 50, 30, 60, 40, 50, 40]
                        },
                        {
                            name: 'Finished',
                            data: [30, 60, 40, 70, 50, 60, 50]
                        }

                    ],
                    xaxis: {
                        categories: {!! json_encode($chartData['label']) !!},
                        title: {
                            text: '{{ __('Days') }}'
                        }
                    },
                    colors: {!! json_encode($chartData['color']) !!},

                    grid: {
                        strokeDashArray: 4,
                    },
                    legend: {
                        show: false,
                    },
                    markers: {
                        size: 4,
                        colors: ['#ffa21d', '#FF3A6E'],
                        opacity: 0.9,
                        strokeWidth: 2,
                        hover: {
                            size: 7,
                        }
                    },
                    yaxis: {
                        tickAmount: 3,
                        min: 10,
                        max: 70,
                    },
                    title: {
                        text: '{{ __('Projects') }}'
                    },
                };
                var chart = new ApexCharts(document.querySelector("#project-area-chart"), options);
                chart.render();
            })();
        </script>
    @endif
@endpush
