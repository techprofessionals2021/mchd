@extends('layouts.super-admin.super-admin')

@section('page-title')
    {{ __('Dashboard') }}
@endsection
@php
	$client_keyword = Auth::user()->getGuard() == 'client' ? 'client.' : '';
@endphp
@section('content')

    <section class="section">
        @if (Auth::user()->type == 'super-admin')
            <div class="row">
                {{-- <div class="col-12">
                    @if (empty(env('PUSHER_APP_ID')) || empty(env('PUSHER_APP_KEY')) || empty(env('PUSHER_APP_SECRET')) || empty(env('PUSHER_APP_CLUSTER')))
                        <div class="alert alert-warning"><i class="fas fa-warning"></i>
                            {{ __('Please Add Pusher Detail in Setting Page ') }}<u><a
                                    href="{{ route('settings.index') }}">{{ __('here') }}</a></u></div>
                    @endif
                    @if (empty(env('MAIL_DRIVER')) || empty(env('MAIL_HOST')) || empty(env('MAIL_PORT')) || empty(env('MAIL_USERNAME')) || empty(env('MAIL_PASSWORD')) || empty(env('MAIL_PASSWORD')))
                        <div class="alert alert-warning"><i class="fas fa-warning"></i>
                            {{ __('Please Add Mail Details in Setting Page ') }} <u><a
                                    href="{{ route('settings.index') }}">{{ __('here') }}</a></u></div>
                    @endif
                </div> --}}
                <div class="col-lg-7 col-md-7 col-sm-7">
                    <div class="row">

                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-info">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">
                                       {{ __('User') }} : <strong>{{ $user }}</strong></p>
                                    <h6 class="mb-3">{{ __('Total Users') }}</h6>
                                    <h3 class="mb-0">{{  $user }} <span
                                            class="text-success text-sm"></span></h3>
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

                                        {{ __('Workspaces') }} : <strong>{{$workspace}}</strong></p>
                                    <h6 class="mb-3">{{ __('Total Workspace') }}</h6>
                                    <h3 class="mb-0">{{$workspace}} <span
                                            class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-dark">
                                        <i class="fas fa-calendar-alt bg-primary text-white"></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">

                                        {{ __('Total Tasks') }} : <strong>{{$task}}</strong></p>
                                    <h6 class="mb-3">{{ __('Total Tasks') }}</h6>
                                    <h3 class="mb-0">{{$task}} <span
                                            class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-yellow-600">
                                        <i class="fas fa-tasks "></i>
                                    </div>
                                    <p class="text-muted text-sm mt-4 mb-2">

                                        {{ __('Total Projects') }} : <strong>{{$project}}</strong></p>
                                    <h6 class="mb-3">{{ __('Total Projects') }}</h6>
                                    <h3 class="mb-0">{{ $project }} <span
                                            class="text-success text-sm"></span></h3>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- <div class="col-lg-5 col-md-5 col-sm-5">
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
                </div> --}}
            </div>

    
        @endif
    </section>

@endsection


@push('scripts')
    <script src="{{ asset('assets/custom/js/apexcharts.min.js') }}"></script>

    @if (Auth::user()->type == 'admin')
   
        <script>
            (function() {
                var options = {
                    chart: {
                        height: 200,
                        type: 'donut',
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                            }
                        }
                    },
                    series: {!! json_encode($arrProcessPer) !!},

                    colors: {!! json_encode($chartData['color']) !!},
                    labels: {!! json_encode($arrProcessLabel) !!},
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
                var chart = new ApexCharts(document.querySelector("#projects-chart"), options);
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
                var options = {
                    chart: {
                        height: 150,
                        type: 'line',
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
                    series: [
                        @foreach ($chartData['stages'] as $id => $name)
                            {
                                name: "{{ __($name) }}",
                                data: {!! json_encode($chartData[$id]) !!}
                            },
                        @endforeach
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
            })();
        </script>
    @endif
@endpush
