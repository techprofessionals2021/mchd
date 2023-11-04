<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProject;
use App\Models\UserWorkspace;
use App\Models\Stage;
use App\Models\Task;
use App\Models\Project;
use App\Models\ModelHasRole;
use App\Models\Timesheet;
use App\Models\WorkspaceType;
use App\Models\Milestone;
use App\Models\ClientProject;
use App\Models\Utility;
use Auth;


class CeoDashboardController extends Controller
{
    public function executives($slug = '')
    {

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $model_has_role = ModelHasRole::where('model_id',Auth::id())->first();

        $executives = $model_has_role->executives;
        $executives_id = json_decode($executives);

        $users = User::whereIn('id', $executives_id)->get();

        // $user_project = UserProject::whereIn('user_id', $executives_id)->get();

        // dd($user_project);

        return view('layouts.ceo.executives', compact('currentWorkspace','users'));


    }    



    public function executive_report($id , $slug)
    {

        // dd($id);
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $user = User::where('id',$id)->first();

  

        if ($currentWorkspace) {
            $doneStage = Stage::where('workspace_id', '=', $currentWorkspace->id)->where('complete', '=', '1')->first();
  
                // dd(date("Y-m-d"));
                $workspace_type = WorkspaceType::get();
                $totalProject = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $id)->count();
                $dueDateProjects = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $id)->whereDate('end_date', '=', date("Y-m-d"))->count();
                $inProgressProjects = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $id)->where('status', '=', 'Ongoing')->count();
                $FinishedProjects = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $id)->where('status', '=', 'Finished')->count();

                $dueDateTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $id)->whereDate('due_date', '=', date("Y-m-d"))->count();
                $inProgressTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $id)->where('tasks.status','=','82')->count();
                if ($currentWorkspace->permission == 'Owner') {
                    $totalBugs = UserProject::join("bug_reports", "bug_reports.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $id)->count();
                    $totalTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $id)->count();
                    $completeTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $id)->where('tasks.status', '=', $doneStage->id)->count();
                    $tasks = Task::select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->where("user_id", "=", $id)->orderBy('tasks.id', 'desc')->limit(5)->get();

                    // dd($totalTask);
                    $projects = Project::
                    join("user_projects", "projects.id", "=", "user_projects.project_id")
                    ->join("projects as p2", "p2.id", "=", "user_projects.project_id") // Change alias here
                    ->where("user_id", "=", $id)
                    // ->where('p2.workspace', '=', $currentWorkspace->id) // Use the new alias here
                    ->orderBy('projects.id', 'desc')
                    ->limit(5)
                    ->get();
                    
                    // dd($projects);

                } else {
                    $totalBugs = UserProject::join("bug_reports", "bug_reports.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $id)->where('bug_reports.assign_to', '=', $id)->count();
                    $totalTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $id)->whereRaw("find_in_set('" . $id . "',tasks.assign_to)")->count();
      
                    $completeTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $id)->whereRaw("find_in_set('" . $id . "',tasks.assign_to)")->where('tasks.status', '=', $doneStage->id)->count();
                    $tasks = Task::select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->where("user_id", "=", $id)->orderBy('tasks.id', 'desc')->limit(5)->get();

                     
                }

                // $tasks = Task::select([
                //     'tasks.*',
                //     'stages.name as status',
                //     'stages.complete',
                // ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->where("assign_to", "=", $id)->orderBy('tasks.id', 'desc')->limit(5)->get();

                
                $projects = Project::
                join("user_projects", "projects.id", "=", "user_projects.project_id")
                ->join("projects as p2", "p2.id", "=", "user_projects.project_id") // Change alias here
                ->where("user_id", "=", $id)
                // ->where('p2.workspace', '=', $currentWorkspace->id) // Use the new alias here
                ->orderBy('projects.id', 'desc')
                ->limit(5)
                ->get();
                $totalMembers = UserWorkspace::where('workspace_id', '=', $currentWorkspace->id)->count();

                $projectProcess = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $id)->groupBy('projects.status')->selectRaw('count(projects.id) as count, projects.status')->pluck('count', 'projects.status');
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


                

                // dd( $chartData['stages']);

                // $chartData = app('App\Http\Controllers\ProjectController')->getProjectChart([
                //     'workspace_id' => $currentWorkspace->id,
                //     'duration' => 'week',
                // ]);

           

        
                $ongoingProjectCount = UserProject::where('user_id', $id)
                ->whereHas('projects', function ($query) {
                    $query->where('status', 'Ongoing');
                })
                ->count();

                $finishedProjectCount = UserProject::where('user_id', $id)
                ->whereHas('projects', function ($query) {
                    $query->where('status', 'Finished');
                })
                ->count();

                $ongoingProjectCountArray = [
                    'count' => $ongoingProjectCount
                ];

                $finishedProjectCountArray = [
                    'count' => $finishedProjectCount
                ];

                $ongoingProjectCount = array_values($ongoingProjectCountArray);
                $finishedProjectCount = array_values($finishedProjectCountArray);

            
                // dd($ongoingProjectCount);
                // foreach ($workspace as $w) {
                //     $projectCount = $project->where('workspace', $w->id)->count();
                //     $ongoingProjectCount = $project->where('workspace', $w->id)->where('status', 'Ongoing')->count();
                //     $finishedProjectCount = $project->where('workspace', $w->id)->where('status', 'Finished')->count();
                //     $arrTask['total_projects'][] = $projectCount;
                //     $arrTask['total_ongoing_projects'][] = $ongoingProjectCount;
                //     $arrTask['total_finished_projects'][] = $finishedProjectCount;
                // }
                
                // $arrTask['total_projects'] = array_values($arrTask['total_projects']);
                // $arrTask['total_ongoing_projects'] = array_values($arrTask['total_ongoing_projects']);
                // $arrTask['total_finished_projects'] = array_values($arrTask['total_finished_projects']);

                // dd($arrTask['total_ongoing_projects']);
            
                return view('layouts.ceo.executive_report_detail', compact('currentWorkspace',
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
                'projects',
                'user',
                'FinishedProjects',
                'ongoingProjectCount',
                'finishedProjectCount'
                

            ));
         
        }

     

        // dd($currentWorkspace);

        // return view('layouts.ceo.executive_report_detail', compact('currentWorkspace','user','arrProcessPer','projectProcess','chartData','arrProcessLabel'));


    }    
}
