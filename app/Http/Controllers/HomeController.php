<?php

namespace App\Http\Controllers;

use App\Models\ClientProject;
use App\Models\Department;
use App\Models\Stage;
use App\Models\Task;
use App\Models\User;
use App\Models\UserProject;
use App\Models\UserWorkspace;
use App\Models\Project;
use App\Models\WorkspaceType;
use App\Models\Workspace;
use App\Models\Utility;
use DB;
use App\Models\ModelHasRole;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function landingPage()
    {
        return redirect('login');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($slug = '', $currentStatus = 'All')
    {


        $userObj = Auth::user();
        if ($userObj->type == 'admin') {
            $users = User::where('type', '!=', 'admin')->get();

            return view('users.index', compact('users'));
        }
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace) {
            $doneStage = Stage::where('workspace_id', '=', $currentWorkspace->id)->where('complete', '=', '1')->first();

            $check_home = 0;
            $workspace_type = WorkspaceType::get();
            $totalProject = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->count();
            $dueDateProjects = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->whereDate('end_date', '=', date("Y-m-d"))->count();
            $inProgressProjects = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('status', '=', 'Ongoing')->count();
            $overDueTasks = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->whereDate('tasks.due_date', '<=', date("Y-m-d"))->where('tasks.status', '!=', $doneStage->id)->count();
            $inProgressTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('tasks.status', '=', '82')->count();
            if ($currentWorkspace->permission == 'Owner') {
                $totalBugs = UserProject::join("bug_reports", "bug_reports.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->count();
                $totalTask = Task::whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")->count();
                $completeTask = Task::whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")->whereHas('stage', function ($query) {
                    $query->where('complete', 1);
                })->count();


                if ($currentStatus == 'All') {
                    $tasks = Task::select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])
                        ->join("stages", "stages.id", "=", "tasks.status")
                        ->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")
                        ->orderBy('tasks.id', 'desc')
                        ->get();
                } else {
                    $tasks = Task::select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])
                        ->join("stages", "stages.id", "=", "tasks.status")
                        ->orderBy('tasks.id', 'desc')
                        ->whereHas('stage', function ($query) use ($currentStatus) {
                            $query->where('name', $currentStatus);
                        })
                        ->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")
                        ->get();
                }
                $taskStatistics = $tasks->groupBy('status')->map->count()->values();
                $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();

                $taskStatisticsColors = ['Todo' => '#008ffb', 'In Progress' => '#00e396', 'Review' => '#feb019', 'Done' => '#ff4560'];

                $taskChartColor = array_intersect_key($taskStatisticsColors, array_flip($taskStatisticsKeys));

                $taskCounts = $tasks->groupBy('status')->map->count();

                $totalCount = $taskCounts->sum();

                $taskPercentages = $taskCounts->map(function ($count) use ($totalCount) {
                    return ($count / $totalCount) * 100;
                });

                // dd($taskPercentages);


                $projects = Project::join("user_projects", "projects.id", "=", "user_projects.project_id")
                    ->join("projects as p2", "p2.id", "=", "user_projects.project_id") // Change alias here
                    ->where("user_id", "=", $userObj->id)
                    ->where('p2.workspace', '=', $currentWorkspace->id) // Use the new alias here
                    ->orderBy('projects.id', 'desc')
                    ->limit(5)
                    ->get();
            } else {
                $totalBugs = UserProject::join("bug_reports", "bug_reports.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('bug_reports.assign_to', '=', $userObj->id)->count();
                $totalTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")->count();
                $completeTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")->where('tasks.status', '=', $doneStage->id)->count();
                $overDueTasks = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")
                    ->join("projects", "projects.id", "=", "user_projects.project_id")
                    ->where("user_id", "=", $userObj->id)
                    ->where('projects.workspace', '=', $currentWorkspace->id)
                    ->whereDate('tasks.due_date', '<=', date("Y-m-d"))
                    ->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")
                    ->where('tasks.status', '!=', $doneStage->id)->count();


                if ($currentStatus == 'All') {
                    $tasks = Task::select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
                        ->join("projects", "projects.id", "=", "user_projects.project_id")
                        ->join("stages", "stages.id", "=", "tasks.status")
                        ->where("user_id", "=", $userObj->id)
                        ->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")
                        ->orderBy('tasks.id', 'desc')
                        ->get();
                } else {
                    $tasks = Task::select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
                        ->join("projects", "projects.id", "=", "user_projects.project_id")
                        ->join("stages", "stages.id", "=", "tasks.status")
                        ->where("user_id", "=", $userObj->id)
                        ->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")
                        ->orderBy('tasks.id', 'desc')
                        ->whereHas('stage', function ($query) use ($currentStatus) {
                            $query->where('name', $currentStatus);
                        })
                        ->get();
                }
                $taskStatistics = $tasks->groupBy('status')->map->count()->values();
                $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();
                $taskStatisticsColors = ['Todo' => '#008FFB', 'In Progress' => '#00E396', 'Review' => '#FEB019', 'Done' => '#FF4560'];
                $taskChartColor = array_intersect_key($taskStatisticsColors, array_flip($taskStatisticsKeys));
                $taskCounts = $tasks->groupBy('status')->map->count();
                $totalCount = $taskCounts->sum();
                $taskPercentages = $taskCounts->map(function ($count) use ($totalCount) {
                    return ($count / $totalCount) * 100;
                });
                $projects = Project::join("user_projects", "projects.id", "=", "user_projects.project_id")
                    ->join("projects as p2", "p2.id", "=", "user_projects.project_id") // Change alias here
                    ->where("user_id", "=", $userObj->id)
                    ->where('p2.workspace', '=', $currentWorkspace->id) // Use the new alias here
                    ->orderBy('projects.id', 'desc')
                    ->limit(5)
                    ->get();
            }



            $projects = Project::join("user_projects", "projects.id", "=", "user_projects.project_id")
                ->join("projects as p2", "p2.id", "=", "user_projects.project_id") // Change alias here
                ->where("user_id", "=", $userObj->id)
                ->where('p2.workspace', '=', $currentWorkspace->id) // Use the new alias here
                ->orderBy('projects.id', 'desc')
                ->limit(5)
                ->get();
            $totalMembers = UserWorkspace::where('workspace_id', '=', $currentWorkspace->id)->count();

            $projectProcess = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->groupBy('projects.status')->selectRaw('count(projects.id) as count, projects.status')->pluck('count', 'projects.status');
            $arrProcessPer = [];
            $arrProcessLabel = [];
            foreach ($projectProcess as $lable => $process) {
                $arrProcessLabel[] = $lable;
                if ($totalProject == 0) {
                    $arrProcessPer[] = 0.00;
                } else {
                    $arrProcessPer[] = round(($process * 100) / $totalProject, 2);
                }
            }
            // dd($arrProcessPer);
            $arrProcessClass = [
                'text-success',
                'text-primary',
                'text-danger',
            ];

            $projectController = new ProjectReportController();

            $chartData = $projectController->getProjectChart([
                'workspace_id' => $currentWorkspace->id,
                'duration' => 'week',
            ]);

            $MonthArr = [];
            $CompletedTaskArr = [];
            $PendingTaskArr = [];
            $CreatedTaskArr = [];


            $report = Task::join('stages', 'tasks.status', '=', 'stages.id')
                ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                ->selectRaw('SUM(CASE WHEN stages.name = "Done"  THEN 1 ELSE 0 END) as total_completed_task')
                ->selectRaw('SUM(CASE WHEN stages.name = "In Progress"  THEN 1 ELSE 0 END) as total_pending_task')
                ->selectRaw('COUNT(*) as total_created_task')
                ->where('tasks.assign_to', $userObj->id)
                ->groupBy('month')
                ->get();


            $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {

                array_push($MonthArr, $item->month);

                array_push($CompletedTaskArr, $item->total_completed_task);

                array_push($PendingTaskArr, $item->total_pending_task);

                array_push($CreatedTaskArr, $item->total_created_task);
            });


            $reportData = $report->values()->all();

            $result = [
                'MonthArr' => $MonthArr,
                'CompletedTaskArr' => $CompletedTaskArr,
                'PendingTaskArr' => $PendingTaskArr,
                'CreatedTaskArr' => $CreatedTaskArr,

            ];
            $taskStatus = ['All', 'Todo', 'In Progress', 'Review', 'Done'];
            $blade_type = '';
            return view(
                'home',
                compact(
                    'blade_type',
                    'currentStatus',
                    'taskStatus',
                    'taskChartColor',
                    'taskStatisticsKeys',
                    'currentWorkspace',
                    'totalProject',
                    'totalBugs',
                    'totalTask',
                    'totalMembers',
                    'arrProcessLabel',
                    'arrProcessPer',
                    'arrProcessClass',
                    'completeTask',
                    'tasks',
                    'chartData',
                    'inProgressProjects',
                    'dueDateProjects',
                    'inProgressTask',
                    'overDueTasks',
                    'workspace_type',
                    'projects',
                    'taskStatistics',
                    'result',
                    'taskPercentages',
                    'check_home'


                )
            );
            // }
        } else {
            return view('home', compact('currentWorkspace'));
        }
    }


    public function index_report($slug = '', $currentStatus = 'All')
    {

        $userObj = Auth::user();
        $workspace_type = WorkspaceType::get();

        if ($userObj->type == 'admin') {
            $users = User::where('type', '!=', 'admin')->get();

            return view('users.index', compact('users'));
        }
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace) {
            $doneStage = Stage::where('workspace_id', '=', $currentWorkspace->id)->where('complete', '=', '1')->first();
            if (auth()->user()->hasRole('CEO')) {
                // FOR CEO
                $check_home = 1;
                $model_has_role_ceo = ModelHasRole::where('model_id', auth()->id())->first();
                $executives_id = json_decode($model_has_role_ceo->executives);
                $Executives = User::whereIn('id', $executives_id)->get();
                // global variables
                $totalTask = 0;
                $completeTask = 0;
                $overDueTasks = 0;
                $tasks = new Collection();

                $report =  [];

                $MonthArr = [];
                $CompletedTaskArr = [];
                $PendingTaskArr = [];
                $CreatedTaskArr = [];

                if(is_array($executives_id))
                // for executive
                foreach ($executives_id as $key => $single_executive_id) {
                    $model_has_role_executive = ModelHasRole::where('model_id', $single_executive_id)->first();
                    $HODs_id = json_decode($model_has_role_executive->hods);
                    if (is_null($HODs_id)) {
                        $HODs_id = [];

                    }
                    $HODs = User::whereIn('id', $HODs_id)->get();
                    // global variables
                    $executiveTasks = new collection();
                    if (is_array($HODs_id)) {
                        foreach ($HODs_id as $key => $singleHodId) {
                            $model_has_role = ModelHasRole::where('model_id', $singleHodId)->first();
                            $workspaces = $model_has_role->workspace_id;
                            $workspace_id = json_decode($workspaces);

                            //for workspaces query

                            if (is_array($workspace_id)) {

                                $hod_workspaces = Workspace::
                                    whereIn('workspaces.id', $workspace_id)
                                    ->get();

                                $department_id = json_decode($model_has_role->department_id);
                                $depart_user_role_id = json_decode($model_has_role->depart_user_role_id);
                                $doneStage = Stage::whereIn('workspace_id', $workspace_id)->where('complete', '=', '1')->pluck('id');



                                if (is_array($department_id)) {

                                    if (is_array($depart_user_role_id)) {

                                        //for all tasks
                                        $nestedtasks = Task::with('project', 'stage')->select([
                                            'tasks.*',
                                            'stages.name as status',
                                            'stages.complete',
                                        ])->join("stages", "stages.id", "=", "tasks.status")
                                        ->whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                                $query->whereIn('workspace', $workspace_id);
                                                $query->whereIn('department_id', $department_id);
                                        })
                                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                                $query->whereIn('role_id', $depart_user_role_id);
                                            });
                                        })
                                        ->orderBy('tasks.id', 'desc')->get();
                                        //for all tasks


                                        //for complete task
                                        $completeTask += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                            $query->whereIn('workspace', $workspace_id);
                                            $query->whereIn('department_id', $department_id);
                                        })
                                        ->whereHas('stage', function ($query) use ($doneStage) {
                                            $query->whereIn('status', $doneStage);
                                        })
                                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                                $query->whereIn('role_id', $depart_user_role_id);
                                            });
                                        })
                                        ->count();
                                        //for complete task


                                        //for total task
                                        $totalTask += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                            $query->whereIn('workspace', $workspace_id);
                                            $query->whereIn('department_id', $department_id);
                                         })
                                         ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                                $query->whereIn('role_id', $depart_user_role_id);
                                            });
                                        })
                                        ->count();
                                        //for total task

                                        //for over due task
                                        $overDueTasks += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                            $query->whereIn('workspace', $workspace_id);
                                            $query->whereIn('department_id', $department_id);
                                        })
                                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                                $query->whereIn('role_id', $depart_user_role_id);
                                            });
                                        })
                                        ->whereHas('stage', function ($query) use ($doneStage) {
                                            $query->whereNotIn('status' , $doneStage);
                                        })
                                        ->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                                         //for over due task


                                        //for monthly task report


                                        $nestedReport = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                                        ->join('stages', 'tasks.status', '=', 'stages.id')
                                        ->whereIn('projects.workspace', $workspace_id)
                                        ->whereIn('projects.department_id', $department_id)
                                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                                $query->whereIn('role_id', $depart_user_role_id);
                                            });
                                        })
                                        ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                                        ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                                        ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                                        ->selectRaw('COUNT(*) as total_created_task')
                                        ->groupBy('projects.workspace', 'month')
                                        ->orderBy('projects.workspace')
                                        ->orderBy('month')
                                        ->get();

                                    } else { //no  depart user role


                                        //for all tasks
                                        $nestedtasks = Task::with('project', 'stage')->select([
                                            'tasks.*',
                                            'stages.name as status',
                                            'stages.complete',
                                        ])->join("stages", "stages.id", "=", "tasks.status")
                                        ->whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                                $query->whereIn('workspace', $workspace_id);
                                                $query->whereIn('department_id', $department_id);
                                        })->orderBy('tasks.id', 'desc')->get();
                                        //for all tasks


                                        //for complete task
                                        $completeTask += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                            $query->whereIn('workspace', $workspace_id);
                                            $query->whereIn('department_id', $department_id);
                                        })

                                        ->whereHas('stage', function ($query) use ($doneStage) {
                                            $query->whereIn('status', $doneStage);
                                        })->count();
                                        //for complete task


                                        //for total task
                                        $totalTask += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                            $query->whereIn('workspace', $workspace_id);
                                            $query->whereIn('department_id', $department_id);
                                         })->count();
                                        //for total task

                                        //for over due task
                                        $overDueTasks += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                            $query->whereIn('workspace', $workspace_id);
                                            $query->whereIn('department_id', $department_id);
                                        })
                                        ->whereHas('stage', function ($query) use ($doneStage) {
                                            $query->whereNotIn('status' , $doneStage);
                                        })
                                        ->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                                         //for over due task



                                        //for monthly task report


                                        $nestedReport = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                                        ->join('stages', 'tasks.status', '=', 'stages.id')
                                        ->whereIn('projects.workspace', $workspace_id)
                                        ->whereIn('projects.department_id', $department_id)
                                        ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                                        ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                                        ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                                        ->selectRaw('COUNT(*) as total_created_task')
                                        ->groupBy('projects.workspace', 'month')
                                        ->orderBy('projects.workspace')
                                        ->orderBy('month')
                                        ->get();
                                    }



                                } else {  // no depart

                                    if (is_array($depart_user_role_id)) {


                                        //for all tasks
                                        $nestedtasks = Task::with('project', 'stage')->select([
                                            'tasks.*',
                                            'stages.name as status',
                                            'stages.complete',
                                        ])->join("stages", "stages.id", "=", "tasks.status")
                                        ->whereHas('project', function ($query) use ($workspace_id) {
                                                $query->whereIn('workspace', $workspace_id);
                                        })
                                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                                $query->whereIn('role_id', $depart_user_role_id);
                                            });
                                        })->orderBy('tasks.id', 'desc')->get();
                                        //for all tasks

                                        //for complete task
                                        $completeTask += Task::whereHas('project', function ($query) use (&$workspace_id) {
                                            $query->whereIn('workspace', $workspace_id);
                                        })
                                        ->whereHas('stage', function ($query) use ($doneStage) {
                                            $query->whereIn('status', $doneStage);
                                        })
                                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                                $query->whereIn('role_id', $depart_user_role_id);
                                            });
                                        })->count();
                                        //for complete task



                                       //for total task
                                        $totalTask += Task::whereHas('project', function ($query) use (&$workspace_id) {
                                            $query->whereIn('workspace', $workspace_id);
                                        })
                                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                                $query->whereIn('role_id', $depart_user_role_id);
                                            });
                                        })->count();
                                        //for total task

                                        //for over due task
                                        $overDueTasks += Task::whereHas('project', function ($query) use (&$workspace_id) {
                                            $query->whereIn('workspace', $workspace_id);
                                        })
                                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                                $query->whereIn('role_id', $depart_user_role_id);
                                            });
                                        })->whereHas('stage', function ($query) use ($doneStage) {
                                            $query->whereNotIn('status' , $doneStage);
                                        })->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                                         //for over due task


                                        //for monthly task report
                                        $nestedReport = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                                        ->join('stages', 'tasks.status', '=', 'stages.id')
                                        ->whereIn('projects.workspace', $workspace_id)
                                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                                $query->whereIn('role_id', $depart_user_role_id);
                                            });
                                        })
                                        ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                                        ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                                        ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                                        ->selectRaw('COUNT(*) as total_created_task')
                                        ->groupBy('projects.workspace', 'month')
                                        ->orderBy('projects.workspace')
                                        ->orderBy('month')
                                        ->get();

                                    } else { // no  depart user role



                                        //for all tasks
                                        $nestedtasks = Task::with('project', 'stage')->select([
                                            'tasks.*',
                                            'stages.name as status',
                                            'stages.complete',
                                        ])->join("stages", "stages.id", "=", "tasks.status")
                                        ->whereHas('project', function ($query) use ($workspace_id) {
                                                $query->whereIn('workspace', $workspace_id);
                                        })->orderBy('tasks.id', 'desc')->get();
                                        //for all tasks


                                        //for complete task
                                        $completeTask += Task::whereHas('project', function ($query) use (&$workspace_id) {
                                            $query->whereIn('workspace', $workspace_id);
                                        })
                                        ->whereHas('stage', function ($query) use ($doneStage) {
                                            $query->whereIn('status', $doneStage);
                                        })->count();
                                        //for complete task


                                        //for total task
                                        $totalTask += Task::whereHas('project', function ($query) use (&$workspace_id) {
                                            $query->whereIn('workspace', $workspace_id);
                                        })->count();
                                        //for total task

                                        //for over due task
                                        $overDueTasks += Task::whereHas('project', function ($query) use (&$workspace_id) {
                                            $query->whereIn('workspace', $workspace_id);
                                        })->whereHas('stage', function ($query) use ($doneStage) {
                                            $query->whereNotIn('status' , $doneStage);
                                        })->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                                        //for over due task


                                        //for monthly task report


                                        $nestedReport = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                                        ->join('stages', 'tasks.status', '=', 'stages.id')
                                        ->whereIn('projects.workspace', $workspace_id)
                                        ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                                        ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                                        ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                                        ->selectRaw('COUNT(*) as total_created_task')
                                        ->groupBy('projects.workspace', 'month')
                                        ->orderBy('projects.workspace')
                                        ->orderBy('month')
                                        ->get();





                                         //for monthly task report
                                    }

                                }

                                // merge nested tasks to tasks
                                $executiveTasks = $executiveTasks->merge($nestedtasks);
                                // $report = $report->merge($nestedReport);
                                $report[] = $nestedReport;
                                // dd($nestedReport);


                            }
                        }

                    }
                    $tasks = $tasks->merge($executiveTasks);
                }
                //for executive
                // for monthly progress reports
                $report = collect($report)->collapse()->groupBy('month');
                $report->each(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                    $MonthArr[] = $item->first()->month; // Assuming all instances in the group have the same month
                    $CompletedTaskArr[] = $item->sum('total_completed_task');
                    $PendingTaskArr[] = $item->sum('total_pending_task');
                    $CreatedTaskArr[] = $item->sum('total_created_task');
                });
                $reportData = $report->values()->all();
                $result = [
                    'MonthArr' => $MonthArr,
                    'CompletedTaskArr' => $CompletedTaskArr,
                    'PendingTaskArr' => $PendingTaskArr,
                    'CreatedTaskArr' => $CreatedTaskArr,
                ];
                // for monthly progress reports

                // dd($totalTask);
                //for task statictis chart
                $taskStatistics = $tasks->groupBy('status')->map->count()->values();
                $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();
                $taskStatisticsColors = ['Todo' => '#008FFB', 'In Progress' => '#00E396', 'Review' => '#FEB019', 'Done' => '#FF4560'];
                $taskChartColor = array_intersect_key($taskStatisticsColors, array_flip($taskStatisticsKeys));
                $taskCounts = $tasks->groupBy('status')->map->count();
                $totalCount = $taskCounts->sum();
                $taskPercentages = $taskCounts->map(function ($count) use ($totalCount) {
                    return ($count / $totalCount) * 100;
                });
                //for task statictis chart

                $chartData = [];
                $blade_type = 'Ceo';
                $taskStatus = ['All', 'Todo', 'In Progress', 'Review', 'Done'];

                return view(
                    'home',
                    compact(
                        'taskChartColor',
                        'currentStatus',
                        'taskStatus',
                        'blade_type',
                        'taskStatisticsKeys',
                        'currentWorkspace',
                        // 'totalProject',
                        'totalTask',
                        // 'totalMembers',
                        // 'arrProcessLabel',
                        // 'arrProcessPer',
                        // 'arrProcessClass',
                        'completeTask',
                        'tasks',
                        'chartData',
                        // 'inProgressProjects',
                        // 'dueDateProjects',
                        // 'inProgressTask',
                        'overDueTasks',
                        'workspace_type',
                        // 'projects',
                        'taskStatistics',
                        'result',
                        'taskPercentages',
                        // 'hod_workspaces',
                        'check_home',
                        // 'departmentList',
                        'Executives'
                    )
                );
            }



                // FOR CEO

                // $check_home = 1;
                // $model_has_role_ceo = ModelHasRole::where('model_id', auth()->id())->first();
                // $executives_id = json_decode($model_has_role_ceo->executives);
                // $Executives = User::whereIn('id', $executives_id)->get();
                // $totalTask = 0;
                // $completeTask = 0;
                // $overDueTasks = 0;

                // $tasks = new Collection();

                // if (is_array($executives_id)) {
                //     foreach ($executives_id as $key => $executive_id) {
                //         $model_has_role_executive = ModelHasRole::where('model_id', $executive_id)->first();
                //         $HODs_id = json_decode($model_has_role_executive->hods);
                //         $executiveTasks = new Collection();
                //         if (!is_null($HODs_id)) {


                //             foreach ($HODs_id as $key => $hod_id) {
                //                 $model_has_role = ModelHasRole::where('model_id', $hod_id)->first();
                //                 $workspaces = $model_has_role->workspace_id;
                //                 $workspace_id = json_decode($workspaces);
                //                 $department_id = json_decode($model_has_role->department_id);
                //                 $depart_user_role_id = json_decode($model_has_role->depart_user_role_id);

                //                 if (is_array($department_id)) {

                //                     if (is_null($depart_user_role_id)) {

                //                         $workspace = Workspace::whereIn('id', $workspace_id)->first();
                //                         $departments = Department::whereIn('id', $department_id)->get();

                //                         foreach ($departments as $department) {
                //                             //completed tasks
                //                             $doneStage = Stage::where('workspace_id', '=', $workspace->id)->where('complete', '=', '1')->first();
                //                             //   dd($doneStage);
                //                             $completeTask += $department->projects->flatMap(function ($project) use ($doneStage) {
                //                                 return $project->task->where('status', $doneStage->id);
                //                             })->count();


                //                             //
                //                             $totalTask += $department->projects->flatMap(function ($project) {
                //                                 return $project->task;
                //                             })->count();
                //                             $overDueTasks += $department->projects->flatMap(function ($project) use ($doneStage) {
                //                                 return $project->task->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->where('status', '!=', $doneStage->id);
                //                             })->count();
                //                         }


                //                         if ($currentStatus == 'All') {
                //                             $nestedtasks = Task::with('project', 'stage')->select([
                //                                 'tasks.*',
                //                                 'stages.name as status',
                //                                 'stages.complete',
                //                             ])->join("stages", "stages.id", "=", "tasks.status")
                //                                 ->whereHas('project', function ($query) use ($workspace_id, $department_id) {
                //                                     $query->whereIn('department_id', $department_id);
                //                                     // $query->whereIn('workspace', $workspace_id);
                //                                 })->orderBy('tasks.id', 'desc')->get();

                //                         } else {
                //                             $nestedtasks = Task::with('project', 'stage')->select([
                //                                 'tasks.*',
                //                                 'stages.name as status',
                //                                 'stages.complete',
                //                             ])->join("stages", "stages.id", "=", "tasks.status")
                //                                 ->whereHas('project', function ($query) use ($workspace_id, $department_id) {
                //                                     $query->whereIn('department_id', $department_id);
                //                                     // $query->whereIn('workspace', $workspace_id);
                //                                 })->orderBy('tasks.id', 'desc')
                //                                 ->whereHas('stage', function ($query) use ($currentStatus) {
                //                                     $query->where('name', $currentStatus);
                //                                 })
                //                                 ->get();
                //                         }

                //                         $executiveTasks = $executiveTasks->merge($nestedtasks);
                //                         // dd($tasks->merge($nestedtasks));
                //                     } else {

                //                         $workspace = Workspace::whereIn('id', $workspace_id)->first();
                //                         $departments = Department::whereIn('id', $department_id)->get();

                //                         foreach ($departments as $department) {
                //                             //completed tasks
                //                             $doneStage = Stage::where('workspace_id', '=', $workspace->id)->where('complete', '=', '1')->first();
                //                             //   dd($doneStage);
                //                             // $completeTask += $department->projects->flatMap(function ($project) use ($doneStage,&$department,&$depart_user_role_id) {
                //                             //     return $project->task->where('status', $doneStage->id)
                //                             //     ->filter(function($task)use($department,&$depart_user_role_id){
                //                             //         return $task->assignees()->whereHas('departments',function($query)use(&$department,&$depart_user_role_id){
                //                             //             $query->where('department_id', $department->id)
                //                             //             ->whereIn('role_id', $depart_user_role_id);
                //                             //         })
                //                             //         ->get()->isNotEmpty();
                //                             //      });
                //                             // })->count();


                //                             $completeTask = Task::whereHas('project', function ($query) use (&$workspace_id, &$department_id) {
                //                                 $query->whereIn('workspace', $workspace_id)->whereIn('department_id', $department_id);
                //                             })
                //                                 ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                //                                     $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                //                                         $query->whereIn('role_id', $depart_user_role_id);
                //                                     });
                //                                 })
                //                                 ->whereHas('stage', function ($query) use ($doneStage) {
                //                                     $query->whereIn('status', $doneStage);
                //                                 })->count();

                //                             $totalTask = Task::whereHas('project', function ($query) use (&$workspace_id, &$department_id) {
                //                                 $query->whereIn('workspace', $workspace_id)->whereIn('department_id', $department_id);
                //                             })
                //                                 ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                //                                     $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                //                                         $query->whereIn('role_id', $depart_user_role_id);
                //                                     });
                //                                 })
                //                                 ->count();


                //                             $overDueTasks = Task::whereHas('project', function ($query) use (&$workspace_id, &$department_id) {
                //                                 $query->whereIn('workspace', $workspace_id)->whereIn('department_id', $department_id);
                //                             })
                //                                 ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                //                                     $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                //                                         $query->whereIn('role_id', $depart_user_role_id);
                //                                     });
                //                                 })
                //                                 ->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();

                //                             //
                //                             // $totalTask +=  $department->projects->flatMap(function ($project) use($department,&$depart_user_role_id){
                //                             //   return  $project->task->filter(function($task)use($department,&$depart_user_role_id){
                //                             //     //
                //                             //        return $task->assignees()->whereHas('departments',function($query)use(&$department,&$depart_user_role_id){
                //                             //             $query->where('department_id', $department->id)
                //                             //             ->whereIn('role_id', $depart_user_role_id);
                //                             //         })
                //                             //         ->get()->isNotEmpty();
                //                             //     //
                //                             //     //    $filteredUsers = $task->filterTaskUsersByRoles($department->id, $depart_user_role_id);
                //                             //     //     $assignedUserIds = explode(',', $task->assign_to);
                //                             //     //     // Check if any of the assigned user IDs are in the filtered users
                //                             //     //     return collect($assignedUserIds)->intersect($filteredUsers->pluck('id'))->isNotEmpty();
                //                             //     });
                //                             // })->count();
                //                             // // dd($totalTask);
                //                             // $overDueTasks += $department->projects->flatMap(function ($project) use ($doneStage,&$department,&$depart_user_role_id) {
                //                             //     return $project->task->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->where('status', '!=', $doneStage->id)
                //                             //     ->filter(function($task)use($department,&$depart_user_role_id){
                //                             //         return $task->assignees()->whereHas('departments',function($query)use(&$department,&$depart_user_role_id){
                //                             //             $query->where('department_id', $department->id)
                //                             //             ->whereIn('role_id', $depart_user_role_id);
                //                             //         })
                //                             //         ->get()->isNotEmpty();
                //                             //      });
                //                             // })->count();
                //                         }

                //                         if ($currentStatus == 'All') {
                //                             $nestedtasks = Task::with('project', 'stage')->select([
                //                                 'tasks.*',
                //                                 'stages.name as status',
                //                                 'stages.complete',
                //                             ])->join("stages", "stages.id", "=", "tasks.status")
                //                                 ->whereHas('project', function ($query) use ($workspace_id, &$department_id) {
                //                                     $query->whereIn('department_id', $department_id);
                //                                     $query->whereIn('workspace', $workspace_id);
                //                                 })
                //                                 ->whereHas('assignees', function ($query) use ($department_id, &$depart_user_role_id) {
                //                                     $query->whereHas('departments', function ($q) use (&$department_id, &$depart_user_role_id) {
                //                                         $q->where('department_id', $department_id)
                //                                             ->whereIn('role_id', $depart_user_role_id);
                //                                     });
                //                                     // $query->whereIn('department_id', $department_id)
                //                                     //     ->whereIn('role_id', $depart_user_role_id);
                //                                 })
                //                                 ->orderBy('tasks.id', 'desc')->get();
                //                             // dd($nestedtasks);
                //                         } else {
                //                             $nestedtasks = Task::with('project', 'stage')->select([
                //                                 'tasks.*',
                //                                 'stages.name as status',
                //                                 'stages.complete',
                //                             ])->join("stages", "stages.id", "=", "tasks.status")
                //                                 ->whereHas('project', function ($query) use ($workspace_id, $department_id) {
                //                                     $query->whereIn('department_id', $department_id);
                //                                     // $query->whereIn('workspace', $workspace_id);
                //                                 })->orderBy('tasks.id', 'desc')
                //                                 ->whereHas('stage', function ($query) use ($currentStatus) {
                //                                     $query->where('name', $currentStatus);
                //                                 })
                //                                 ->whereHas('assignees', function ($query) use ($department_id, &$depart_user_role_id) {
                //                                     $query->whereHas('departments', function ($q) use (&$department_id, &$depart_user_role_id) {
                //                                         $q->where('department_id', $department_id)
                //                                             ->whereIn('role_id', $depart_user_role_id);
                //                                     });
                //                                     // $query->whereIn('department_id', $department_id)
                //                                     //     ->whereIn('role_id', $depart_user_role_id);
                //                                 })
                //                                 ->get();


                //                         }

                //                         $executiveTasks = $executiveTasks->merge($nestedtasks);
                //                     }
                //                 }
                //             }
                //         }

                //     }
                //     $tasks = $tasks->merge($executiveTasks);
                //     $taskStatistics = $tasks->groupBy('status')->map->count()->values();
                //     $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();
                //     $taskStatisticsColors = ['Todo' => '#008FFB', 'In Progress' => '#00E396', 'Review' => '#FEB019', 'Done' => '#FF4560'];
                //     $taskChartColor = array_intersect_key($taskStatisticsColors, array_flip($taskStatisticsKeys));
                //     $taskCounts = $tasks->groupBy('status')->map->count();
                //     $totalCount = $taskCounts->sum();
                //     $taskPercentages = $taskCounts->map(function ($count) use ($totalCount) {
                //         return ($count / $totalCount) * 100;
                //     });

                //     $MonthArr = [];
                //     $CompletedTaskArr = [];
                //     $PendingTaskArr = [];
                //     $CreatedTaskArr = [];
                //     $report = DB::table('tasks')
                //         ->join('projects', 'tasks.project_id', '=', 'projects.id')
                //         ->join('stages', 'tasks.status', '=', 'stages.id')
                //         ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                //         ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                //         ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                //         ->selectRaw('COUNT(*) as total_created_task')
                //         // ->where('projects.workspace', $id)
                //         ->whereIn('projects.workspace', $workspace_id)
                //         ->whereIn('projects.department_id', $department_id)
                //         // ->groupBy('projects.workspace', 'month')
                //         ->groupBy('projects.department_id', 'month')
                //         // ->orderBy('projects.workspace')
                //         ->orderBy('projects.department_id')
                //         ->orderBy('month')
                //         ->get();

                //     // dd($workspace_id);
                //     $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                //         array_push($MonthArr, $item->month);
                //         array_push($CompletedTaskArr, $item->total_completed_task);
                //         array_push($PendingTaskArr, $item->total_pending_task);
                //         array_push($CreatedTaskArr, $item->total_created_task);
                //     });
                //     $reportData = $report->values()->all();
                //     $result = [
                //         'MonthArr' => $MonthArr,
                //         'CompletedTaskArr' => $CompletedTaskArr,
                //         'PendingTaskArr' => $PendingTaskArr,
                //         'CreatedTaskArr' => $CreatedTaskArr,
                //     ];
                //     // $inProgressTask = Task::whereIn('projects.workspace', $workspace_id)
                //     //     ->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
                //     //     ->join("projects", "projects.id", "=", "user_projects.project_id")
                //     //     ->where('tasks.status', '=', '82')->count();

                //     $inProgressTask = Task::
                //         join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
                //         ->join("projects", "projects.id", "=", "user_projects.project_id")
                //         ->where('tasks.status', '=', '82')->count();

                //     $chartData = [];
                //     $blade_type = 'Ceo';
                //     $taskStatus = ['All', 'Todo', 'In Progress', 'Review', 'Done'];

                //     return view(
                //         'home',
                //         compact(
                //             'taskChartColor',
                //             'currentStatus',
                //             'taskStatus',
                //             'blade_type',
                //             'taskStatisticsKeys',
                //             'currentWorkspace',
                //             // 'totalProject',
                //             'totalTask',
                //             // 'totalMembers',
                //             // 'arrProcessLabel',
                //             // 'arrProcessPer',
                //             // 'arrProcessClass',
                //             'completeTask',
                //             'tasks',
                //             'chartData',
                //             // 'inProgressProjects',
                //             // 'dueDateProjects',
                //             'inProgressTask',
                //             'overDueTasks',
                //             'workspace_type',
                //             // 'projects',
                //             'taskStatistics',
                //             'result',
                //             'taskPercentages',
                //             // 'hod_workspaces',
                //             'check_home',
                //             // 'departmentList',
                //             'Executives'
                //         )
                //     );
                // } else {
                //     return redirect()->back()->with('error', __("No Executive Found Under This CEO "));
                // }

            }

            if (auth()->user()->hasRole('HOD')) {


                $check_home = 1;
                $model_has_role = ModelHasRole::where('model_id', Auth::id())->first();
                $workspaces = $model_has_role->workspace_id;
                $workspace_id = json_decode($workspaces);

                //for workspaces query

                if (is_array($workspace_id)) {

                    $hod_workspaces = Workspace::
                        whereIn('workspaces.id', $workspace_id)
                        ->get();

                    $department_id = json_decode($model_has_role->department_id);
                    $depart_user_role_id = json_decode($model_has_role->depart_user_role_id);
                    $doneStage = Stage::whereIn('workspace_id', $workspace_id)->where('complete', '=', '1')->pluck('id');

                    $MonthArr = [];
                    $CompletedTaskArr = [];
                    $PendingTaskArr = [];
                    $CreatedTaskArr = [];

                    if (is_array($department_id)) {

                        if (is_array($depart_user_role_id)) {

                            //for all tasks
                            $tasks = Task::with('project', 'stage')->select([
                                'tasks.*',
                                'stages.name as status',
                                'stages.complete',
                            ])->join("stages", "stages.id", "=", "tasks.status")
                            ->whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                    $query->whereIn('workspace', $workspace_id);
                                    $query->whereIn('department_id', $department_id);
                            })
                            ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                    $query->whereIn('role_id', $depart_user_role_id);
                                });
                            })
                            ->orderBy('tasks.id', 'desc')->get();
                            //for all tasks


                            //for complete task
                            $completeTask = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                $query->whereIn('workspace', $workspace_id);
                                $query->whereIn('department_id', $department_id);
                            })
                            ->whereHas('stage', function ($query) use ($doneStage) {
                                $query->whereIn('status', $doneStage);
                            })
                            ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                    $query->whereIn('role_id', $depart_user_role_id);
                                });
                            })
                            ->count();
                            //for complete task


                            //for total task
                            $totalTask = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                $query->whereIn('workspace', $workspace_id);
                                $query->whereIn('department_id', $department_id);
                             })
                             ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                    $query->whereIn('role_id', $depart_user_role_id);
                                });
                            })
                            ->count();
                            //for total task

                            //for over due task
                            $overDueTasks = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                $query->whereIn('workspace', $workspace_id);
                                $query->whereIn('department_id', $department_id);
                            })
                            ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                    $query->whereIn('role_id', $depart_user_role_id);
                                });
                            })
                            ->whereHas('stage', function ($query) use ($doneStage) {
                                $query->whereNotIn('status' , $doneStage);
                            })
                            ->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                             //for over due task


                            //for monthly task report


                            $report = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                            ->join('stages', 'tasks.status', '=', 'stages.id')
                            ->whereIn('projects.workspace', $workspace_id)
                            ->whereIn('projects.department_id', $department_id)
                            ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                    $query->whereIn('role_id', $depart_user_role_id);
                                });
                            })
                            ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                            ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                            ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                            ->selectRaw('COUNT(*) as total_created_task')
                            ->groupBy('projects.workspace', 'month')
                            ->orderBy('projects.workspace')
                            ->orderBy('month')
                            ->get();


                            $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                                array_push($MonthArr, $item->month);
                                array_push($CompletedTaskArr, $item->total_completed_task);
                                array_push($PendingTaskArr, $item->total_pending_task);
                                array_push($CreatedTaskArr, $item->total_created_task);
                            });
                            $reportData = $report->values()->all();
                            $result = [
                                'MonthArr' => $MonthArr,
                                'CompletedTaskArr' => $CompletedTaskArr,
                                'PendingTaskArr' => $PendingTaskArr,
                                'CreatedTaskArr' => $CreatedTaskArr,
                            ];


                        } else { //no  depart user role


                            //for all tasks
                            $tasks = Task::with('project', 'stage')->select([
                                'tasks.*',
                                'stages.name as status',
                                'stages.complete',
                            ])->join("stages", "stages.id", "=", "tasks.status")
                            ->whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                    $query->whereIn('workspace', $workspace_id);
                                    $query->whereIn('department_id', $department_id);
                            })->orderBy('tasks.id', 'desc')->get();
                            //for all tasks


                            //for complete task
                            $completeTask = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                $query->whereIn('workspace', $workspace_id);
                                $query->whereIn('department_id', $department_id);
                            })

                            ->whereHas('stage', function ($query) use ($doneStage) {
                                $query->whereIn('status', $doneStage);
                            })->count();
                            //for complete task


                            //for total task
                            $totalTask = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                $query->whereIn('workspace', $workspace_id);
                                $query->whereIn('department_id', $department_id);
                             })->count();
                            //for total task

                            //for over due task
                            $overDueTasks = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                $query->whereIn('workspace', $workspace_id);
                                $query->whereIn('department_id', $department_id);
                            })
                            ->whereHas('stage', function ($query) use ($doneStage) {
                                $query->whereNotIn('status' , $doneStage);
                            })
                            ->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                             //for over due task

                            //  dd($overDueTasks);

                            //for monthly task report


                            $report = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                            ->join('stages', 'tasks.status', '=', 'stages.id')
                            ->whereIn('projects.workspace', $workspace_id)
                            ->whereIn('projects.department_id', $department_id)
                            ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                            ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                            ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                            ->selectRaw('COUNT(*) as total_created_task')
                            ->groupBy('projects.workspace', 'month')
                            ->orderBy('projects.workspace')
                            ->orderBy('month')
                            ->get();


                            $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                                array_push($MonthArr, $item->month);
                                array_push($CompletedTaskArr, $item->total_completed_task);
                                array_push($PendingTaskArr, $item->total_pending_task);
                                array_push($CreatedTaskArr, $item->total_created_task);
                            });
                            $reportData = $report->values()->all();
                            $result = [
                                'MonthArr' => $MonthArr,
                                'CompletedTaskArr' => $CompletedTaskArr,
                                'PendingTaskArr' => $PendingTaskArr,
                                'CreatedTaskArr' => $CreatedTaskArr,
                            ];

                        }



                    } else {  // no depart

                        if (is_array($depart_user_role_id)) {


                            //for all tasks
                            $tasks = Task::with('project', 'stage')->select([
                                'tasks.*',
                                'stages.name as status',
                                'stages.complete',
                            ])->join("stages", "stages.id", "=", "tasks.status")
                            ->whereHas('project', function ($query) use ($workspace_id) {
                                    $query->whereIn('workspace', $workspace_id);
                            })
                            ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                    $query->whereIn('role_id', $depart_user_role_id);
                                });
                            })->orderBy('tasks.id', 'desc')->get();
                            //for all tasks

                            //for complete task
                            $completeTask = Task::whereHas('project', function ($query) use (&$workspace_id) {
                                $query->whereIn('workspace', $workspace_id);
                            })
                            ->whereHas('stage', function ($query) use ($doneStage) {
                                $query->whereIn('status', $doneStage);
                            })
                            ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                    $query->whereIn('role_id', $depart_user_role_id);
                                });
                            })->count();
                            //for complete task



                           //for total task
                            $totalTask = Task::whereHas('project', function ($query) use (&$workspace_id) {
                                $query->whereIn('workspace', $workspace_id);
                            })
                            ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                    $query->whereIn('role_id', $depart_user_role_id);
                                });
                            })->count();
                            //for total task

                            //for over due task
                            $overDueTasks = Task::whereHas('project', function ($query) use (&$workspace_id) {
                                $query->whereIn('workspace', $workspace_id);
                            })
                            ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                    $query->whereIn('role_id', $depart_user_role_id);
                                });
                            })->whereHas('stage', function ($query) use ($doneStage) {
                                $query->whereNotIn('status' , $doneStage);
                            })->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                             //for over due task


                            //for monthly task report
                            $report = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                            ->join('stages', 'tasks.status', '=', 'stages.id')
                            ->whereIn('projects.workspace', $workspace_id)
                            ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                    $query->whereIn('role_id', $depart_user_role_id);
                                });
                            })
                            ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                            ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                            ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                            ->selectRaw('COUNT(*) as total_created_task')
                            ->groupBy('projects.workspace', 'month')
                            ->orderBy('projects.workspace')
                            ->orderBy('month')
                            ->get();


                            $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                                array_push($MonthArr, $item->month);
                                array_push($CompletedTaskArr, $item->total_completed_task);
                                array_push($PendingTaskArr, $item->total_pending_task);
                                array_push($CreatedTaskArr, $item->total_created_task);
                            });
                            $reportData = $report->values()->all();
                            $result = [
                                'MonthArr' => $MonthArr,
                                'CompletedTaskArr' => $CompletedTaskArr,
                                'PendingTaskArr' => $PendingTaskArr,
                                'CreatedTaskArr' => $CreatedTaskArr,
                            ];

                        } else { // no  depart user role



                            //for all tasks
                            $tasks = Task::with('project', 'stage')->select([
                                'tasks.*',
                                'stages.name as status',
                                'stages.complete',
                            ])->join("stages", "stages.id", "=", "tasks.status")
                            ->whereHas('project', function ($query) use ($workspace_id) {
                                    $query->whereIn('workspace', $workspace_id);
                            })->orderBy('tasks.id', 'desc')->get();
                            //for all tasks


                            //for complete task
                            $completeTask = Task::whereHas('project', function ($query) use (&$workspace_id) {
                                $query->whereIn('workspace', $workspace_id);
                            })
                            ->whereHas('stage', function ($query) use ($doneStage) {
                                $query->whereIn('status', $doneStage);
                            })->count();
                            //for complete task


                            //for total task
                            $totalTask = Task::whereHas('project', function ($query) use (&$workspace_id) {
                                $query->whereIn('workspace', $workspace_id);
                            })->count();
                            //for total task

                            //for over due task
                            $overDueTasks = Task::whereHas('project', function ($query) use (&$workspace_id) {
                                $query->whereIn('workspace', $workspace_id);
                            })->whereHas('stage', function ($query) use ($doneStage) {
                                $query->whereNotIn('status' , $doneStage);
                            })->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                             //for over due task


                            //for monthly task report


                            $report = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                            ->join('stages', 'tasks.status', '=', 'stages.id')
                            ->whereIn('projects.workspace', $workspace_id)
                            ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                            ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                            ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                            ->selectRaw('COUNT(*) as total_created_task')
                            ->groupBy('projects.workspace', 'month')
                            ->orderBy('projects.workspace')
                            ->orderBy('month')
                            ->get();


                            $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                                array_push($MonthArr, $item->month);
                                array_push($CompletedTaskArr, $item->total_completed_task);
                                array_push($PendingTaskArr, $item->total_pending_task);
                                array_push($CreatedTaskArr, $item->total_created_task);
                            });
                            $reportData = $report->values()->all();
                            $result = [
                                'MonthArr' => $MonthArr,
                                'CompletedTaskArr' => $CompletedTaskArr,
                                'PendingTaskArr' => $PendingTaskArr,
                                'CreatedTaskArr' => $CreatedTaskArr,
                            ];


                             //for monthly task report
                        }

                    }

                    //for task statictis chart
                    $taskStatistics = $tasks->groupBy('status')->map->count()->values();
                    $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();
                    $taskStatisticsColors = ['Todo' => '#008FFB', 'In Progress' => '#00E396', 'Review' => '#FEB019', 'Done' => '#FF4560'];
                    $taskChartColor = array_intersect_key($taskStatisticsColors, array_flip($taskStatisticsKeys));
                    $taskCounts = $tasks->groupBy('status')->map->count();
                    $totalCount = $taskCounts->sum();
                    $taskPercentages = $taskCounts->map(function ($count) use ($totalCount) {
                        return ($count / $totalCount) * 100;
                    });
                    //for task statictis chart

                    $chartData = [];
                    $blade_type = 'HOD';
                    $taskStatus = ['All', 'Todo', 'In Progress', 'Review', 'Done'];

                    return view(
                        'home',
                        compact(
                            'taskChartColor',
                            'currentStatus',
                            'taskStatus',
                            'blade_type',
                            'taskStatisticsKeys',
                            'currentWorkspace',
                            // 'totalProject',
                            'totalTask',
                            // 'totalMembers',
                            // 'arrProcessLabel',
                            // 'arrProcessPer',
                            // 'arrProcessClass',
                            'completeTask',
                            'tasks',
                            'chartData',
                            // 'inProgressProjects',
                            // 'dueDateProjects',
                            // 'inProgressTask',
                            'overDueTasks',
                            'workspace_type',
                            // 'projects',
                            'taskStatistics',
                            'result',
                            'taskPercentages',
                            'hod_workspaces',
                            'check_home',
                            // 'departmentList'
                        )
                    );

                } else {
                    return redirect()->back()->with('error', __("No Workspace Found Under This HOD "));
                }




                // //for workspaces query
                // $department_id = json_decode($model_has_role->department_id);
                // $depart_user_role_id = json_decode($model_has_role->depart_user_role_id);



                // dd($department_id);

                // if (is_array($workspace_id) && is_array($department_id)) {

                //     // if (is_null($depart_user_role_id)) {

                //     //     $hod_workspaces = Workspace::select('workspaces.*', DB::raw('COUNT(tasks.id) as tasks_count'))
                //     //         ->whereIn('workspaces.id', $workspace_id)
                //     //         ->leftJoin('projects', 'workspaces.id', '=', 'projects.workspace')
                //     //         ->leftJoin('tasks', 'projects.id', '=', 'tasks.project_id')
                //     //         ->leftJoin('user_projects', 'projects.id', '=', 'user_projects.project_id')
                //     //         ->leftJoin('users', 'user_projects.user_id', '=', 'users.id')
                //     //         ->groupBy('workspaces.id')
                //     //         ->get();


                //     //     $departmentList = Department::select(
                //     //         'departments.*',
                //     //         DB::raw('COUNT(tasks.id) as tasks_count')
                //     //     )
                //     //         ->whereIn('departments.id', $department_id)
                //     //         ->leftJoin('projects', 'departments.id', '=', 'projects.department_id')
                //     //         ->leftJoin('tasks', 'projects.id', '=', 'tasks.project_id')
                //     //         ->groupBy('departments.id')
                //     //         ->get();

                //     //     $workspace = Workspace::whereIn('id', $workspace_id)->first();
                //     //     $departments = Department::whereIn('id', $department_id)->get();
                //     //     $totalTask = 0;
                //     //     $completeTask = 0;
                //     //     $overDueTasks = 0;
                //     //     foreach ($departments as $department) {
                //     //         //completed tasks
                //     //         $doneStage = Stage::where('workspace_id', '=', $workspace->id)->where('complete', '=', '1')->first();
                //     //         //   dd($doneStage);
                //     //         $completeTask += $department->projects->flatMap(function ($project) use ($doneStage, $department_id) {
                //     //             return $project->task->where('status', $doneStage->id)->whereIn('department_id', $department_id);
                //     //         })->count();

                //     //         // dd($completeTask);


                //     //         //
                //     //         $totalTask += $department->projects->flatMap(function ($project) {
                //     //             return $project->task;
                //     //         })->count();
                //     //         $overDueTasks += $department->projects->flatMap(function ($project) use ($doneStage) {
                //     //             return $project->task->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->where('status', '!=', $doneStage->id);
                //     //         })->count();
                //     //     }

                //     //     //
                //     //     if ($currentStatus == 'All') {
                //     //         $tasks = Task::with('project', 'stage')->select([
                //     //             'tasks.*',
                //     //             'stages.name as status',
                //     //             'stages.complete',
                //     //         ])->join("stages", "stages.id", "=", "tasks.status")
                //     //             ->whereHas('project', function ($query) use ($workspace_id, $department_id) {
                //     //                 $query->whereIn('department_id', $department_id);
                //     //                 // $query->whereIn('workspace', $workspace_id);
                //     //             })->orderBy('tasks.id', 'desc')->get();
                //     //     } else {
                //     //         $tasks = Task::with('project', 'stage')->select([
                //     //             'tasks.*',
                //     //             'stages.name as status',
                //     //             'stages.complete',
                //     //         ])->join("stages", "stages.id", "=", "tasks.status")
                //     //             ->whereHas('project', function ($query) use ($workspace_id, $department_id) {
                //     //                 $query->whereIn('department_id', $department_id);
                //     //                 // $query->whereIn('workspace', $workspace_id);
                //     //             })->orderBy('tasks.id', 'desc')
                //     //             ->whereHas('stage', function ($query) use ($currentStatus) {
                //     //                 $query->where('name', $currentStatus);
                //     //             })
                //     //             ->get();
                //     //     }


                //     //     $taskStatistics = $tasks->groupBy('status')->map->count()->values();
                //     //     $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();
                //     //     $taskStatisticsColors = ['Todo' => '#008FFB', 'In Progress' => '#00E396', 'Review' => '#FEB019', 'Done' => '#FF4560'];
                //     //     $taskChartColor = array_intersect_key($taskStatisticsColors, array_flip($taskStatisticsKeys));
                //     //     $taskCounts = $tasks->groupBy('status')->map->count();
                //     //     $totalCount = $taskCounts->sum();
                //     //     $taskPercentages = $taskCounts->map(function ($count) use ($totalCount) {
                //     //         return ($count / $totalCount) * 100;
                //     //     });
                //     //     //
                //     //     $MonthArr = [];
                //     //     $CompletedTaskArr = [];
                //     //     $PendingTaskArr = [];
                //     //     $CreatedTaskArr = [];



                //     //     if (is_null($depart_user_role_id)) {
                //     //         $report = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                //     //             ->join('stages', 'tasks.status', '=', 'stages.id')
                //     //             ->whereIn('projects.department_id', $department_id)
                //     //             ->whereIn('projects.workspace', $workspace_id)
                //     //             ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                //     //             ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                //     //             ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                //     //             ->selectRaw('COUNT(*) as total_created_task')
                //     //             ->groupBy('projects.department_id', 'month')
                //     //             ->orderBy('projects.department_id')
                //     //             ->orderBy('month')
                //     //             ->get();

                //     //     } else {
                //     //         $report = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                //     //             ->join('stages', 'tasks.status', '=', 'stages.id')
                //     //             ->join('department_users', 'projects.department_id', '=', 'department_users.department_id')
                //     //             ->whereIn('projects.department_id', $department_id)
                //     //             ->whereIn('department_users.role_id', $depart_user_role_id)
                //     //             ->whereIn('projects.workspace', $workspace_id)
                //     //             ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                //     //             ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                //     //             ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                //     //             ->selectRaw('COUNT(*) as total_created_task')
                //     //             ->groupBy('projects.department_id', 'month')
                //     //             ->orderBy('projects.department_id')
                //     //             ->orderBy('month')
                //     //             ->get();
                //     //     }


                //     //     $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                //     //         array_push($MonthArr, $item->month);
                //     //         array_push($CompletedTaskArr, $item->total_completed_task);
                //     //         array_push($PendingTaskArr, $item->total_pending_task);
                //     //         array_push($CreatedTaskArr, $item->total_created_task);
                //     //     });
                //     //     $reportData = $report->values()->all();
                //     //     $result = [
                //     //         'MonthArr' => $MonthArr,
                //     //         'CompletedTaskArr' => $CompletedTaskArr,
                //     //         'PendingTaskArr' => $PendingTaskArr,
                //     //         'CreatedTaskArr' => $CreatedTaskArr,
                //     //     ];
                //     //     $inProgressTask = Task::whereIn('projects.workspace', $workspace_id)
                //     //         ->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
                //     //         ->join("projects", "projects.id", "=", "user_projects.project_id")
                //     //         ->where('tasks.status', '=', '82')->count();
                //     // } else {
                //     //     $hod_workspaces = Workspace::select('workspaces.*', DB::raw('COUNT(tasks.id) as tasks_count'))
                //     //         ->whereIn('workspaces.id', $workspace_id)
                //     //         ->leftJoin('projects', 'workspaces.id', '=', 'projects.workspace')
                //     //         ->leftJoin('tasks', 'projects.id', '=', 'tasks.project_id')
                //     //         ->leftJoin('user_projects', 'projects.id', '=', 'user_projects.project_id')
                //     //         ->leftJoin('users', 'user_projects.user_id', '=', 'users.id')
                //     //         ->groupBy('workspaces.id')
                //     //         ->get();


                //     //     // dd($hod_workspaces);




                //     //     $departmentList = Department::select(
                //     //         'departments.*',
                //     //         DB::raw('COUNT(tasks.id) as tasks_count')
                //     //     )
                //     //         ->whereIn('departments.id', $department_id)
                //     //         ->leftJoin('projects', 'departments.id', '=', 'projects.department_id')
                //     //         ->leftJoin('tasks', 'projects.id', '=', 'tasks.project_id')
                //     //         ->groupBy('departments.id')
                //     //         ->get();

                //     //     $workspace = Workspace::whereIn('id', $workspace_id)->first();

                //     //     $departments = Department::whereIn('id', $department_id)->get();

                //     //     // dd($workspace_id);


                //     //     // dd($hod_workspaces);
                //     //     $totalTask = 0;
                //     //     $completeTask = 0;
                //     //     $overDueTasks = 0;
                //     //     foreach ($departments as $department) {
                //     //         // completed tasks
                //     //         $doneStage = Stage::whereIn('workspace_id', $workspace_id)->where('complete', '=', '1')->pluck('id');
                //     //         //    dd($doneStage);

                //     //         // dd($depart_user_role_id);

                //     //         $completeTask = Task::whereHas('project', function ($query) use (&$workspace_id, &$department_id) {
                //     //             $query->whereIn('workspace', $workspace_id)->whereIn('department_id', $department_id);
                //     //         })
                //     //             ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                //     //                 $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                //     //                     $query->whereIn('role_id', $depart_user_role_id);
                //     //                 });
                //     //             })
                //     //             ->whereHas('stage', function ($query) use ($doneStage) {
                //     //                 $query->whereIn('status', $doneStage);
                //     //             })->count();



                //     //         $totalTask = Task::whereHas('project', function ($query) use (&$workspace_id, &$department_id) {
                //     //             $query->whereIn('workspace', $workspace_id)->whereIn('department_id', $department_id);
                //     //         })
                //     //             ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                //     //                 $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                //     //                     $query->whereIn('role_id', $depart_user_role_id);
                //     //                 });
                //     //             })
                //     //             ->count();


                //     //         $overDueTasks = Task::whereHas('project', function ($query) use (&$workspace_id, &$department_id) {
                //     //             $query->whereIn('workspace', $workspace_id)->whereIn('department_id', $department_id);
                //     //         })
                //     //             ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                //     //                 $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                //     //                     $query->whereIn('role_id', $depart_user_role_id);
                //     //                 });
                //     //             })
                //     //             ->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                //     //     }

                //     //     // dd($totalTask);


                //     //     //
                //     //     if ($currentStatus == 'All') {
                //     //         $tasks = Task::with('project', 'stage')->select([
                //     //             'tasks.*',
                //     //             'stages.name as status',
                //     //             'stages.complete',
                //     //         ])->join("stages", "stages.id", "=", "tasks.status")
                //     //             ->whereHas('project', function ($query) use ($workspace_id, &$department_id) {
                //     //                 $query->whereIn('department_id', $department_id);
                //     //                 $query->whereIn('workspace', $workspace_id);
                //     //             })
                //     //             ->whereHas('assignees', function ($query) use ($department_id, &$depart_user_role_id) {
                //     //                 $query->whereHas('departments', function ($q) use (&$department_id, &$depart_user_role_id) {
                //     //                     // $q->where('department_id', $department_id)
                //     //                     $q->whereIn('role_id', $depart_user_role_id);
                //     //                 });
                //     //                 // $query->whereIn('department_id', $department_id)
                //     //                 //     ->whereIn('role_id', $depart_user_role_id);
                //     //             })
                //     //             ->orderBy('tasks.id', 'desc')->get();
                //     //         // dd($tasks);
                //     //     } else {
                //     //         $tasks = Task::with('project', 'stage')->select([
                //     //             'tasks.*',
                //     //             'stages.name as status',
                //     //             'stages.complete',
                //     //         ])->join("stages", "stages.id", "=", "tasks.status")
                //     //             ->whereHas('project', function ($query) use ($workspace_id, $department_id) {
                //     //                 $query->whereIn('department_id', $department_id);
                //     //                 $query->whereIn('workspace', $workspace_id);
                //     //             })->orderBy('tasks.id', 'desc')
                //     //             ->whereHas('stage', function ($query) use ($currentStatus) {
                //     //                 $query->where('name', $currentStatus);
                //     //             })
                //     //             ->whereHas('assignees', function ($query) use ($department_id, &$depart_user_role_id) {
                //     //                 $query->whereHas('departments', function ($q) use (&$department_id, &$depart_user_role_id) {
                //     //                     $q->where('department_id', $department_id)
                //     //                         ->whereIn('role_id', $depart_user_role_id);
                //     //                 });
                //     //                 // $query->whereIn('department_id', $department_id)
                //     //                 //     ->whereIn('role_id', $depart_user_role_id);
                //     //             })
                //     //             ->get();


                //     //     }



                //     //     $taskStatistics = $tasks->groupBy('status')->map->count()->values();
                //     //     $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();
                //     //     $taskStatisticsColors = ['Todo' => '#008FFB', 'In Progress' => '#00E396', 'Review' => '#FEB019', 'Done' => '#FF4560'];
                //     //     $taskChartColor = array_intersect_key($taskStatisticsColors, array_flip($taskStatisticsKeys));
                //     //     $taskCounts = $tasks->groupBy('status')->map->count();
                //     //     $totalCount = $taskCounts->sum();
                //     //     $taskPercentages = $taskCounts->map(function ($count) use ($totalCount) {
                //     //         return ($count / $totalCount) * 100;
                //     //     });

                //     //     // dd($taskPercentages);
                //     //     //
                //     //     $MonthArr = [];
                //     //     $CompletedTaskArr = [];
                //     //     $PendingTaskArr = [];
                //     //     $CreatedTaskArr = [];


                //     //     // $report = DB::table('tasks')
                //     //     //     ->join('projects', 'tasks.project_id', '=', 'projects.id')
                //     //     //     ->select(
                //     //     //         DB::raw('DATE_FORMAT(tasks.created_at, "%b") as month'),
                //     //     //         DB::raw('SUM(CASE WHEN tasks.status = "16" THEN 1 ELSE 0 END) as total_completed_task'),
                //     //     //         DB::raw('SUM(CASE WHEN tasks.status = "10" THEN 1 ELSE 0 END) as total_pending_task'),
                //     //     //         DB::raw('COUNT(*) as total_created_task')
                //     //     //     )
                //     //     //     ->whereIn('projects.department_id', $department_id)
                //     //     //     ->groupBy('projects.department_id', 'month')

                //     //     //     ->orderBy('projects.department_id')
                //     //     //     ->orderBy('month')
                //     //     //     ->get();


                //     //     if (is_null($depart_user_role_id)) {
                //     //         $report = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                //     //             ->join('stages', 'tasks.status', '=', 'stages.id')
                //     //             ->whereIn('projects.department_id', $department_id)
                //     //             ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                //     //             ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                //     //             ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                //     //             ->selectRaw('COUNT(*) as total_created_task')
                //     //             ->groupBy('projects.department_id', 'month')
                //     //             ->orderBy('projects.department_id')
                //     //             ->orderBy('month')
                //     //             ->get();

                //     //     } else {
                //     //         $report = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                //     //             ->join('stages', 'tasks.status', '=', 'stages.id')
                //     //             ->join('department_users', 'projects.department_id', '=', 'department_users.department_id')
                //     //             ->whereIn('projects.department_id', $department_id)
                //     //             ->whereIn('department_users.role_id', $depart_user_role_id)
                //     //             ->whereIn('projects.workspace', $workspace_id)
                //     //             ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                //     //             ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                //     //             ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                //     //             ->selectRaw('COUNT(*) as total_created_task')
                //     //             ->groupBy('projects.department_id', 'month')
                //     //             ->orderBy('projects.department_id')
                //     //             ->orderBy('month')
                //     //             ->get();

                //     //         // dd($report);
                //     //     }


                //     //     // dd($tasks);

                //     //     // dd($report);

                //     //     // dd($report);

                //     //     $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                //     //         array_push($MonthArr, $item->month);
                //     //         array_push($CompletedTaskArr, $item->total_completed_task);
                //     //         array_push($PendingTaskArr, $item->total_pending_task);
                //     //         array_push($CreatedTaskArr, $item->total_created_task);
                //     //     });
                //     //     $reportData = $report->values()->all();
                //     //     $result = [
                //     //         'MonthArr' => $MonthArr,
                //     //         'CompletedTaskArr' => $CompletedTaskArr,
                //     //         'PendingTaskArr' => $PendingTaskArr,
                //     //         'CreatedTaskArr' => $CreatedTaskArr,
                //     //     ];
                //     //     $inProgressTask = Task::whereIn('projects.workspace', $workspace_id)
                //     //         ->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
                //     //         ->join("projects", "projects.id", "=", "user_projects.project_id")
                //     //         ->where('tasks.status', '=', '82')->count();
                //     // }
                //     $chartData = [];
                //     $blade_type = 'HOD';
                //     $taskStatus = ['All', 'Todo', 'In Progress', 'Review', 'Done'];

                //     return view(
                //         'home',
                //         compact(
                //             'taskChartColor',
                //             'currentStatus',
                //             'taskStatus',
                //             'blade_type',
                //             'taskStatisticsKeys',
                //             'currentWorkspace',
                //             // 'totalProject',
                //             'totalTask',
                //             // 'totalMembers',
                //             // 'arrProcessLabel',
                //             // 'arrProcessPer',
                //             // 'arrProcessClass',
                //             'completeTask',
                //             'tasks',
                //             'chartData',
                //             // 'inProgressProjects',
                //             // 'dueDateProjects',
                //             'inProgressTask',
                //             'overDueTasks',
                //             'workspace_type',
                //             // 'projects',
                //             'taskStatistics',
                //             'result',
                //             'taskPercentages',
                //             'hod_workspaces',
                //             'check_home',
                //             'departmentList'
                //         )
                //     );
                // } else {
                //     return redirect()->back()->with('error', __("No Department Found Under This HOD "));
                // }
            }

            if (auth()->user()->hasRole('EXECUTIVE')) {
                // for executive
                $check_home = 1;

                $model_has_role_executive = ModelHasRole::where('model_id', auth()->id())->first();
                $HODs_id = json_decode($model_has_role_executive->hods);
                $HODs = User::whereIn('id', $HODs_id)->get();
                if (is_array($HODs_id)) {
                // global variables
                $totalTask = 0;
                $completeTask = 0;
                $overDueTasks = 0;
                $tasks = new Collection();
                // $report = new Collection();
                $report =  [];

                $MonthArr = [];
                $CompletedTaskArr = [];
                $PendingTaskArr = [];
                $CreatedTaskArr = [];


                foreach ($HODs_id as $key => $singleHodId) {
                    $model_has_role = ModelHasRole::where('model_id', $singleHodId)->first();
                    $workspaces = $model_has_role->workspace_id;
                    $workspace_id = json_decode($workspaces);

                    //for workspaces query

                    if (is_array($workspace_id)) {

                        $hod_workspaces = Workspace::
                            whereIn('workspaces.id', $workspace_id)
                            ->get();

                        $department_id = json_decode($model_has_role->department_id);
                        $depart_user_role_id = json_decode($model_has_role->depart_user_role_id);
                        $doneStage = Stage::whereIn('workspace_id', $workspace_id)->where('complete', '=', '1')->pluck('id');



                        if (is_array($department_id)) {

                            if (is_array($depart_user_role_id)) {

                                //for all tasks
                                $nestedtasks = Task::with('project', 'stage')->select([
                                    'tasks.*',
                                    'stages.name as status',
                                    'stages.complete',
                                ])->join("stages", "stages.id", "=", "tasks.status")
                                ->whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                        $query->whereIn('workspace', $workspace_id);
                                        $query->whereIn('department_id', $department_id);
                                })
                                ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                    $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                        $query->whereIn('role_id', $depart_user_role_id);
                                    });
                                })
                                ->orderBy('tasks.id', 'desc')->get();
                                //for all tasks


                                //for complete task
                                $completeTask += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                    $query->whereIn('workspace', $workspace_id);
                                    $query->whereIn('department_id', $department_id);
                                })
                                ->whereHas('stage', function ($query) use ($doneStage) {
                                    $query->whereIn('status', $doneStage);
                                })
                                ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                    $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                        $query->whereIn('role_id', $depart_user_role_id);
                                    });
                                })
                                ->count();
                                //for complete task


                                //for total task
                                $totalTask += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                    $query->whereIn('workspace', $workspace_id);
                                    $query->whereIn('department_id', $department_id);
                                 })
                                 ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                    $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                        $query->whereIn('role_id', $depart_user_role_id);
                                    });
                                })
                                ->count();
                                //for total task

                                //for over due task
                                $overDueTasks += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                    $query->whereIn('workspace', $workspace_id);
                                    $query->whereIn('department_id', $department_id);
                                })
                                ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                    $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                        $query->whereIn('role_id', $depart_user_role_id);
                                    });
                                })
                                ->whereHas('stage', function ($query) use ($doneStage) {
                                    $query->whereNotIn('status' , $doneStage);
                                })
                                ->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                                 //for over due task


                                //for monthly task report


                                $nestedReport = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                                ->join('stages', 'tasks.status', '=', 'stages.id')
                                ->whereIn('projects.workspace', $workspace_id)
                                ->whereIn('projects.department_id', $department_id)
                                ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                    $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                        $query->whereIn('role_id', $depart_user_role_id);
                                    });
                                })
                                ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                                ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                                ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                                ->selectRaw('COUNT(*) as total_created_task')
                                ->groupBy('projects.workspace', 'month')
                                ->orderBy('projects.workspace')
                                ->orderBy('month')
                                ->get();

                            } else { //no  depart user role


                                //for all tasks
                                $nestedtasks = Task::with('project', 'stage')->select([
                                    'tasks.*',
                                    'stages.name as status',
                                    'stages.complete',
                                ])->join("stages", "stages.id", "=", "tasks.status")
                                ->whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                        $query->whereIn('workspace', $workspace_id);
                                        $query->whereIn('department_id', $department_id);
                                })->orderBy('tasks.id', 'desc')->get();
                                //for all tasks


                                //for complete task
                                $completeTask += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                    $query->whereIn('workspace', $workspace_id);
                                    $query->whereIn('department_id', $department_id);
                                })

                                ->whereHas('stage', function ($query) use ($doneStage) {
                                    $query->whereIn('status', $doneStage);
                                })->count();
                                //for complete task


                                //for total task
                                $totalTask += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                    $query->whereIn('workspace', $workspace_id);
                                    $query->whereIn('department_id', $department_id);
                                 })->count();
                                //for total task

                                //for over due task
                                $overDueTasks += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                    $query->whereIn('workspace', $workspace_id);
                                    $query->whereIn('department_id', $department_id);
                                })
                                ->whereHas('stage', function ($query) use ($doneStage) {
                                    $query->whereNotIn('status' , $doneStage);
                                })
                                ->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                                 //for over due task



                                //for monthly task report


                                $nestedReport = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                                ->join('stages', 'tasks.status', '=', 'stages.id')
                                ->whereIn('projects.workspace', $workspace_id)
                                ->whereIn('projects.department_id', $department_id)
                                ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                                ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                                ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                                ->selectRaw('COUNT(*) as total_created_task')
                                ->groupBy('projects.workspace', 'month')
                                ->orderBy('projects.workspace')
                                ->orderBy('month')
                                ->get();
                            }



                        } else {  // no depart

                            if (is_array($depart_user_role_id)) {


                                //for all tasks
                                $nestedtasks = Task::with('project', 'stage')->select([
                                    'tasks.*',
                                    'stages.name as status',
                                    'stages.complete',
                                ])->join("stages", "stages.id", "=", "tasks.status")
                                ->whereHas('project', function ($query) use ($workspace_id) {
                                        $query->whereIn('workspace', $workspace_id);
                                })
                                ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                    $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                        $query->whereIn('role_id', $depart_user_role_id);
                                    });
                                })->orderBy('tasks.id', 'desc')->get();
                                //for all tasks

                                //for complete task
                                $completeTask += Task::whereHas('project', function ($query) use (&$workspace_id) {
                                    $query->whereIn('workspace', $workspace_id);
                                })
                                ->whereHas('stage', function ($query) use ($doneStage) {
                                    $query->whereIn('status', $doneStage);
                                })
                                ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                    $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                        $query->whereIn('role_id', $depart_user_role_id);
                                    });
                                })->count();
                                //for complete task



                               //for total task
                                $totalTask += Task::whereHas('project', function ($query) use (&$workspace_id) {
                                    $query->whereIn('workspace', $workspace_id);
                                })
                                ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                    $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                        $query->whereIn('role_id', $depart_user_role_id);
                                    });
                                })->count();
                                //for total task

                                //for over due task
                                $overDueTasks += Task::whereHas('project', function ($query) use (&$workspace_id) {
                                    $query->whereIn('workspace', $workspace_id);
                                })
                                ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                    $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                        $query->whereIn('role_id', $depart_user_role_id);
                                    });
                                })->whereHas('stage', function ($query) use ($doneStage) {
                                    $query->whereNotIn('status' , $doneStage);
                                })->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                                 //for over due task


                                //for monthly task report
                                $nestedReport = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                                ->join('stages', 'tasks.status', '=', 'stages.id')
                                ->whereIn('projects.workspace', $workspace_id)
                                ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                    $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                        $query->whereIn('role_id', $depart_user_role_id);
                                    });
                                })
                                ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                                ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                                ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                                ->selectRaw('COUNT(*) as total_created_task')
                                ->groupBy('projects.workspace', 'month')
                                ->orderBy('projects.workspace')
                                ->orderBy('month')
                                ->get();

                            } else { // no  depart user role



                                //for all tasks
                                $nestedtasks = Task::with('project', 'stage')->select([
                                    'tasks.*',
                                    'stages.name as status',
                                    'stages.complete',
                                ])->join("stages", "stages.id", "=", "tasks.status")
                                ->whereHas('project', function ($query) use ($workspace_id) {
                                        $query->whereIn('workspace', $workspace_id);
                                })->orderBy('tasks.id', 'desc')->get();
                                //for all tasks


                                //for complete task
                                $completeTask += Task::whereHas('project', function ($query) use (&$workspace_id) {
                                    $query->whereIn('workspace', $workspace_id);
                                })
                                ->whereHas('stage', function ($query) use ($doneStage) {
                                    $query->whereIn('status', $doneStage);
                                })->count();
                                //for complete task


                                //for total task
                                $totalTask += Task::whereHas('project', function ($query) use (&$workspace_id) {
                                    $query->whereIn('workspace', $workspace_id);
                                })->count();
                                //for total task

                                //for over due task
                                $overDueTasks += Task::whereHas('project', function ($query) use (&$workspace_id) {
                                    $query->whereIn('workspace', $workspace_id);
                                })->whereHas('stage', function ($query) use ($doneStage) {
                                    $query->whereNotIn('status' , $doneStage);
                                })->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                                //for over due task


                                //for monthly task report


                                $nestedReport = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                                ->join('stages', 'tasks.status', '=', 'stages.id')
                                ->whereIn('projects.workspace', $workspace_id)
                                ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                                ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                                ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                                ->selectRaw('COUNT(*) as total_created_task')
                                ->groupBy('projects.workspace', 'month')
                                ->orderBy('projects.workspace')
                                ->orderBy('month')
                                ->get();





                                 //for monthly task report
                            }

                        }

                        // merge nested tasks to tasks
                        $tasks = $tasks->merge($nestedtasks);
                        // $report = $report->merge($nestedReport);
                        $report[] = $nestedReport;
                        // dd($nestedReport);


                    }
                }
                // dd($report);
                $report = collect($report)->collapse()->groupBy('month');
                //    dd($report);
                // for monthly progress reports
                // $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                //     array_push($MonthArr, $item->month);
                //     array_push($CompletedTaskArr, $item->total_completed_task);
                //     array_push($PendingTaskArr, $item->total_pending_task);
                //     array_push($CreatedTaskArr, $item->total_created_task);
                // });

                $report->each(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                    $MonthArr[] = $item->first()->month; // Assuming all instances in the group have the same month
                    $CompletedTaskArr[] = $item->sum('total_completed_task');
                    $PendingTaskArr[] = $item->sum('total_pending_task');
                    $CreatedTaskArr[] = $item->sum('total_created_task');
                });
                $reportData = $report->values()->all();
                $result = [
                    'MonthArr' => $MonthArr,
                    'CompletedTaskArr' => $CompletedTaskArr,
                    'PendingTaskArr' => $PendingTaskArr,
                    'CreatedTaskArr' => $CreatedTaskArr,
                ];
                // for monthly progress reports


                //for task statictis chart
                $taskStatistics = $tasks->groupBy('status')->map->count()->values();
                $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();
                $taskStatisticsColors = ['Todo' => '#008FFB', 'In Progress' => '#00E396', 'Review' => '#FEB019', 'Done' => '#FF4560'];
                $taskChartColor = array_intersect_key($taskStatisticsColors, array_flip($taskStatisticsKeys));
                $taskCounts = $tasks->groupBy('status')->map->count();
                $totalCount = $taskCounts->sum();
                $taskPercentages = $taskCounts->map(function ($count) use ($totalCount) {
                    return ($count / $totalCount) * 100;
                });
                //for task statictis chart

                $chartData = [];
                $blade_type = 'Executive';
                $taskStatus = ['All', 'Todo', 'In Progress', 'Review', 'Done'];
                return view(
                    'home',
                    compact(
                        'taskChartColor',
                        'currentStatus',
                        'taskStatus',
                        'blade_type',
                        'taskStatisticsKeys',
                        'currentWorkspace',
                        // 'totalProject',
                        'totalTask',
                        // 'totalMembers',
                        // 'arrProcessLabel',
                        // 'arrProcessPer',
                        // 'arrProcessClass',
                        'completeTask',
                        'tasks',
                        'chartData',
                        // 'inProgressProjects',
                        // 'dueDateProjects',
                        // 'inProgressTask',
                        'overDueTasks',
                        'workspace_type',
                        // 'projects',
                        'taskStatistics',
                        'result',
                        'taskPercentages',
                        // 'hod_workspaces',
                        'check_home',
                        // 'departmentList',
                        'HODs'
                    )
                );
            }else{
                return redirect()->back()->with('error', __("No HOD Found Under This Executive "));
            }
                // for executive

                $check_home = 1;
                $model_has_role_executive = ModelHasRole::where('model_id', auth()->id())->first();
                $HODs_id = json_decode($model_has_role_executive->hods);
                // dd(auth()->id());
                if (is_null($HODs_id)) {
                    $HODs_id = [];

                }




                $HODs = User::whereIn('id', $HODs_id)->get();
                // dd($HODs);
                if (is_array($HODs_id)) {
                    # code...

                    $totalTask = 0;
                    $completeTask = 0;
                    $overDueTasks = 0;

                    $tasks = new Collection();

                    if (!is_null($HODs_id)) {
                        foreach ($HODs_id as $key => $hod_id) {
                            $model_has_role = ModelHasRole::where('model_id', $hod_id)->first();
                            $workspaces = $model_has_role->workspace_id;
                            $workspace_id = json_decode($workspaces);


                            $department_id = json_decode($model_has_role->department_id);
                            $depart_user_role_id = json_decode($model_has_role->depart_user_role_id);

                            if (is_array($department_id)) {

                                if (is_null($depart_user_role_id)) {

                                    $workspace = Workspace::whereIn('id', $workspace_id)->first();
                                    $departments = Department::whereIn('id', $department_id)->get();

                                    foreach ($departments as $department) {
                                        //completed tasks
                                        $doneStage = Stage::where('workspace_id', '=', $workspace->id)->where('complete', '=', '1')->first();
                                        //   dd($doneStage);
                                        $completeTask += $department->projects->flatMap(function ($project) use ($doneStage) {
                                            return $project->task->where('status', $doneStage->id);
                                        })->count();
                                        //

                                        $totalTask += $department->projects->flatMap(function ($project) {
                                            return $project->task;
                                        })->count();
                                        $overDueTasks += $department->projects->flatMap(function ($project) use ($doneStage) {
                                            return $project->task->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->where('status', '!=', $doneStage->id);
                                        })->count();
                                    }


                                    if ($currentStatus == 'All') {
                                        $nestedtasks = Task::with('project', 'stage')->select([
                                            'tasks.*',
                                            'stages.name as status',
                                            'stages.complete',
                                        ])->join("stages", "stages.id", "=", "tasks.status")
                                            ->whereHas('project', function ($query) use ($workspace_id, $department_id) {
                                                $query->whereIn('department_id', $department_id);
                                                // $query->whereIn('workspace', $workspace_id);
                                            })->orderBy('tasks.id', 'desc')->get();

                                    } else {
                                        $nestedtasks = Task::with('project', 'stage')->select([
                                            'tasks.*',
                                            'stages.name as status',
                                            'stages.complete',
                                        ])->join("stages", "stages.id", "=", "tasks.status")
                                            ->whereHas('project', function ($query) use ($workspace_id, $department_id) {
                                                $query->whereIn('department_id', $department_id);
                                                // $query->whereIn('workspace', $workspace_id);
                                            })->orderBy('tasks.id', 'desc')
                                            ->whereHas('stage', function ($query) use ($currentStatus) {
                                                $query->where('name', $currentStatus);
                                            })
                                            ->get();
                                    }

                                    $tasks = $tasks->merge($nestedtasks);
                                    // dd($tasks->merge($nestedtasks));
                                } else {

                                    $workspace = Workspace::whereIn('id', $workspace_id)->first();
                                    $departments = Department::whereIn('id', $department_id)->get();

                                    foreach ($departments as $department) {
                                        //completed tasks
                                        $doneStage = Stage::where('workspace_id', '=', $workspace->id)->where('complete', '=', '1')->first();
                                        //   dd($doneStage);


                                        $completeTask = Task::whereHas('project', function ($query) use (&$workspace_id, &$department_id) {
                                            $query->whereIn('workspace', $workspace_id)->whereIn('department_id', $department_id);
                                        })
                                            ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                                $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                                    $query->whereIn('role_id', $depart_user_role_id);
                                                });
                                            })
                                            ->whereHas('stage', function ($query) use ($doneStage) {
                                                $query->whereIn('status', $doneStage);
                                            })->count();



                                        $totalTask = Task::whereHas('project', function ($query) use (&$workspace_id, &$department_id) {
                                            $query->whereIn('workspace', $workspace_id)->whereIn('department_id', $department_id);
                                        })
                                            ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                                $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                                    $query->whereIn('role_id', $depart_user_role_id);
                                                });
                                            })
                                            ->count();


                                        $overDueTasks = Task::whereHas('project', function ($query) use (&$workspace_id, &$department_id) {
                                            $query->whereIn('workspace', $workspace_id)->whereIn('department_id', $department_id);
                                        })
                                            ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                                                $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                                    $query->whereIn('role_id', $depart_user_role_id);
                                                });
                                            })
                                            ->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                                    }

                                    if ($currentStatus == 'All') {
                                        $nestedtasks = Task::with('project', 'stage')->select([
                                            'tasks.*',
                                            'stages.name as status',
                                            'stages.complete',
                                        ])->join("stages", "stages.id", "=", "tasks.status")
                                            ->whereHas('project', function ($query) use ($workspace_id, &$department_id) {
                                                $query->whereIn('department_id', $department_id);
                                                $query->whereIn('workspace', $workspace_id);
                                            })
                                            ->whereHas('assignees', function ($query) use ($department_id, &$depart_user_role_id) {
                                                $query->whereHas('departments', function ($q) use (&$department_id, &$depart_user_role_id) {
                                                    $q->where('department_id', $department_id)
                                                        ->whereIn('role_id', $depart_user_role_id);
                                                });
                                                // $query->whereIn('department_id', $department_id)
                                                //     ->whereIn('role_id', $depart_user_role_id);
                                            })
                                            ->orderBy('tasks.id', 'desc')->get();

                                    } else {
                                        $nestedtasks = Task::with('project', 'stage')->select([
                                            'tasks.*',
                                            'stages.name as status',
                                            'stages.complete',
                                        ])->join("stages", "stages.id", "=", "tasks.status")
                                            ->whereHas('project', function ($query) use ($workspace_id, $department_id) {
                                                $query->whereIn('department_id', $department_id);
                                                // $query->whereIn('workspace', $workspace_id);
                                            })->orderBy('tasks.id', 'desc')
                                            ->whereHas('stage', function ($query) use ($currentStatus) {
                                                $query->where('name', $currentStatus);
                                            })
                                            ->whereHas('assignees', function ($query) use ($department_id, &$depart_user_role_id) {
                                                $query->whereHas('departments', function ($q) use (&$department_id, &$depart_user_role_id) {
                                                    $q->where('department_id', $department_id)
                                                        ->whereIn('role_id', $depart_user_role_id);
                                                });
                                                // $query->whereIn('department_id', $department_id)
                                                //     ->whereIn('role_id', $depart_user_role_id);
                                            })
                                            ->get();
                                    }

                                    $tasks = $tasks->merge($nestedtasks);
                                }
                            }
                        }

                    }

                    $taskStatistics = $tasks->groupBy('status')->map->count()->values();
                    $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();
                    $taskStatisticsColors = ['Todo' => '#008FFB', 'In Progress' => '#00E396', 'Review' => '#FEB019', 'Done' => '#FF4560'];
                    $taskChartColor = array_intersect_key($taskStatisticsColors, array_flip($taskStatisticsKeys));
                    $taskCounts = $tasks->groupBy('status')->map->count();
                    $totalCount = $taskCounts->sum();
                    $taskPercentages = $taskCounts->map(function ($count) use ($totalCount) {
                        return ($count / $totalCount) * 100;
                    });

                    // dd($workspace_id);


                    $MonthArr = [];
                    $CompletedTaskArr = [];
                    $PendingTaskArr = [];
                    $CreatedTaskArr = [];
                    // $report = DB::table('tasks')
                    //         ->join('projects', 'tasks.project_id', '=', 'projects.id')
                    //         ->join('stages', 'tasks.status', '=', 'stages.id')
                    //         ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                    //         ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                    //         ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                    //         ->selectRaw('COUNT(*) as total_created_task')
                    //         // ->where('projects.workspace', $id)
                    //         ->whereIn('projects.workspace', $workspace_id)
                    //         ->whereIn('projects.department_id', $department_id)
                    //         // ->groupBy('projects.workspace', 'month')
                    //         ->groupBy('projects.department_id', 'month')
                    //         // ->orderBy('projects.workspace')
                    //         ->orderBy('projects.department_id')
                    //         ->orderBy('month')
                    //         ->get();

                    if (isset($workspace_id)) {
                        // Your query here
                        $report = DB::table('tasks')
                            ->join('projects', 'tasks.project_id', '=', 'projects.id')
                            ->join('stages', 'tasks.status', '=', 'stages.id')
                            ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                            ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                            ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                            ->selectRaw('COUNT(*) as total_created_task')
                            ->whereIn('projects.workspace', $workspace_id)
                            ->whereIn('projects.department_id', $department_id)
                            ->groupBy('projects.department_id', 'month')
                            ->orderBy('projects.department_id')
                            ->orderBy('month')
                            ->get();

                        $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                            array_push($MonthArr, $item->month);
                            array_push($CompletedTaskArr, $item->total_completed_task);
                            array_push($PendingTaskArr, $item->total_pending_task);
                            array_push($CreatedTaskArr, $item->total_created_task);
                        });
                        $reportData = $report->values()->all();
                        $result = [
                            'MonthArr' => $MonthArr,
                            'CompletedTaskArr' => $CompletedTaskArr,
                            'PendingTaskArr' => $PendingTaskArr,
                            'CreatedTaskArr' => $CreatedTaskArr,
                        ];

                        $inProgressTask = Task::whereIn('projects.workspace', $workspace_id)
                            ->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
                            ->join("projects", "projects.id", "=", "user_projects.project_id")
                            ->where('tasks.status', '=', '82')->count();
                    } else {
                        $report = DB::table('tasks')
                            ->join('projects', 'tasks.project_id', '=', 'projects.id')
                            ->join('stages', 'tasks.status', '=', 'stages.id')
                            ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                            ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                            ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                            ->selectRaw('COUNT(*) as total_created_task')
                            ->whereIn('projects.workspace', [])
                            ->whereIn('projects.department_id', [])
                            ->groupBy('projects.department_id', 'month')
                            ->orderBy('projects.department_id')
                            ->orderBy('month')
                            ->get();

                        $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                            array_push($MonthArr, $item->month);
                            array_push($CompletedTaskArr, $item->total_completed_task);
                            array_push($PendingTaskArr, $item->total_pending_task);
                            array_push($CreatedTaskArr, $item->total_created_task);
                        });
                        $reportData = $report->values()->all();
                        $result = [
                            'MonthArr' => $MonthArr,
                            'CompletedTaskArr' => $CompletedTaskArr,
                            'PendingTaskArr' => $PendingTaskArr,
                            'CreatedTaskArr' => $CreatedTaskArr,
                        ];

                        $inProgressTask = Task::whereIn('projects.workspace', [])
                            ->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
                            ->join("projects", "projects.id", "=", "user_projects.project_id")
                            ->where('tasks.status', '=', '82')->count();
                    }

                    // dd($report);



                    $chartData = [];
                    $blade_type = 'Executive';
                    $taskStatus = ['All', 'Todo', 'In Progress', 'Review', 'Done'];

                    return view(
                        'home',
                        compact(
                            'taskChartColor',
                            'currentStatus',
                            'taskStatus',
                            'blade_type',
                            'taskStatisticsKeys',
                            'currentWorkspace',
                            // 'totalProject',
                            'totalTask',
                            // 'totalMembers',
                            // 'arrProcessLabel',
                            // 'arrProcessPer',
                            // 'arrProcessClass',
                            'completeTask',
                            'tasks',
                            'chartData',
                            // 'inProgressProjects',
                            // 'dueDateProjects',
                            'inProgressTask',
                            'overDueTasks',
                            'workspace_type',
                            // 'projects',
                            'taskStatistics',
                            'result',
                            'taskPercentages',
                            // 'hod_workspaces',
                            'check_home',
                            // 'departmentList',
                            'HODs'
                        )
                    );
                } else {
                    return redirect()->back()->with('error', __("No HOD Found Under This Executive "));
                }
            }

            // else {
            // dd(date("Y-m-d"));
            $workspace_type = WorkspaceType::get();
            $totalProject = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->count();
            $dueDateProjects = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->whereDate('end_date', '=', date("Y-m-d"))->count();
            $inProgressProjects = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('status', '=', 'Ongoing')->count();
            $dueDateTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->whereDate('due_date', '=', date("Y-m-d"))->count();
            $inProgressTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('tasks.status', '=', '82')->count();
            if ($currentWorkspace->permission == 'Owner') {
                $totalBugs = UserProject::join("bug_reports", "bug_reports.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->count();
                $totalTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->count();
                $completeTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('tasks.status', '=', $doneStage->id)->count();
                $tasks = Task::select([
                    'tasks.*',
                    'stages.name as status',
                    'stages.complete',
                ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->orderBy('tasks.id', 'desc')->limit(5)->get();



                $projects = Project::join("user_projects", "projects.id", "=", "user_projects.project_id")
                    ->join("projects as p2", "p2.id", "=", "user_projects.project_id") // Change alias here
                    ->where("user_id", "=", $userObj->id)
                    ->where('p2.workspace', '=', $currentWorkspace->id) // Use the new alias here
                    ->orderBy('projects.id', 'desc')
                    ->limit(5)
                    ->get();

                // dd($projects);

            } else {
                $totalBugs = UserProject::join("bug_reports", "bug_reports.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('bug_reports.assign_to', '=', $userObj->id)->count();
                $totalTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")->count();
                $completeTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")->where('tasks.status', '=', $doneStage->id)->count();
                $tasks = Task::select([
                    'tasks.*',
                    'stages.name as status',
                    'stages.complete',
                ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")->orderBy('tasks.id', 'desc')->limit(5)->get();
            }



            $projects = Project::join("user_projects", "projects.id", "=", "user_projects.project_id")
                ->join("projects as p2", "p2.id", "=", "user_projects.project_id") // Change alias here
                ->where("user_id", "=", $userObj->id)
                ->where('p2.workspace', '=', $currentWorkspace->id) // Use the new alias here
                ->orderBy('projects.id', 'desc')
                ->limit(5)
                ->get();
            $totalMembers = UserWorkspace::where('workspace_id', '=', $currentWorkspace->id)->count();

            $projectProcess = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->groupBy('projects.status')->selectRaw('count(projects.id) as count, projects.status')->pluck('count', 'projects.status');
            $arrProcessPer = [];
            $arrProcessLabel = [];
            foreach ($projectProcess as $lable => $process) {
                $arrProcessLabel[] = $lable;
                if ($totalProject == 0) {
                    $arrProcessPer[] = 0.00;
                } else {
                    $arrProcessPer[] = round(($process * 100) / $totalProject, 2);
                }
            }
            // dd($arrProcessPer);
            $arrProcessClass = [
                'text-success',
                'text-primary',
                'text-danger',
            ];

            $projectController = new ProjectReportController();

            $chartData = $projectController->getProjectChart([
                'workspace_id' => $currentWorkspace->id,
                'duration' => 'week',
            ]);
            // dd($chartData);
            // dd( $chartData['stages']);

            // $chartData = app('App\Http\Controllers\ProjectController')->getProjectChart([
            //     'workspace_id' => $currentWorkspace->id,
            //     'duration' => 'week',
            // ]);



            return view(
                'home',
                compact(
                    'currentWorkspace',
                    'taskStatistics',
                    'totalProject',
                    'totalBugs',
                    'totalTask',
                    'totalMembers',
                    'arrProcessLabel',
                    'arrProcessPer',
                    'arrProcessClass',
                    'completeTask',
                    'tasks',
                    'chartData',
                    'inProgressProjects',
                    'dueDateProjects',
                    'inProgressTask',
                    'dueDateTask',
                    'workspace_type',
                    'projects'

                )
            );
            // }

    }

    public function single_workspace_report($workspace_id, $slug = '', $currentStatus = 'All')
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $check_home = 0;
        $model_has_role = ModelHasRole::where('model_id', Auth::id())->first();

        //for workspaces query

        if (isset($workspace_id)) {

            // $singleWorkspace = Workspace::find($workspace_id);

            $department_id = json_decode($model_has_role->department_id);
            $depart_user_role_id = json_decode($model_has_role->depart_user_role_id);
            $doneStage = Stage::where('workspace_id', $workspace_id)->where('complete', '=', '1')->pluck('id');

            $MonthArr = [];
            $CompletedTaskArr = [];
            $PendingTaskArr = [];
            $CreatedTaskArr = [];

            if (is_array($department_id)) {

                if (is_array($depart_user_role_id)) {

                    //for all tasks
                    $tasks = Task::with('project', 'stage')->select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("stages", "stages.id", "=", "tasks.status")
                    ->whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                            $query->where('workspace', $workspace_id);
                            $query->whereIn('department_id', $department_id);
                    })
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })
                    ->orderBy('tasks.id', 'desc')->get();
                    //for all tasks


                    //for complete task
                    $completeTask = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                        $query->where('workspace', $workspace_id);
                        $query->whereIn('department_id', $department_id);
                    })
                    ->whereHas('stage', function ($query) use ($doneStage) {
                        $query->whereIn('status', $doneStage);
                    })
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })
                    ->count();
                    //for complete task


                    //for total task
                    $totalTask = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                        $query->where('workspace', $workspace_id);
                        $query->whereIn('department_id', $department_id);
                     })
                     ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })
                    ->count();
                    //for total task

                    //for over due task
                    $overDueTasks = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                        $query->where('workspace', $workspace_id);
                        $query->whereIn('department_id', $department_id);
                    })
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })
                    ->whereHas('stage', function ($query) use ($doneStage) {
                        $query->whereNotIn('status' , $doneStage);
                    })
                    ->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                     //for over due task


                    //for monthly task report


                    $report = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                    ->join('stages', 'tasks.status', '=', 'stages.id')
                    ->where('projects.workspace', $workspace_id)
                    ->whereIn('projects.department_id', $department_id)
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })
                    ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                    ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                    ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                    ->selectRaw('COUNT(*) as total_created_task')
                    ->groupBy('projects.workspace', 'month')
                    ->orderBy('projects.workspace')
                    ->orderBy('month')
                    ->get();


                    $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                        array_push($MonthArr, $item->month);
                        array_push($CompletedTaskArr, $item->total_completed_task);
                        array_push($PendingTaskArr, $item->total_pending_task);
                        array_push($CreatedTaskArr, $item->total_created_task);
                    });
                    $reportData = $report->values()->all();
                    $result = [
                        'MonthArr' => $MonthArr,
                        'CompletedTaskArr' => $CompletedTaskArr,
                        'PendingTaskArr' => $PendingTaskArr,
                        'CreatedTaskArr' => $CreatedTaskArr,
                    ];


                } else { //no  depart user role


                    //for all tasks
                    $tasks = Task::with('project', 'stage')->select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("stages", "stages.id", "=", "tasks.status")
                    ->whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                            $query->where('workspace', $workspace_id);
                            $query->whereIn('department_id', $department_id);
                    })->orderBy('tasks.id', 'desc')->get();
                    //for all tasks


                    //for complete task
                    $completeTask = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                        $query->where('workspace', $workspace_id);
                        $query->whereIn('department_id', $department_id);
                    })

                    ->whereHas('stage', function ($query) use ($doneStage) {
                        $query->whereIn('status', $doneStage);
                    })->count();
                    //for complete task


                    //for total task
                    $totalTask = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                        $query->where('workspace', $workspace_id);
                        $query->whereIn('department_id', $department_id);
                     })->count();
                    //for total task

                    //for over due task
                    $overDueTasks = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                        $query->where('workspace', $workspace_id);
                        $query->whereIn('department_id', $department_id);
                    })
                    ->whereHas('stage', function ($query) use ($doneStage) {
                        $query->whereNotIn('status' , $doneStage);
                    })
                    ->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                     //for over due task

                    //  dd($overDueTasks);

                    //for monthly task report


                    $report = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                    ->join('stages', 'tasks.status', '=', 'stages.id')
                    ->where('projects.workspace', $workspace_id)
                    ->whereIn('projects.department_id', $department_id)
                    ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                    ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                    ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                    ->selectRaw('COUNT(*) as total_created_task')
                    ->groupBy('projects.workspace', 'month')
                    ->orderBy('projects.workspace')
                    ->orderBy('month')
                    ->get();


                    $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                        array_push($MonthArr, $item->month);
                        array_push($CompletedTaskArr, $item->total_completed_task);
                        array_push($PendingTaskArr, $item->total_pending_task);
                        array_push($CreatedTaskArr, $item->total_created_task);
                    });
                    $reportData = $report->values()->all();
                    $result = [
                        'MonthArr' => $MonthArr,
                        'CompletedTaskArr' => $CompletedTaskArr,
                        'PendingTaskArr' => $PendingTaskArr,
                        'CreatedTaskArr' => $CreatedTaskArr,
                    ];

                }



            } else {  // no depart

                if (is_array($depart_user_role_id)) {


                    //for all tasks
                    $tasks = Task::with('project', 'stage')->select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("stages", "stages.id", "=", "tasks.status")
                    ->whereHas('project', function ($query) use ($workspace_id) {
                            $query->where('workspace', $workspace_id);
                    })
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })->orderBy('tasks.id', 'desc')->get();
                    //for all tasks

                    //for complete task
                    $completeTask = Task::whereHas('project', function ($query) use (&$workspace_id) {
                        $query->where('workspace', $workspace_id);
                    })
                    ->whereHas('stage', function ($query) use ($doneStage) {
                        $query->whereIn('status', $doneStage);
                    })
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })->count();
                    //for complete task



                   //for total task
                    $totalTask = Task::whereHas('project', function ($query) use (&$workspace_id) {
                        $query->where('workspace', $workspace_id);
                    })
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })->count();
                    //for total task

                    //for over due task
                    $overDueTasks = Task::whereHas('project', function ($query) use (&$workspace_id) {
                        $query->where('workspace', $workspace_id);
                    })
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })->whereHas('stage', function ($query) use ($doneStage) {
                        $query->whereNotIn('status' , $doneStage);
                    })->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                     //for over due task


                    //for monthly task report
                    $report = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                    ->join('stages', 'tasks.status', '=', 'stages.id')
                    ->where('projects.workspace', $workspace_id)
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })
                    ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                    ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                    ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                    ->selectRaw('COUNT(*) as total_created_task')
                    ->groupBy('projects.workspace', 'month')
                    ->orderBy('projects.workspace')
                    ->orderBy('month')
                    ->get();


                    $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                        array_push($MonthArr, $item->month);
                        array_push($CompletedTaskArr, $item->total_completed_task);
                        array_push($PendingTaskArr, $item->total_pending_task);
                        array_push($CreatedTaskArr, $item->total_created_task);
                    });
                    $reportData = $report->values()->all();
                    $result = [
                        'MonthArr' => $MonthArr,
                        'CompletedTaskArr' => $CompletedTaskArr,
                        'PendingTaskArr' => $PendingTaskArr,
                        'CreatedTaskArr' => $CreatedTaskArr,
                    ];

                } else { // no  depart user role



                    //for all tasks
                    $tasks = Task::with('project', 'stage')->select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("stages", "stages.id", "=", "tasks.status")
                    ->whereHas('project', function ($query) use ($workspace_id) {
                            $query->where('workspace', $workspace_id);
                    })->orderBy('tasks.id', 'desc')->get();
                    //for all tasks


                    //for complete task
                    $completeTask = Task::whereHas('project', function ($query) use (&$workspace_id) {
                        $query->where('workspace', $workspace_id);
                    })
                    ->whereHas('stage', function ($query) use ($doneStage) {
                        $query->whereIn('status', $doneStage);
                    })->count();
                    //for complete task


                    //for total task
                    $totalTask = Task::whereHas('project', function ($query) use (&$workspace_id) {
                        $query->where('workspace', $workspace_id);
                    })->count();
                    //for total task

                    //for over due task
                    $overDueTasks = Task::whereHas('project', function ($query) use (&$workspace_id) {
                        $query->where('workspace', $workspace_id);
                    })->whereHas('stage', function ($query) use ($doneStage) {
                        $query->whereNotIn('status' , $doneStage);
                    })->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                     //for over due task


                    //for monthly task report


                    $report = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                    ->join('stages', 'tasks.status', '=', 'stages.id')
                    ->where('projects.workspace', $workspace_id)
                    ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                    ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                    ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                    ->selectRaw('COUNT(*) as total_created_task')
                    ->groupBy('projects.workspace', 'month')
                    ->orderBy('projects.workspace')
                    ->orderBy('month')
                    ->get();


                    $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                        array_push($MonthArr, $item->month);
                        array_push($CompletedTaskArr, $item->total_completed_task);
                        array_push($PendingTaskArr, $item->total_pending_task);
                        array_push($CreatedTaskArr, $item->total_created_task);
                    });
                    $reportData = $report->values()->all();
                    $result = [
                        'MonthArr' => $MonthArr,
                        'CompletedTaskArr' => $CompletedTaskArr,
                        'PendingTaskArr' => $PendingTaskArr,
                        'CreatedTaskArr' => $CreatedTaskArr,
                    ];


                     //for monthly task report
                }

            }

            //for task statictis chart
            $taskStatistics = $tasks->groupBy('status')->map->count()->values();
            $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();
            $taskStatisticsColors = ['Todo' => '#008FFB', 'In Progress' => '#00E396', 'Review' => '#FEB019', 'Done' => '#FF4560'];
            $taskChartColor = array_intersect_key($taskStatisticsColors, array_flip($taskStatisticsKeys));
            $taskCounts = $tasks->groupBy('status')->map->count();
            $totalCount = $taskCounts->sum();
            $taskPercentages = $taskCounts->map(function ($count) use ($totalCount) {
                return ($count / $totalCount) * 100;
            });
            //for task statictis chart

            $chartData = [];
            $blade_type = 'HOD';
            $taskStatus = ['All', 'Todo', 'In Progress', 'Review', 'Done'];

            return view(
                'home',
                compact(
                    'taskChartColor',
                    'currentStatus',
                    'taskStatus',
                    'blade_type',
                    'taskStatisticsKeys',
                    'currentWorkspace',
                    // 'totalProject',
                    'totalTask',
                    // 'totalMembers',
                    // 'arrProcessLabel',
                    // 'arrProcessPer',
                    // 'arrProcessClass',
                    'completeTask',
                    'tasks',
                    'chartData',
                    // 'inProgressProjects',
                    // 'dueDateProjects',
                    // 'inProgressTask',
                    'overDueTasks',
                    // 'workspace_type',
                    // 'projects',
                    'taskStatistics',
                    'result',
                    'taskPercentages',
                    // 'hod_workspaces',
                    'check_home',
                    // 'departmentList'
                )
            );

        } else {
            return redirect()->back()->with('error', __("No Workspace Found Under This HOD "));
        }
    }

    public function single_hod_report($hod_id, $slug = '', $currentStatus = 'All')
    {
        // SINGLE HOD REPORT
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $check_home = 0;
        $model_has_role = ModelHasRole::where('model_id', $hod_id)->first();
        $workspaces = $model_has_role->workspace_id;
        $workspace_id = json_decode($workspaces);

        //for workspaces query

        if (is_array($workspace_id)) {

            $hod_workspaces = Workspace::
                whereIn('workspaces.id', $workspace_id)
                ->get();

            $department_id = json_decode($model_has_role->department_id);
            $depart_user_role_id = json_decode($model_has_role->depart_user_role_id);
            $doneStage = Stage::whereIn('workspace_id', $workspace_id)->where('complete', '=', '1')->pluck('id');

            $MonthArr = [];
            $CompletedTaskArr = [];
            $PendingTaskArr = [];
            $CreatedTaskArr = [];

            if (is_array($department_id)) {

                if (is_array($depart_user_role_id)) {

                    //for all tasks
                    $tasks = Task::with('project', 'stage')->select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("stages", "stages.id", "=", "tasks.status")
                    ->whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                            $query->whereIn('workspace', $workspace_id);
                            $query->whereIn('department_id', $department_id);
                    })
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })
                    ->orderBy('tasks.id', 'desc')->get();
                    //for all tasks


                    //for complete task
                    $completeTask = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                        $query->whereIn('workspace', $workspace_id);
                        $query->whereIn('department_id', $department_id);
                    })
                    ->whereHas('stage', function ($query) use ($doneStage) {
                        $query->whereIn('status', $doneStage);
                    })
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })
                    ->count();
                    //for complete task


                    //for total task
                    $totalTask = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                        $query->whereIn('workspace', $workspace_id);
                        $query->whereIn('department_id', $department_id);
                     })
                     ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })
                    ->count();
                    //for total task

                    //for over due task
                    $overDueTasks = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                        $query->whereIn('workspace', $workspace_id);
                        $query->whereIn('department_id', $department_id);
                    })
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })
                    ->whereHas('stage', function ($query) use ($doneStage) {
                        $query->whereNotIn('status' , $doneStage);
                    })
                    ->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                     //for over due task


                    //for monthly task report


                    $report = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                    ->join('stages', 'tasks.status', '=', 'stages.id')
                    ->whereIn('projects.workspace', $workspace_id)
                    ->whereIn('projects.department_id', $department_id)
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })
                    ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                    ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                    ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                    ->selectRaw('COUNT(*) as total_created_task')
                    ->groupBy('projects.workspace', 'month')
                    ->orderBy('projects.workspace')
                    ->orderBy('month')
                    ->get();


                    $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                        array_push($MonthArr, $item->month);
                        array_push($CompletedTaskArr, $item->total_completed_task);
                        array_push($PendingTaskArr, $item->total_pending_task);
                        array_push($CreatedTaskArr, $item->total_created_task);
                    });
                    $reportData = $report->values()->all();
                    $result = [
                        'MonthArr' => $MonthArr,
                        'CompletedTaskArr' => $CompletedTaskArr,
                        'PendingTaskArr' => $PendingTaskArr,
                        'CreatedTaskArr' => $CreatedTaskArr,
                    ];


                } else { //no  depart user role


                    //for all tasks
                    $tasks = Task::with('project', 'stage')->select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("stages", "stages.id", "=", "tasks.status")
                    ->whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                            $query->whereIn('workspace', $workspace_id);
                            $query->whereIn('department_id', $department_id);
                    })->orderBy('tasks.id', 'desc')->get();
                    //for all tasks


                    //for complete task
                    $completeTask = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                        $query->whereIn('workspace', $workspace_id);
                        $query->whereIn('department_id', $department_id);
                    })

                    ->whereHas('stage', function ($query) use ($doneStage) {
                        $query->whereIn('status', $doneStage);
                    })->count();
                    //for complete task


                    //for total task
                    $totalTask = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                        $query->whereIn('workspace', $workspace_id);
                        $query->whereIn('department_id', $department_id);
                     })->count();
                    //for total task

                    //for over due task
                    $overDueTasks = Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                        $query->whereIn('workspace', $workspace_id);
                        $query->whereIn('department_id', $department_id);
                    })
                    ->whereHas('stage', function ($query) use ($doneStage) {
                        $query->whereNotIn('status' , $doneStage);
                    })
                    ->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                     //for over due task

                    //  dd($overDueTasks);

                    //for monthly task report


                    $report = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                    ->join('stages', 'tasks.status', '=', 'stages.id')
                    ->whereIn('projects.workspace', $workspace_id)
                    ->whereIn('projects.department_id', $department_id)
                    ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                    ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                    ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                    ->selectRaw('COUNT(*) as total_created_task')
                    ->groupBy('projects.workspace', 'month')
                    ->orderBy('projects.workspace')
                    ->orderBy('month')
                    ->get();


                    $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                        array_push($MonthArr, $item->month);
                        array_push($CompletedTaskArr, $item->total_completed_task);
                        array_push($PendingTaskArr, $item->total_pending_task);
                        array_push($CreatedTaskArr, $item->total_created_task);
                    });
                    $reportData = $report->values()->all();
                    $result = [
                        'MonthArr' => $MonthArr,
                        'CompletedTaskArr' => $CompletedTaskArr,
                        'PendingTaskArr' => $PendingTaskArr,
                        'CreatedTaskArr' => $CreatedTaskArr,
                    ];

                }



            } else {  // no depart

                if (is_array($depart_user_role_id)) {


                    //for all tasks
                    $tasks = Task::with('project', 'stage')->select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("stages", "stages.id", "=", "tasks.status")
                    ->whereHas('project', function ($query) use ($workspace_id) {
                            $query->whereIn('workspace', $workspace_id);
                    })
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })->orderBy('tasks.id', 'desc')->get();
                    //for all tasks

                    //for complete task
                    $completeTask = Task::whereHas('project', function ($query) use (&$workspace_id) {
                        $query->whereIn('workspace', $workspace_id);
                    })
                    ->whereHas('stage', function ($query) use ($doneStage) {
                        $query->whereIn('status', $doneStage);
                    })
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })->count();
                    //for complete task



                   //for total task
                    $totalTask = Task::whereHas('project', function ($query) use (&$workspace_id) {
                        $query->whereIn('workspace', $workspace_id);
                    })
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })->count();
                    //for total task

                    //for over due task
                    $overDueTasks = Task::whereHas('project', function ($query) use (&$workspace_id) {
                        $query->whereIn('workspace', $workspace_id);
                    })
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })->whereHas('stage', function ($query) use ($doneStage) {
                        $query->whereNotIn('status' , $doneStage);
                    })->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                     //for over due task


                    //for monthly task report
                    $report = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                    ->join('stages', 'tasks.status', '=', 'stages.id')
                    ->whereIn('projects.workspace', $workspace_id)
                    ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                        $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                            $query->whereIn('role_id', $depart_user_role_id);
                        });
                    })
                    ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                    ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                    ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                    ->selectRaw('COUNT(*) as total_created_task')
                    ->groupBy('projects.workspace', 'month')
                    ->orderBy('projects.workspace')
                    ->orderBy('month')
                    ->get();


                    $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                        array_push($MonthArr, $item->month);
                        array_push($CompletedTaskArr, $item->total_completed_task);
                        array_push($PendingTaskArr, $item->total_pending_task);
                        array_push($CreatedTaskArr, $item->total_created_task);
                    });
                    $reportData = $report->values()->all();
                    $result = [
                        'MonthArr' => $MonthArr,
                        'CompletedTaskArr' => $CompletedTaskArr,
                        'PendingTaskArr' => $PendingTaskArr,
                        'CreatedTaskArr' => $CreatedTaskArr,
                    ];

                } else { // no  depart user role



                    //for all tasks
                    $tasks = Task::with('project', 'stage')->select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("stages", "stages.id", "=", "tasks.status")
                    ->whereHas('project', function ($query) use ($workspace_id) {
                            $query->whereIn('workspace', $workspace_id);
                    })->orderBy('tasks.id', 'desc')->get();
                    //for all tasks


                    //for complete task
                    $completeTask = Task::whereHas('project', function ($query) use (&$workspace_id) {
                        $query->whereIn('workspace', $workspace_id);
                    })
                    ->whereHas('stage', function ($query) use ($doneStage) {
                        $query->whereIn('status', $doneStage);
                    })->count();
                    //for complete task


                    //for total task
                    $totalTask = Task::whereHas('project', function ($query) use (&$workspace_id) {
                        $query->whereIn('workspace', $workspace_id);
                    })->count();
                    //for total task

                    //for over due task
                    $overDueTasks = Task::whereHas('project', function ($query) use (&$workspace_id) {
                        $query->whereIn('workspace', $workspace_id);
                    })->whereHas('stage', function ($query) use ($doneStage) {
                        $query->whereNotIn('status' , $doneStage);
                    })->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                     //for over due task


                    //for monthly task report


                    $report = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                    ->join('stages', 'tasks.status', '=', 'stages.id')
                    ->whereIn('projects.workspace', $workspace_id)
                    ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                    ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                    ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                    ->selectRaw('COUNT(*) as total_created_task')
                    ->groupBy('projects.workspace', 'month')
                    ->orderBy('projects.workspace')
                    ->orderBy('month')
                    ->get();


                    $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                        array_push($MonthArr, $item->month);
                        array_push($CompletedTaskArr, $item->total_completed_task);
                        array_push($PendingTaskArr, $item->total_pending_task);
                        array_push($CreatedTaskArr, $item->total_created_task);
                    });
                    $reportData = $report->values()->all();
                    $result = [
                        'MonthArr' => $MonthArr,
                        'CompletedTaskArr' => $CompletedTaskArr,
                        'PendingTaskArr' => $PendingTaskArr,
                        'CreatedTaskArr' => $CreatedTaskArr,
                    ];


                     //for monthly task report
                }

            }

            //for task statictis chart
            $taskStatistics = $tasks->groupBy('status')->map->count()->values();
            $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();
            $taskStatisticsColors = ['Todo' => '#008FFB', 'In Progress' => '#00E396', 'Review' => '#FEB019', 'Done' => '#FF4560'];
            $taskChartColor = array_intersect_key($taskStatisticsColors, array_flip($taskStatisticsKeys));
            $taskCounts = $tasks->groupBy('status')->map->count();
            $totalCount = $taskCounts->sum();
            $taskPercentages = $taskCounts->map(function ($count) use ($totalCount) {
                return ($count / $totalCount) * 100;
            });
            //for task statictis chart

            $chartData = [];
            $blade_type = 'HOD';
            $taskStatus = ['All', 'Todo', 'In Progress', 'Review', 'Done'];

            return view(
                'home',
                compact(
                    'taskChartColor',
                    'currentStatus',
                    'taskStatus',
                    'blade_type',
                    'taskStatisticsKeys',
                    'currentWorkspace',
                    // 'totalProject',
                    'totalTask',
                    // 'totalMembers',
                    // 'arrProcessLabel',
                    // 'arrProcessPer',
                    // 'arrProcessClass',
                    'completeTask',
                    'tasks',
                    'chartData',
                    // 'inProgressProjects',
                    // 'dueDateProjects',
                    // 'inProgressTask',
                    'overDueTasks',
                    // 'workspace_type',
                    // 'projects',
                    'taskStatistics',
                    'result',
                    'taskPercentages',
                    'hod_workspaces',
                    'check_home',
                    // 'departmentList'
                )
            );

        } else {
            return redirect()->back()->with('error', __("No Workspace Found Under This HOD "));
        }
        // SINGLE HOD REPORT

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $check_home = 0;
        $totalTask = 0;
        $completeTask = 0;
        $overDueTasks = 0;

        $tasks = new Collection();

        $model_has_role = ModelHasRole::where('model_id', $hod_id)->first();
        $workspaces = $model_has_role->workspace_id;
        $workspace_id = json_decode($workspaces);
        $department_id = json_decode($model_has_role->department_id);


        $depart_user_role_id = json_decode($model_has_role->depart_user_role_id);
        if (is_array($department_id)) {

            if (is_null($depart_user_role_id)) {

                $workspace = Workspace::whereIn('id', $workspace_id)->first();
                $departments = Department::whereIn('id', $department_id)->get();

                foreach ($departments as $department) {
                    //completed tasks
                    $doneStage = Stage::where('workspace_id', '=', $workspace->id)->where('complete', '=', '1')->first();
                    //   dd($doneStage);
                    $completeTask += $department->projects->flatMap(function ($project) use ($doneStage) {
                        return $project->task->where('status', $doneStage->id);
                    })->count();
                    //
                    $totalTask += $department->projects->flatMap(function ($project) {
                        return $project->task;
                    })->count();
                    $overDueTasks += $department->projects->flatMap(function ($project) use ($doneStage) {
                        return $project->task->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->where('status', '!=', $doneStage->id);
                    })->count();
                }


                if ($currentStatus == 'All') {
                    $nestedtasks = Task::with('project', 'stage')->select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("stages", "stages.id", "=", "tasks.status")
                        ->whereHas('project', function ($query) use ($workspace_id, $department_id) {
                            $query->whereIn('department_id', $department_id);
                            // $query->whereIn('workspace', $workspace_id);
                        })->orderBy('tasks.id', 'desc')->get();

                } else {
                    $nestedtasks = Task::with('project', 'stage')->select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("stages", "stages.id", "=", "tasks.status")
                        ->whereHas('project', function ($query) use ($workspace_id, $department_id) {
                            $query->whereIn('department_id', $department_id);
                            // $query->whereIn('workspace', $workspace_id);
                        })->orderBy('tasks.id', 'desc')
                        ->whereHas('stage', function ($query) use ($currentStatus) {
                            $query->where('name', $currentStatus);
                        })
                        ->get();
                }

                $tasks = $tasks->merge($nestedtasks);
                // dd($tasks->merge($nestedtasks));
            } else {

                $workspace = Workspace::whereIn('id', $workspace_id)->first();
                $departments = Department::whereIn('id', $department_id)->get();

                foreach ($departments as $department) {
                    //completed tasks
                    $doneStage = Stage::where('workspace_id', '=', $workspace->id)->where('complete', '=', '1')->first();
                    //   dd($doneStage);
                    $completeTask += $department->projects->flatMap(function ($project) use ($doneStage, &$department, &$depart_user_role_id) {
                        return $project->task->where('status', $doneStage->id)
                            ->filter(function ($task) use ($department, &$depart_user_role_id) {
                                return $task->assignees()->whereHas('departments', function ($query) use (&$department, &$depart_user_role_id) {
                                    $query->where('department_id', $department->id)
                                        ->whereIn('role_id', $depart_user_role_id);
                                })
                                    ->get()->isNotEmpty();
                            });
                    })->count();
                    //
                    $totalTask += $department->projects->flatMap(function ($project) use ($department, &$depart_user_role_id) {
                        return $project->task->filter(function ($task) use ($department, &$depart_user_role_id) {
                            //
                            return $task->assignees()->whereHas('departments', function ($query) use (&$department, &$depart_user_role_id) {
                                $query->where('department_id', $department->id)
                                    ->whereIn('role_id', $depart_user_role_id);
                            })
                                ->get()->isNotEmpty();
                            //
                            //    $filteredUsers = $task->filterTaskUsersByRoles($department->id, $depart_user_role_id);
                            //     $assignedUserIds = explode(',', $task->assign_to);
                            //     // Check if any of the assigned user IDs are in the filtered users
                            //     return collect($assignedUserIds)->intersect($filteredUsers->pluck('id'))->isNotEmpty();
                        });
                    })->count();
                    // dd($totalTask);
                    $overDueTasks += $department->projects->flatMap(function ($project) use ($doneStage, &$department, &$depart_user_role_id) {
                        return $project->task->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->where('status', '!=', $doneStage->id)
                            ->filter(function ($task) use ($department, &$depart_user_role_id) {
                                return $task->assignees()->whereHas('departments', function ($query) use (&$department, &$depart_user_role_id) {
                                    $query->where('department_id', $department->id)
                                        ->whereIn('role_id', $depart_user_role_id);
                                })
                                    ->get()->isNotEmpty();
                            });
                    })->count();
                }

                if ($currentStatus == 'All') {
                    $nestedtasks = Task::with('project', 'stage')->select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("stages", "stages.id", "=", "tasks.status")
                        ->whereHas('project', function ($query) use ($workspace_id, &$department_id) {
                            $query->whereIn('department_id', $department_id);
                            // $query->whereIn('workspace', $workspace_id);
                        })
                        ->whereHas('assignees', function ($query) use ($department_id, &$depart_user_role_id) {
                            $query->whereHas('departments', function ($q) use (&$department_id, &$depart_user_role_id) {
                                $q->where('department_id', $department_id)
                                    ->whereIn('role_id', $depart_user_role_id);
                            });
                            // $query->whereIn('department_id', $department_id)
                            //     ->whereIn('role_id', $depart_user_role_id);
                        })
                        ->orderBy('tasks.id', 'desc')->get();

                } else {
                    $nestedtasks = Task::with('project', 'stage')->select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("stages", "stages.id", "=", "tasks.status")
                        ->whereHas('project', function ($query) use ($workspace_id, $department_id) {
                            $query->whereIn('department_id', $department_id);
                            // $query->whereIn('workspace', $workspace_id);
                        })->orderBy('tasks.id', 'desc')
                        ->whereHas('stage', function ($query) use ($currentStatus) {
                            $query->where('name', $currentStatus);
                        })
                        ->whereHas('assignees', function ($query) use ($department_id, &$depart_user_role_id) {
                            $query->whereHas('departments', function ($q) use (&$department_id, &$depart_user_role_id) {
                                $q->where('department_id', $department_id)
                                    ->whereIn('role_id', $depart_user_role_id);
                            });
                            // $query->whereIn('department_id', $department_id)
                            //     ->whereIn('role_id', $depart_user_role_id);
                        })
                        ->get();
                }

                $tasks = $tasks->merge($nestedtasks);
            }

            $taskStatistics = $tasks->groupBy('status')->map->count()->values();
            $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();
            $taskStatisticsColors = ['Todo' => '#008FFB', 'In Progress' => '#00E396', 'Review' => '#FEB019', 'Done' => '#FF4560'];
            $taskChartColor = array_intersect_key($taskStatisticsColors, array_flip($taskStatisticsKeys));
            $taskCounts = $tasks->groupBy('status')->map->count();
            $totalCount = $taskCounts->sum();
            $taskPercentages = $taskCounts->map(function ($count) use ($totalCount) {
                return ($count / $totalCount) * 100;
            });
            $MonthArr = [];
            $CompletedTaskArr = [];
            $PendingTaskArr = [];
            $CreatedTaskArr = [];
            $report = DB::table('tasks')
                ->join('projects', 'tasks.project_id', '=', 'projects.id')
                ->join('stages', 'tasks.status', '=', 'stages.id')
                ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                ->selectRaw('COUNT(*) as total_created_task')
                // ->where('projects.workspace', $id)
                // ->whereIn('projects.workspace', $workspace_id)
                ->whereIn('projects.department_id', $department_id)
                // ->groupBy('projects.workspace', 'month')
                ->groupBy('projects.department_id', 'month')
                // ->orderBy('projects.workspace')
                ->orderBy('projects.department_id')
                ->orderBy('month')
                ->get();
            // dd($report);
            $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
                array_push($MonthArr, $item->month);
                array_push($CompletedTaskArr, $item->total_completed_task);
                array_push($PendingTaskArr, $item->total_pending_task);
                array_push($CreatedTaskArr, $item->total_created_task);
            });
            $reportData = $report->values()->all();
            $result = [
                'MonthArr' => $MonthArr,
                'CompletedTaskArr' => $CompletedTaskArr,
                'PendingTaskArr' => $PendingTaskArr,
                'CreatedTaskArr' => $CreatedTaskArr,
            ];
            $inProgressTask = Task::whereIn('projects.workspace', $workspace_id)
                ->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
                ->join("projects", "projects.id", "=", "user_projects.project_id")
                ->where('tasks.status', '=', '82')->count();
            $chartData = [];
            $blade_type = 'SingleHOD';
            $taskStatus = ['All', 'Todo', 'In Progress', 'Review', 'Done'];
            return view(
                'home',
                compact(
                    'taskChartColor',
                    'currentStatus',
                    'taskStatus',
                    'blade_type',
                    'taskStatisticsKeys',
                    'currentWorkspace',
                    // 'totalProject',
                    'totalTask',
                    // 'totalMembers',
                    // 'arrProcessLabel',
                    // 'arrProcessPer',
                    // 'arrProcessClass',
                    'completeTask',
                    'tasks',
                    'chartData',
                    // 'inProgressProjects',
                    // 'dueDateProjects',
                    'inProgressTask',
                    'overDueTasks',
                    // 'workspace_type',
                    // 'projects',
                    'taskStatistics',
                    'result',
                    'taskPercentages',
                    // 'hod_workspaces',
                    'check_home',
                    // 'departmentList',
                    // 'HODs',
                    'hod_id'
                )
            );
        } else {
            return redirect()->back()->with('error', __("No Department Found Under This HOD "));
        }

    }

    public function single_executive_report($executive_id, $slug = '', $currentStatus = 'All')
    {

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);


        $check_home = 0;
        // $model_has_role_ceo = ModelHasRole::where('model_id', auth()->id())->first();
        // $executives_id = json_decode($model_has_role_ceo->executives);

        // $totalTask = 0;
        // $completeTask = 0;
        // $overDueTasks = 0;

        // $tasks = new Collection();


        // $model_has_role_executive = ModelHasRole::where('model_id', $executive_id)->first();
        // $HODs_id = json_decode($model_has_role_executive->hods);
        // $executiveTasks = new Collection();
        // if (!is_null($HODs_id)) {
        //     foreach ($HODs_id as $key => $hod_id) {
        //         $model_has_role = ModelHasRole::where('model_id', $hod_id)->first();
        //         $workspaces = $model_has_role->workspace_id;
        //         $workspace_id = json_decode($workspaces);
        //         $department_id = json_decode($model_has_role->department_id);
        //         $depart_user_role_id = json_decode($model_has_role->depart_user_role_id);

        //         if (is_array($department_id)) {

        //             if (is_null($depart_user_role_id)) {




        //                 $workspace = Workspace::whereIn('id', $workspace_id)->first();
        //                 $departments = Department::whereIn('id', $department_id)->get();

        //                 foreach ($departments as $department) {
        //                     //completed tasks
        //                     $doneStage = Stage::where('workspace_id', '=', $workspace->id)->where('complete', '=', '1')->first();
        //                     //   dd($doneStage);
        //                     $completeTask += $department->projects->flatMap(function ($project) use ($doneStage) {
        //                         return $project->task->where('status', $doneStage->id);
        //                     })->count();
        //                     //
        //                     $totalTask += $department->projects->flatMap(function ($project) {
        //                         return $project->task;
        //                     })->count();
        //                     $overDueTasks += $department->projects->flatMap(function ($project) use ($doneStage) {
        //                         return $project->task->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->where('status', '!=', $doneStage->id);
        //                     })->count();
        //                 }


        //                 if ($currentStatus == 'All') {
        //                     $nestedtasks = Task::with('project', 'stage')->select([
        //                         'tasks.*',
        //                         'stages.name as status',
        //                         'stages.complete',
        //                     ])->join("stages", "stages.id", "=", "tasks.status")
        //                         ->whereHas('project', function ($query) use ($workspace_id, $department_id) {
        //                             $query->whereIn('department_id', $department_id);
        //                             // $query->whereIn('workspace', $workspace_id);
        //                         })->orderBy('tasks.id', 'desc')->get();

        //                 } else {
        //                     $nestedtasks = Task::with('project', 'stage')->select([
        //                         'tasks.*',
        //                         'stages.name as status',
        //                         'stages.complete',
        //                     ])->join("stages", "stages.id", "=", "tasks.status")
        //                         ->whereHas('project', function ($query) use ($workspace_id, $department_id) {
        //                             $query->whereIn('department_id', $department_id);
        //                             // $query->whereIn('workspace', $workspace_id);
        //                         })->orderBy('tasks.id', 'desc')
        //                         ->whereHas('stage', function ($query) use ($currentStatus) {
        //                             $query->where('name', $currentStatus);
        //                         })
        //                         ->get();
        //                 }

        //                 $executiveTasks = $executiveTasks->merge($nestedtasks);
        //                 // dd($tasks->merge($nestedtasks));
        //             } else {

        //                 $workspace = Workspace::whereIn('id', $workspace_id)->first();
        //                 $departments = Department::whereIn('id', $department_id)->get();

        //                 foreach ($departments as $department) {
        //                     //completed tasks
        //                     $doneStage = Stage::where('workspace_id', '=', $workspace->id)->where('complete', '=', '1')->first();
        //                     //   dd($doneStage);
        //                     $completeTask += $department->projects->flatMap(function ($project) use ($doneStage, &$department, &$depart_user_role_id) {
        //                         return $project->task->where('status', $doneStage->id)
        //                             ->filter(function ($task) use ($department, &$depart_user_role_id) {
        //                                 return $task->assignees()->whereHas('departments', function ($query) use (&$department, &$depart_user_role_id) {
        //                                     $query->where('department_id', $department->id)
        //                                         ->whereIn('role_id', $depart_user_role_id);
        //                                 })
        //                                     ->get()->isNotEmpty();
        //                             });
        //                     })->count();
        //                     //
        //                     $totalTask += $department->projects->flatMap(function ($project) use ($department, &$depart_user_role_id) {
        //                         return $project->task->filter(function ($task) use ($department, &$depart_user_role_id) {
        //                             //
        //                             return $task->assignees()->whereHas('departments', function ($query) use (&$department, &$depart_user_role_id) {
        //                                 $query->where('department_id', $department->id)
        //                                     ->whereIn('role_id', $depart_user_role_id);
        //                             })
        //                                 ->get()->isNotEmpty();
        //                             //
        //                             //    $filteredUsers = $task->filterTaskUsersByRoles($department->id, $depart_user_role_id);
        //                             //     $assignedUserIds = explode(',', $task->assign_to);
        //                             //     // Check if any of the assigned user IDs are in the filtered users
        //                             //     return collect($assignedUserIds)->intersect($filteredUsers->pluck('id'))->isNotEmpty();
        //                         });
        //                     })->count();
        //                     // dd($totalTask);
        //                     $overDueTasks += $department->projects->flatMap(function ($project) use ($doneStage, &$department, &$depart_user_role_id) {
        //                         return $project->task->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->where('status', '!=', $doneStage->id)
        //                             ->filter(function ($task) use ($department, &$depart_user_role_id) {
        //                                 return $task->assignees()->whereHas('departments', function ($query) use (&$department, &$depart_user_role_id) {
        //                                     $query->where('department_id', $department->id)
        //                                         ->whereIn('role_id', $depart_user_role_id);
        //                                 })
        //                                     ->get()->isNotEmpty();
        //                             });
        //                     })->count();
        //                 }

        //                 if ($currentStatus == 'All') {
        //                     $nestedtasks = Task::with('project', 'stage')->select([
        //                         'tasks.*',
        //                         'stages.name as status',
        //                         'stages.complete',
        //                     ])->join("stages", "stages.id", "=", "tasks.status")
        //                         ->whereHas('project', function ($query) use ($workspace_id, &$department_id) {
        //                             $query->whereIn('department_id', $department_id);
        //                             // $query->whereIn('workspace', $workspace_id);
        //                         })
        //                         ->whereHas('assignees', function ($query) use ($department_id, &$depart_user_role_id) {
        //                             $query->whereHas('departments', function ($q) use (&$department_id, &$depart_user_role_id) {
        //                                 $q->where('department_id', $department_id)
        //                                     ->whereIn('role_id', $depart_user_role_id);
        //                             });
        //                             // $query->whereIn('department_id', $department_id)
        //                             //     ->whereIn('role_id', $depart_user_role_id);
        //                         })
        //                         ->orderBy('tasks.id', 'desc')->get();

        //                 } else {
        //                     $nestedtasks = Task::with('project', 'stage')->select([
        //                         'tasks.*',
        //                         'stages.name as status',
        //                         'stages.complete',
        //                     ])->join("stages", "stages.id", "=", "tasks.status")
        //                         ->whereHas('project', function ($query) use ($workspace_id, $department_id) {
        //                             $query->whereIn('department_id', $department_id);
        //                             // $query->whereIn('workspace', $workspace_id);
        //                         })->orderBy('tasks.id', 'desc')
        //                         ->whereHas('stage', function ($query) use ($currentStatus) {
        //                             $query->where('name', $currentStatus);
        //                         })
        //                         ->whereHas('assignees', function ($query) use ($department_id, &$depart_user_role_id) {
        //                             $query->whereHas('departments', function ($q) use (&$department_id, &$depart_user_role_id) {
        //                                 $q->where('department_id', $department_id)
        //                                     ->whereIn('role_id', $depart_user_role_id);
        //                             });
        //                             // $query->whereIn('department_id', $department_id)
        //                             //     ->whereIn('role_id', $depart_user_role_id);
        //                         })
        //                         ->get();
        //                 }

        //                 $executiveTasks = $executiveTasks->merge($nestedtasks);
        //             }
        //         }

        //     }
        // }


        // $tasks = $tasks->merge($executiveTasks);
        // $taskStatistics = $tasks->groupBy('status')->map->count()->values();
        // $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();
        // $taskStatisticsColors = ['Todo' => '#008FFB', 'In Progress' => '#00E396', 'Review' => '#FEB019', 'Done' => '#FF4560'];
        // $taskChartColor = array_intersect_key($taskStatisticsColors, array_flip($taskStatisticsKeys));
        // $taskCounts = $tasks->groupBy('status')->map->count();
        // $totalCount = $taskCounts->sum();
        // $taskPercentages = $taskCounts->map(function ($count) use ($totalCount) {
        //     return ($count / $totalCount) * 100;
        // });




        // $MonthArr = [];
        // $CompletedTaskArr = [];
        // $PendingTaskArr = [];
        // $CreatedTaskArr = [];

        // $report = Task::
        //     join('projects', 'tasks.project_id', '=', 'projects.id')
        //     ->join('stages', 'tasks.status', '=', 'stages.id')
        //     ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
        //     ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
        //     ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
        //     ->selectRaw('COUNT(*) as total_created_task')

        //     // ->where('projects.workspace', $id)
        //     ->whereIn('projects.workspace', $workspace_id)
        //     ->whereIn('projects.department_id', $department_id)
        //     // ->groupBy('projects.workspace', 'month')
        //     ->groupBy('projects.department_id', 'month')
        //     // ->orderBy('projects.workspace')
        //     ->orderBy('projects.department_id')
        //     ->orderBy('month')
        //     ->get();

        // // dd($report);
        // $report = $report->map(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
        //     array_push($MonthArr, $item->month);
        //     array_push($CompletedTaskArr, $item->total_completed_task);
        //     array_push($PendingTaskArr, $item->total_pending_task);
        //     array_push($CreatedTaskArr, $item->total_created_task);
        // });
        // $reportData = $report->values()->all();
        // $result = [
        //     'MonthArr' => $MonthArr,
        //     'CompletedTaskArr' => $CompletedTaskArr,
        //     'PendingTaskArr' => $PendingTaskArr,
        //     'CreatedTaskArr' => $CreatedTaskArr,
        // ];



        // $inProgressTask = Task::
        //     join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
        //     ->join("projects", "projects.id", "=", "user_projects.project_id")
        //     ->where('tasks.status', '=', '82')->count();


        $model_has_role_executive = ModelHasRole::where('model_id', $executive_id)->first();
        $HODs_id = json_decode($model_has_role_executive->hods);
        if (is_null($HODs_id)) {
            $HODs_id = [];

        }
        $HODs = User::whereIn('id', $HODs_id)->get();

        if (is_array($HODs_id)) {
        // global variables
        $totalTask = 0;
        $completeTask = 0;
        $overDueTasks = 0;
        $tasks = new Collection();
        // $report = new Collection();
        $report =  [];

        $MonthArr = [];
        $CompletedTaskArr = [];
        $PendingTaskArr = [];
        $CreatedTaskArr = [];


        foreach ($HODs_id as $key => $singleHodId) {
            $model_has_role = ModelHasRole::where('model_id', $singleHodId)->first();
            $workspaces = $model_has_role->workspace_id;
            $workspace_id = json_decode($workspaces);

            //for workspaces query

            if (is_array($workspace_id)) {

                $hod_workspaces = Workspace::
                    whereIn('workspaces.id', $workspace_id)
                    ->get();

                $department_id = json_decode($model_has_role->department_id);
                $depart_user_role_id = json_decode($model_has_role->depart_user_role_id);
                $doneStage = Stage::whereIn('workspace_id', $workspace_id)->where('complete', '=', '1')->pluck('id');



                if (is_array($department_id)) {

                    if (is_array($depart_user_role_id)) {

                        //for all tasks
                        $nestedtasks = Task::with('project', 'stage')->select([
                            'tasks.*',
                            'stages.name as status',
                            'stages.complete',
                        ])->join("stages", "stages.id", "=", "tasks.status")
                        ->whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                $query->whereIn('workspace', $workspace_id);
                                $query->whereIn('department_id', $department_id);
                        })
                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                $query->whereIn('role_id', $depart_user_role_id);
                            });
                        })
                        ->orderBy('tasks.id', 'desc')->get();
                        //for all tasks


                        //for complete task
                        $completeTask += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                            $query->whereIn('workspace', $workspace_id);
                            $query->whereIn('department_id', $department_id);
                        })
                        ->whereHas('stage', function ($query) use ($doneStage) {
                            $query->whereIn('status', $doneStage);
                        })
                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                $query->whereIn('role_id', $depart_user_role_id);
                            });
                        })
                        ->count();
                        //for complete task


                        //for total task
                        $totalTask += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                            $query->whereIn('workspace', $workspace_id);
                            $query->whereIn('department_id', $department_id);
                         })
                         ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                $query->whereIn('role_id', $depart_user_role_id);
                            });
                        })
                        ->count();
                        //for total task

                        //for over due task
                        $overDueTasks += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                            $query->whereIn('workspace', $workspace_id);
                            $query->whereIn('department_id', $department_id);
                        })
                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                $query->whereIn('role_id', $depart_user_role_id);
                            });
                        })
                        ->whereHas('stage', function ($query) use ($doneStage) {
                            $query->whereNotIn('status' , $doneStage);
                        })
                        ->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                         //for over due task


                        //for monthly task report


                        $nestedReport = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                        ->join('stages', 'tasks.status', '=', 'stages.id')
                        ->whereIn('projects.workspace', $workspace_id)
                        ->whereIn('projects.department_id', $department_id)
                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                $query->whereIn('role_id', $depart_user_role_id);
                            });
                        })
                        ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                        ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                        ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                        ->selectRaw('COUNT(*) as total_created_task')
                        ->groupBy('projects.workspace', 'month')
                        ->orderBy('projects.workspace')
                        ->orderBy('month')
                        ->get();

                    } else { //no  depart user role


                        //for all tasks
                        $nestedtasks = Task::with('project', 'stage')->select([
                            'tasks.*',
                            'stages.name as status',
                            'stages.complete',
                        ])->join("stages", "stages.id", "=", "tasks.status")
                        ->whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                                $query->whereIn('workspace', $workspace_id);
                                $query->whereIn('department_id', $department_id);
                        })->orderBy('tasks.id', 'desc')->get();
                        //for all tasks


                        //for complete task
                        $completeTask += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                            $query->whereIn('workspace', $workspace_id);
                            $query->whereIn('department_id', $department_id);
                        })

                        ->whereHas('stage', function ($query) use ($doneStage) {
                            $query->whereIn('status', $doneStage);
                        })->count();
                        //for complete task


                        //for total task
                        $totalTask += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                            $query->whereIn('workspace', $workspace_id);
                            $query->whereIn('department_id', $department_id);
                         })->count();
                        //for total task

                        //for over due task
                        $overDueTasks += Task::whereHas('project', function ($query) use ($workspace_id,&$department_id) {
                            $query->whereIn('workspace', $workspace_id);
                            $query->whereIn('department_id', $department_id);
                        })
                        ->whereHas('stage', function ($query) use ($doneStage) {
                            $query->whereNotIn('status' , $doneStage);
                        })
                        ->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                         //for over due task



                        //for monthly task report


                        $nestedReport = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                        ->join('stages', 'tasks.status', '=', 'stages.id')
                        ->whereIn('projects.workspace', $workspace_id)
                        ->whereIn('projects.department_id', $department_id)
                        ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                        ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                        ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                        ->selectRaw('COUNT(*) as total_created_task')
                        ->groupBy('projects.workspace', 'month')
                        ->orderBy('projects.workspace')
                        ->orderBy('month')
                        ->get();
                    }



                } else {  // no depart

                    if (is_array($depart_user_role_id)) {


                        //for all tasks
                        $nestedtasks = Task::with('project', 'stage')->select([
                            'tasks.*',
                            'stages.name as status',
                            'stages.complete',
                        ])->join("stages", "stages.id", "=", "tasks.status")
                        ->whereHas('project', function ($query) use ($workspace_id) {
                                $query->whereIn('workspace', $workspace_id);
                        })
                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                $query->whereIn('role_id', $depart_user_role_id);
                            });
                        })->orderBy('tasks.id', 'desc')->get();
                        //for all tasks

                        //for complete task
                        $completeTask += Task::whereHas('project', function ($query) use (&$workspace_id) {
                            $query->whereIn('workspace', $workspace_id);
                        })
                        ->whereHas('stage', function ($query) use ($doneStage) {
                            $query->whereIn('status', $doneStage);
                        })
                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                $query->whereIn('role_id', $depart_user_role_id);
                            });
                        })->count();
                        //for complete task



                       //for total task
                        $totalTask += Task::whereHas('project', function ($query) use (&$workspace_id) {
                            $query->whereIn('workspace', $workspace_id);
                        })
                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                $query->whereIn('role_id', $depart_user_role_id);
                            });
                        })->count();
                        //for total task

                        //for over due task
                        $overDueTasks += Task::whereHas('project', function ($query) use (&$workspace_id) {
                            $query->whereIn('workspace', $workspace_id);
                        })
                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                $query->whereIn('role_id', $depart_user_role_id);
                            });
                        })->whereHas('stage', function ($query) use ($doneStage) {
                            $query->whereNotIn('status' , $doneStage);
                        })->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                         //for over due task


                        //for monthly task report
                        $nestedReport = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                        ->join('stages', 'tasks.status', '=', 'stages.id')
                        ->whereIn('projects.workspace', $workspace_id)
                        ->whereHas('assignees', function ($query) use ($depart_user_role_id) {
                            $query->whereHas('departments', function ($query) use ($depart_user_role_id) {
                                $query->whereIn('role_id', $depart_user_role_id);
                            });
                        })
                        ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                        ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                        ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                        ->selectRaw('COUNT(*) as total_created_task')
                        ->groupBy('projects.workspace', 'month')
                        ->orderBy('projects.workspace')
                        ->orderBy('month')
                        ->get();

                    } else { // no  depart user role



                        //for all tasks
                        $nestedtasks = Task::with('project', 'stage')->select([
                            'tasks.*',
                            'stages.name as status',
                            'stages.complete',
                        ])->join("stages", "stages.id", "=", "tasks.status")
                        ->whereHas('project', function ($query) use ($workspace_id) {
                                $query->whereIn('workspace', $workspace_id);
                        })->orderBy('tasks.id', 'desc')->get();
                        //for all tasks


                        //for complete task
                        $completeTask += Task::whereHas('project', function ($query) use (&$workspace_id) {
                            $query->whereIn('workspace', $workspace_id);
                        })
                        ->whereHas('stage', function ($query) use ($doneStage) {
                            $query->whereIn('status', $doneStage);
                        })->count();
                        //for complete task


                        //for total task
                        $totalTask += Task::whereHas('project', function ($query) use (&$workspace_id) {
                            $query->whereIn('workspace', $workspace_id);
                        })->count();
                        //for total task

                        //for over due task
                        $overDueTasks += Task::whereHas('project', function ($query) use (&$workspace_id) {
                            $query->whereIn('workspace', $workspace_id);
                        })->whereHas('stage', function ($query) use ($doneStage) {
                            $query->whereNotIn('status' , $doneStage);
                        })->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->count();
                        //for over due task


                        //for monthly task report


                        $nestedReport = Task::join('projects', 'tasks.project_id', '=', 'projects.id')
                        ->join('stages', 'tasks.status', '=', 'stages.id')
                        ->whereIn('projects.workspace', $workspace_id)
                        ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                        ->selectRaw('SUM(CASE WHEN stages.name = "Done" THEN 1 ELSE 0 END) as total_completed_task')
                        ->selectRaw('SUM(CASE WHEN stages.name = "In Progress" THEN 1 ELSE 0 END) as total_pending_task')
                        ->selectRaw('COUNT(*) as total_created_task')
                        ->groupBy('projects.workspace', 'month')
                        ->orderBy('projects.workspace')
                        ->orderBy('month')
                        ->get();





                         //for monthly task report
                    }

                }

                // merge nested tasks to tasks
                $tasks = $tasks->merge($nestedtasks);
                // $report = $report->merge($nestedReport);
                $report[] = $nestedReport;
                // dd($nestedReport);


            }
        }
        // dd($report);
        $report = collect($report)->collapse()->groupBy('month');
        // for monthly progress reports
        $report->each(function ($item) use (&$MonthArr, &$CompletedTaskArr, &$PendingTaskArr, &$CreatedTaskArr) {
            $MonthArr[] = $item->first()->month; // Assuming all instances in the group have the same month
            $CompletedTaskArr[] = $item->sum('total_completed_task');
            $PendingTaskArr[] = $item->sum('total_pending_task');
            $CreatedTaskArr[] = $item->sum('total_created_task');
        });
        $reportData = $report->values()->all();
        $result = [
            'MonthArr' => $MonthArr,
            'CompletedTaskArr' => $CompletedTaskArr,
            'PendingTaskArr' => $PendingTaskArr,
            'CreatedTaskArr' => $CreatedTaskArr,
        ];
        // for monthly progress reports


        //for task statictis chart
        $taskStatistics = $tasks->groupBy('status')->map->count()->values();
        $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();
        $taskStatisticsColors = ['Todo' => '#008FFB', 'In Progress' => '#00E396', 'Review' => '#FEB019', 'Done' => '#FF4560'];
        $taskChartColor = array_intersect_key($taskStatisticsColors, array_flip($taskStatisticsKeys));
        $taskCounts = $tasks->groupBy('status')->map->count();
        $totalCount = $taskCounts->sum();
        $taskPercentages = $taskCounts->map(function ($count) use ($totalCount) {
            return ($count / $totalCount) * 100;
        });
        //for task statictis chart

        $chartData = [];
        $blade_type = 'SingleExecutive';
        $taskStatus = ['All', 'Todo', 'In Progress', 'Review', 'Done'];

        return view(
            'home',
            compact(
                'taskChartColor',
                'currentStatus',
                'taskStatus',
                'blade_type',
                'taskStatisticsKeys',
                'currentWorkspace',
                // 'totalProject',
                'totalTask',
                // 'totalMembers',
                // 'arrProcessLabel',
                // 'arrProcessPer',
                // 'arrProcessClass',
                'completeTask',
                'tasks',
                'chartData',
                // 'inProgressProjects',
                // 'dueDateProjects',
                // 'inProgressTask',
                'overDueTasks',
                // 'workspace_type',
                // 'projects',
                'taskStatistics',
                'result',
                'taskPercentages',
                // 'hod_workspaces',
                'check_home',
                // 'departmentList',
                // 'Executives',
                'executive_id'
            )
        );
    }else {

            return redirect()->back()->with('error', __("No HOD Found Under This Executive "));

    }
    }
}
