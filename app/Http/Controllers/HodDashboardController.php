<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\UserProject;
use App\Models\UserWorkspace;
use App\Models\Workspace;
use App\Models\PRoject;
use App\Models\WorkspacePermission;
use App\Models\Utility;
use App\Models\Stage;
use DB;
use Auth;


class HodDashboardController extends Controller
{
    public function index($slug = '')
    {

        $user = User::count();
        $workspace = Workspace::count();
        $task = Task::count();
        $project = Project::count();

        
        return view('layouts.hod.home',compact('user','workspace','task','project'));
    }


    public function workspace_report($id)
    {

        $userObj  = Auth::user();
        $workspace = Workspace::where('id',$id)->first();

  
        $currentWorkspace =  Workspace::where('id',$id)->first();

        // if (is_array($workspace_id)) {
            // $workspaces_model = Workspace::where('id', $id)->get();
            $totalTask = 0;
            $completeTask = 0;
            $overDueTasks = 0;
            // foreach ($workspaces_model as $workspace) {
                //completed tasks

                $doneStage = Stage::where('workspace_id', '=', $id)->where('complete', '=', '1')->first();
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
            // }

            // dd($overDueTasks);

            $tasks = Task::with('project', 'stage')->select([
                'tasks.*',
                'stages.name as status',
                'stages.complete',
            ])->join("stages", "stages.id", "=", "tasks.status")
                ->whereHas('project', function ($query) use ($id) {
                    $query->where('workspace', $id);
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
                ->join('projects', 'tasks.project_id', '=', 'projects.id') // Adjust the join condition based on your actual column names
                ->select(
                    DB::raw('DATE_FORMAT(tasks.created_at, "%b") as month'),
                    DB::raw('SUM(CASE WHEN tasks.status = "16" THEN 1 ELSE 0 END) as total_completed_task'),
                    DB::raw('SUM(CASE WHEN tasks.status = "10" THEN 1 ELSE 0 END) as total_pending_task'),
                    DB::raw('COUNT(*) as total_created_task')
                )
              
                ->where('projects.workspace', $id)
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
            $totalProject = Project::where('workspace', $id)
                ->count();
          
            // dd($totalProject);
            $dueDateProjects = Project::where('workspace', $id)
                ->whereDate('end_date', '=', date("Y-m-d"))->count();
            $inProgressProjects = Project::where('workspace', $id)
                ->where('status', '=', 'Ongoing')->count();

            $inProgressTask = Task::where('projects.workspace', $id)
                ->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
                ->join("projects", "projects.id", "=", "user_projects.project_id")
                ->where('tasks.status', '=', '82')->count();

            $projects = Project::where('workspace', $id)
                ->orderBy('projects.id', 'desc')
                ->limit(5)
                ->get();
            $totalMembers = UserWorkspace::where('workspace_id', '=',$id)->count();
            $projectProcess = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where('projects.workspace', $id)->groupBy('projects.status')->selectRaw('count(projects.id) as count, projects.status')->pluck('count', 'projects.status');
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
        
            // dd($projectController);
            // dd($chartData);
            return view('layouts.hod.workspace_report', compact(
                'taskChartColor',
                'taskStatisticsKeys',
                'totalProject',
                'totalTask',
                'totalMembers',
                'arrProcessLabel',
                'arrProcessPer',
                'arrProcessClass',
                'completeTask',
                'tasks',
                'inProgressProjects',
                'dueDateProjects',
                'inProgressTask',
                'overDueTasks',
                'projects',
                'taskStatistics',
                'result',
                'taskPercentages',
                'currentWorkspace'
            )
            );
        // } else {
        //     return redirect()->back()->with('error', __("No Department Found Under This HOD "));
        // }
    }


 
}
