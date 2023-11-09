<?php

namespace App\Http\Controllers;

use App\Models\ClientProject;
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
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function landingPage()
    {
        return redirect('login');
        // if (!file_exists(storage_path() . "/installed")) {
        //     header('location:install');
        //     die;
        // }

        // if (env('DISPLAY_LANDING') == 'on' || env('DISPLAY_LANDING') == '' && \Schema::hasTable('landing_page_settings')) {


        //     return view('landingpage::layouts.landingpage');
        //     // return view('layouts.landing');

        // } else {
        //     return redirect('login');
        // }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($slug = '')
    {



        $userObj = Auth::user();
        if ($userObj->type == 'admin') {
            $users = User::where('type', '!=', 'admin')->get();

            return view('users.index', compact('users'));
        }
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace) {
            $doneStage = Stage::where('workspace_id', '=', $currentWorkspace->id)->where('complete', '=', '1')->first();
            // if (auth()->user()->hasRole('Ceo')) {

            //     $model_has_role = ModelHasRole::where('model_id',Auth::id())->first();

            //     $executives = $model_has_role->executives;
            //     $executives_id = json_decode($executives);


            //     $totalProject = UserProject::whereIn('user_id', $executives_id)->join("projects", "projects.id", "=", "user_projects.project_id")->count();
            //     $dueDateProjects = UserProject::whereIn('user_id', $executives_id)->join("projects", "projects.id", "=", "user_projects.project_id")->whereDate('end_date', '=', date("Y-m-d"))->count();
            //     $inProgressProjects = UserProject::whereIn('user_id', $executives_id)->join("projects", "projects.id", "=", "user_projects.project_id")->where('status', '=', 'Ongoing')->count();
            //     $dueDateTask = UserProject::whereIn('user_id', $executives_id)->join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->whereDate('due_date', '=', date("Y-m-d"))->count();
            //     $inProgressTask = UserProject::whereIn('user_id', $executives_id)->join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where('tasks.status','=','82')->count();

            //     $totalBugs = UserProject::join("bug_reports", "bug_reports.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('bug_reports.assign_to', '=', $userObj->id)->count();
            //     $totalTask = UserProject::whereIn('user_id', $executives_id)->join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->count();
            //     $completeTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")->where('tasks.status', '=', $doneStage->id)->count();


            //     $projects = Project::whereIn('user_id', $executives_id)->
            //     join("user_projects", "projects.id", "=", "user_projects.project_id")
            //     ->join("projects as p2", "p2.id", "=", "user_projects.project_id") // Change alias here

            //     ->orderBy('projects.id', 'desc')
            //     ->limit(5)
            //     ->get();
            //     // dd('ceo');

            //     $tasks = Task::select([
            //         'tasks.*',
            //         'stages.name as status',
            //         'stages.complete',
            //     ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->whereIn('user_id', $executives_id)->orderBy('tasks.id', 'desc')->limit(5)->get();

            //     $totalMembers = UserWorkspace::where('workspace_id', '=', $currentWorkspace->id)->count();
            //     $projectProcess = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->groupBy('projects.status')->selectRaw('count(projects.id) as count, projects.status')->pluck('count', 'projects.status');

            //     $arrProcessPer = [];
            //     $arrProcessLabel = [];
            //     foreach ($projectProcess as $lable => $process) {
            //         $arrProcessLabel[] = $lable;
            //         if ($totalProject == 0) {
            //             $arrProcessPer[] = 0.00;
            //         } else {
            //             $arrProcessPer[] = round(($process * 100) / $totalProject, 2);
            //         }
            //     }
            //     $arrProcessClass = [
            //         'text-success',
            //         'text-primary',
            //         'text-danger',
            //     ];
            //     $projectController = new ProjectReportController();

            //     $chartData = $projectController->getProjectChart([
            //         'workspace_id' => $currentWorkspace->id,
            //         'duration' => 'week',
            //     ]);

            //     // dd($chartData);


            //     return view('home', compact('currentWorkspace', 'totalProject', 'totalBugs', 'totalTask', 'totalMembers', 'arrProcessLabel', 'arrProcessPer', 'arrProcessClass', 'completeTask', 'tasks', 'chartData','inProgressProjects','dueDateProjects','dueDateTask','projects'));

            // }

            // if (auth()->user()->hasRole('HOD')) {

            //     $model_has_role = ModelHasRole::where('model_id',Auth::id())->first();

            //     $workspaces = $model_has_role->workspace_id;

            //     $workspace_id = json_decode($workspaces);


            //     // $totalProject = UserProject::whereIn('user_id', $executives_id)->join("projects", "projects.id", "=", "user_projects.project_id")->count();

            //     // $totalProjects = UserWorkspace::
            //     // whereIn('workspace_id', $workspace_id)
            //     // ->join('user_projects', 'user_projects.user_id', '=', 'user_workspaces.user_id')
            //     // ->count();

            //     $totalProject = Project::
            //     whereIn('workspace', $workspace_id)
            //     ->count();


            //     $dueDateProjects = Project::
            //     whereIn('workspace', $workspace_id)
            //     ->whereDate('end_date', '=', date("Y-m-d"))->count();

            //     $inProgressProjects = Project::
            //     whereIn('workspace', $workspace_id)
            //     ->where('status', '=', 'Ongoing')->count();

            //     $dueDateTask = UserWorkspace::whereIn('workspace_id', $workspace_id)
            //     ->join('user_projects', 'user_workspaces.user_id', '=', 'user_projects.user_id')
            //     ->join('projects', 'projects.id', '=', 'user_projects.project_id')
            //     ->join('tasks', 'tasks.project_id', '=', 'user_projects.project_id')
            //     ->whereDate('due_date', '=', date('Y-m-d'))
            //     ->count();

            //     // dd($dueDateTask);
            //     // $dueDateTask = UserProject::whereIn('user_id', $executives_id)->join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->whereDate('due_date', '=', date("Y-m-d"))->count();
            //     $inProgressTask = Task::whereIn('projects.workspace', $workspace_id)
            //     ->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
            //     ->join("projects", "projects.id", "=", "user_projects.project_id")
            //     ->where('tasks.status','=','82')->count();

            //     // dd($inProgressTask);
            //     // $totalBugs = UserProject::join("bug_reports", "bug_reports.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('bug_reports.assign_to', '=', $userObj->id)->count();
            //     $totalTask = Task::whereIn('projects.workspace', $workspace_id)
            //     ->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
            //     ->join("projects", "projects.id", "=", "user_projects.project_id")
            //     ->join("stages", "stages.id", "=", "tasks.status")->count();

            //     // dd($totalTask);
            //     $completeTask = Task::whereIn('projects.workspace', $workspace_id)
            //     ->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
            //     ->join("projects", "projects.id", "=", "user_projects.project_id")->where('tasks.status', '=', $doneStage->id)->count();



            //     $projects = Project::whereIn('workspace', $workspace_id)
            //     ->orderBy('projects.id', 'desc')
            //     ->limit(5)
            //     ->get();
            //     // dd('ceo');

            //     // $tasks = Task::select([
            //     //     'tasks.*',
            //     //     'stages.name as status',
            //     //     'stages.complete',
            //     // ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->orderBy('tasks.id', 'desc')->limit(5)->get();

            //     $tasks = Task::select([
            //         'tasks.*',
            //         'stages.name as status',
            //         'stages.complete',
            //     ])
            //     ->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
            //     ->join("projects", "projects.id", "=", "user_projects.project_id")
            //     ->join("stages", "stages.id", "=", "tasks.status")
            //     ->whereIn('projects.workspace', $workspace_id)
            //     ->orderBy('tasks.id', 'desc')
            //     ->limit(5)
            //     ->get();
            //     // dd($tasks);

            //     $totalMembers = UserWorkspace::where('workspace_id', '=', $currentWorkspace->id)->count();
            //     $projectProcess = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->whereIn('projects.workspace', $workspace_id)->groupBy('projects.status')->selectRaw('count(projects.id) as count, projects.status')->pluck('count', 'projects.status');

            //     $arrProcessPer = [];
            //     $arrProcessLabel = [];
            //     foreach ($projectProcess as $lable => $process) {
            //         $arrProcessLabel[] = $lable;
            //         if ($totalProject == 0) {
            //             $arrProcessPer[] = 0.00;
            //         } else {
            //             $arrProcessPer[] = round(($process * 100) / $totalProject, 2);
            //         }
            //     }
            //     $arrProcessClass = [
            //         'text-success',
            //         'text-primary',
            //         'text-danger',
            //     ];
            //     $projectController = new ProjectReportController();

            //     $chartData = $projectController->getProjectChart([
            //         'workspace_id' => $currentWorkspace->id,
            //         'duration' => 'week',
            //     ]);

            //     // dd($chartData);


            //     return view('home', compact('currentWorkspace', 'totalProject', 'totalTask', 'totalMembers', 'arrProcessLabel', 'arrProcessPer', 'arrProcessClass', 'completeTask', 'tasks', 'chartData','inProgressProjects','dueDateProjects','dueDateTask','projects'));

            // }

            // else {
            // dd(date("Y-m-d"));
            $check_home = 0;
            $workspace_type = WorkspaceType::get();
            $totalProject = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->count();
            $dueDateProjects = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->whereDate('end_date', '=', date("Y-m-d"))->count();
            $inProgressProjects = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('status', '=', 'Ongoing')->count();
            $overDueTasks = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->whereDate('tasks.due_date', '<=', date("Y-m-d"))->where('tasks.status', '!=', $doneStage->id)->count();
            $inProgressTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('tasks.status', '=', '82')->count();
            if ($currentWorkspace->permission == 'Owner') {

                $model_has_role = ModelHasRole::where('model_id', Auth::id())->first();

                // $executives = $model_has_role->executives;
                // $executives_id = json_decode($executives);


                // $executives = User::whereIn('id', $executives_id)->get();

                $totalBugs = UserProject::join("bug_reports", "bug_reports.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->count();
                $totalTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->count();
                $completeTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('tasks.status', '=', $doneStage->id)->count();
                // dd($doneStage->id);
                $tasks = Task::select([
                    'tasks.*',
                    'stages.name as status',
                    'stages.complete',
                ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->where("user_id", "=", $userObj->id)->orderBy('tasks.id', 'desc')->limit(5)->get();
                $taskStatistics = $tasks->groupBy('status')->map->count()->values();
                $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();

                // $firstArray = ['Todo', 'Review'];
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

                $model_has_role = ModelHasRole::where('model_id', Auth::id())->first();

                $workspaces = $model_has_role->workspace_id;
                $workspace_id = json_decode($workspaces);
                


                $hod_workspaces = Workspace::whereIn('id', $workspace_id)->get();

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

                $tasks = Task::select([
                    'tasks.*',
                    'stages.name as status',
                    'stages.complete',
                ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->where("user_id", "=", $userObj->id)->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")->orderBy('tasks.id', 'desc')->limit(5)->get();
                // $projects = '';
                $taskStatistics = $tasks->groupBy('status')->map->count()->values();
                $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();
                // $firstArray = ['Todo', 'Review'];
                $taskStatisticsColors = ['Todo' => '#008FFB', 'In Progress' => '#00E396', 'Review' => '#FEB019', 'Done' => '#FF4560'];
                $taskChartColor = array_intersect_key($taskStatisticsColors, array_flip($taskStatisticsKeys));
                $taskCounts = $tasks->groupBy('status')->map->count();
                $totalCount = $taskCounts->sum();
                $taskPercentages = $taskCounts->map(function ($count) use ($totalCount) {
                    return ($count / $totalCount) * 100;
                });
                // dd($taskStatistics = $tasks->groupBy('status')->map->count());
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
            // dd($chartData);
            // dd( $chartData['stages']);

            // $chartData = app('App\Http\Controllers\ProjectController')->getProjectChart([
            //     'workspace_id' => $currentWorkspace->id,
            //     'duration' => 'week',
            // ]);

            // $currentMonth = now()->startOfMonth();


            $MonthArr = [];
            $CompletedTaskArr = [];
            $PendingTaskArr = [];
            $CreatedTaskArr = [];


            // $report = DB::table('tasks')
            //     ->select(
            //         DB::raw('YEAR(created_at) as year'),
            //         // DB::raw('MONTH(created_at) as month'),
            //         DB::raw('DATE_FORMAT(created_at, "%b") as month'),

            //         DB::raw('SUM(CASE WHEN status = "16" THEN 1 ELSE 0 END) as total_completed_task'),
            //         DB::raw('SUM(CASE WHEN status = "10" THEN 1 ELSE 0 END) as total_pending_task'),
            //         DB::raw('COUNT(*) as total_created_task')

            //     )
            //     ->groupBy('month')->where('assign_to', $userObj->id)
            //     ->get();

            $report = Task::join('stages', 'tasks.status', '=', 'stages.id')
                // ->selectRaw('YEAR(tasks.created_at) as year')
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
            // dd($reportData);
            // dd($result['MonthArr'] , $result['PendingTaskArr']);
            // dd($MonthArr)->values();

            // dd($CompletedTaskArr->values());



            return view('home', compact(
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
                'hod_workspaces',
                'check_home'
                // 'executives'

            )
            );
            // }
        } else {
            return view('home', compact('currentWorkspace'));
        }
    }


    public function index_report($slug = '')
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
            if (auth()->user()->hasRole('Ceo')) {

                $model_has_role = ModelHasRole::where('model_id', Auth::id())->first();

                $executives = $model_has_role->executives;
                $executives_id = json_decode($executives);


                $executives = User::whereIn('id', $executives_id)->get();

                // dd($Executives);




                if (is_array($executives_id)) {
                    $totalProject = UserProject::whereIn('user_id', $executives_id)->join("projects", "projects.id", "=", "user_projects.project_id")->count();
                    $dueDateProjects = UserProject::whereIn('user_id', $executives_id)->join("projects", "projects.id", "=", "user_projects.project_id")->whereDate('end_date', '=', date("Y-m-d"))->count();
                    $inProgressProjects = UserProject::whereIn('user_id', $executives_id)->join("projects", "projects.id", "=", "user_projects.project_id")->where('status', '=', 'Ongoing')->count();
                    $overDueTasks = UserProject::whereIn('user_id', $executives_id)->join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->where('tasks.status', '!=', $doneStage->id)->count();
                    $inProgressTask = UserProject::whereIn('user_id', $executives_id)->join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where('tasks.status', '=', '14')->count();

                    $totalBugs = UserProject::join("bug_reports", "bug_reports.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('bug_reports.assign_to', '=', $userObj->id)->count();
                    $totalTask = UserProject::whereIn('user_id', $executives_id)->join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->count();
                    // dd($totalTask);

                    // dd($doneStage->id);
                    // $completeTask = UserProject::whereIn('user_id', $executives_id)->join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where('tasks.status', '=', $doneStage->id)->count();

                    $completeTask = UserProject::whereIn('user_id', $executives_id)
                        ->join('tasks', 'tasks.project_id', '=', 'user_projects.project_id')
                        ->join('projects', 'projects.id', '=', 'user_projects.project_id')
                        ->join('stages', 'tasks.status', '=', 'stages.id') // Join with the "stages" table
                        ->where('stages.id', '=', '12')
                        ->count();
                    // dd($completeTask);

                    $projects = Project::whereIn('user_id', $executives_id)->join("user_projects", "projects.id", "=", "user_projects.project_id")
                        ->join("projects as p2", "p2.id", "=", "user_projects.project_id") // Change alias here

                        ->orderBy('projects.id', 'desc')
                        ->limit(5)
                        ->get();
                    // dd('ceo');

                    $tasks = Task::select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->whereIn('user_id', $executives_id)->orderBy('tasks.id', 'desc')->limit(5)->get();

                    // dd($tasks);

                    $taskStatistics = $tasks->groupBy('status')->map->count()->values();
                    $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();
                    // $firstArray = ['Todo', 'Review'];
                    $taskStatisticsColors = ['Todo' => '#008FFB', 'In Progress' => '#00E396', 'Review' => '#FEB019', 'Done' => '#FF4560'];
                    $taskChartColor = array_intersect_key($taskStatisticsColors, array_flip($taskStatisticsKeys));
                    $taskCounts = $tasks->groupBy('status')->map->count();
                    $totalCount = $taskCounts->sum();
                    $taskPercentages = $taskCounts->map(function ($count) use ($totalCount) {
                        return ($count / $totalCount) * 100;
                    });


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


                    // $report = DB::table('tasks')
                    //     ->select(
                    //         DB::raw('YEAR(created_at) as year'),
                    //         // DB::raw('MONTH(created_at) as month'),
                    //         DB::raw('DATE_FORMAT(created_at, "%b") as month'),

                    //         DB::raw('SUM(CASE WHEN status = "16" THEN 1 ELSE 0 END) as total_completed_task'),
                    //         DB::raw('SUM(CASE WHEN status = "10" THEN 1 ELSE 0 END) as total_pending_task'),
                    //         DB::raw('COUNT(*) as total_created_task')

                    //     )
                    //     ->groupBy('month')->where('assign_to', $userObj->id)
                    //     ->get();


                    $report = Task::whereIn('assign_to', $executives_id)->join('stages', 'tasks.status', '=', 'stages.id')
                        // ->selectRaw('YEAR(tasks.created_at) as year')
                        ->selectRaw('DATE_FORMAT(tasks.created_at, "%b") as month')
                        ->selectRaw('SUM(CASE WHEN stages.name = "Done"  THEN 1 ELSE 0 END) as total_completed_task')
                        ->selectRaw('SUM(CASE WHEN stages.name = "In Progress"  THEN 1 ELSE 0 END) as total_pending_task')
                        ->selectRaw('COUNT(*) as total_created_task')
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

                    // dd($chartData);


                    return view('home', compact('currentWorkspace', 'totalProject', 'totalBugs', 'totalTask', 'totalMembers', 'arrProcessLabel', 'arrProcessPer', 'arrProcessClass', 'completeTask', 'tasks', 'chartData', 'inProgressProjects', 'dueDateProjects', 'overDueTasks', 'projects', 'inProgressTask', 'taskPercentages', 'taskStatistics', 'result', 'taskChartColor', 'taskStatisticsKeys'));
                } else {
                    return redirect()->back()->with('error', __("No Executive Found Under This CEO "));
                }
            }

            if (auth()->user()->hasRole('HOD')) {

                // dd('asd');
                $check_home = 1;
                $model_has_role = ModelHasRole::where('model_id', Auth::id())->first();
                $workspaces = $model_has_role->workspace_id;
                $workspace_id = json_decode($workspaces);

                // $hod_workspaces = Workspace::whereIn('id', $workspace_id)->get();

                $hod_workspaces = Workspace::select('workspaces.*', DB::raw('COUNT(tasks.id) as tasks_count'))
                ->whereIn('workspaces.id', $workspace_id)
                ->leftJoin('projects', 'workspaces.id', '=', 'projects.workspace')
                ->leftJoin('tasks', 'projects.id', '=', 'tasks.project_id')
                ->leftJoin('user_projects', 'projects.id', '=', 'user_projects.project_id')
                ->leftJoin('users', 'user_projects.user_id', '=', 'users.id')
                ->groupBy('workspaces.id')
                ->get();

                // dd($hod_workspaces);
                if (is_array($workspace_id)) {
                    $workspaces_model = Workspace::whereIn('id', $workspace_id)->get();
                    $totalTask = 0;
                    $completeTask = 0;
                    $overDueTasks = 0;
                    foreach ($workspaces_model as $workspace) {
                        //completed tasks
                        $doneStage = Stage::where('workspace_id', '=', $workspace->id)->where('complete', '=', '1')->first();
                        //   dd($doneStage);
                        $completeTask += $workspace->projects->flatMap(function ($project) use ($doneStage) {
                            return $project->task->where('status', $doneStage->id);
                        })->count();
                        //
                        $totalTask += $workspace->projects->flatMap(function ($project) {
                            return $project->task;
                        })->count();
                        $overDueTasks += $workspace->projects->flatMap(function ($project) use ($doneStage) {
                            return $project->task->where('due_date', '<=', date('Y-m-d') . ' 00:00:00')->where('status', '!=', $doneStage->id);
                        })->count();
                    }
                    $tasks = Task::with('project', 'stage')->select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("stages", "stages.id", "=", "tasks.status")
                        ->whereHas('project', function ($query) use ($workspace_id) {
                            $query->whereIn('workspace', $workspace_id);
                        })->orderBy('tasks.id', 'desc')->get();
                    $taskStatistics = $tasks->groupBy('status')->map->count()->values();
                    $taskStatisticsKeys = $tasks->groupBy('status')->map->count()->keys()->all();
                    $taskStatisticsColors = ['Todo' => '#008FFB', 'In Progress' => '#00E396', 'Review' => '#FEB019', 'Done' => '#FF4560'];
                    $taskChartColor = array_intersect_key($taskStatisticsColors, array_flip($taskStatisticsKeys));
                    $taskCounts = $tasks->groupBy('status')->map->count();
                    $totalCount = $taskCounts->sum();
                    $taskPercentages = $taskCounts->map(function ($count) use ($totalCount) {
                        return ($count / $totalCount) * 100;
                    });
                    //
                    $MonthArr = [];
                    $CompletedTaskArr = [];
                    $PendingTaskArr = [];
                    $CreatedTaskArr = [];
                    // $report = DB::table('tasks')
                    //     ->select(
                    //         DB::raw('YEAR(created_at) as year'),
                    //         // DB::raw('MONTH(created_at) as month'),
                    //         DB::raw('DATE_FORMAT(created_at, "%b") as month'),
                    //         DB::raw('SUM(CASE WHEN status = "16" THEN 1 ELSE 0 END) as total_completed_task'),
                    //         DB::raw('SUM(CASE WHEN status = "10" THEN 1 ELSE 0 END) as total_pending_task'),
                    //         DB::raw('COUNT(*) as total_created_task')
                    //     )
                    //     // ->where('created_at', '>=', $currentMonth)
                    //     ->groupBy('month')->where('assign_to', $userObj->id)
                    //     // ->orderBy('a', 'month')
                    //     ->get();

                    $report = DB::table('tasks')
                    ->join('projects', 'tasks.project_id', '=', 'projects.id')
                    ->select(
                        DB::raw('DATE_FORMAT(tasks.created_at, "%b") as month'),
                        DB::raw('SUM(CASE WHEN tasks.status = "16" THEN 1 ELSE 0 END) as total_completed_task'),
                        DB::raw('SUM(CASE WHEN tasks.status = "10" THEN 1 ELSE 0 END) as total_pending_task'),
                        DB::raw('COUNT(*) as total_created_task')
                    )
                  
                    // ->where('projects.workspace', $id)
                    ->whereIn('projects.workspace', $workspace_id)
                    ->groupBy('projects.workspace', 'month')
                    ->orderBy('projects.workspace')
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
                    //
                    $totalProject = Project::whereIn('workspace', $workspace_id)
                        ->count();
                    $dueDateProjects = Project::whereIn('workspace', $workspace_id)
                        ->whereDate('end_date', '=', date("Y-m-d"))->count();
                    $inProgressProjects = Project::whereIn('workspace', $workspace_id)
                        ->where('status', '=', 'Ongoing')->count();

                    $inProgressTask = Task::whereIn('projects.workspace', $workspace_id)
                        ->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
                        ->join("projects", "projects.id", "=", "user_projects.project_id")
                        ->where('tasks.status', '=', '82')->count();

                    $projects = Project::whereIn('workspace', $workspace_id)
                        ->orderBy('projects.id', 'desc')
                        ->limit(5)
                        ->get();
                    $totalMembers = UserWorkspace::where('workspace_id', '=', $currentWorkspace->id)->count();
                    $projectProcess = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->whereIn('projects.workspace', $workspace_id)->groupBy('projects.status')->selectRaw('count(projects.id) as count, projects.status')->pluck('count', 'projects.status');
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
                    return view('home', compact(
                        'taskChartColor',
                        'taskStatisticsKeys',
                        'currentWorkspace',
                        'totalProject',
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
                        'hod_workspaces',
                        'check_home'
                    )
                    );
                } else {
                    return redirect()->back()->with('error', __("No Department Found Under This HOD "));
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



            return view('home', compact(
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
        } else {
            return view('home', compact('currentWorkspace'));
        }
    }
}
