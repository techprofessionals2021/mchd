<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utility;
use Auth;
use App\Models\UserWorkspace;
use App\Models\Workspace;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class SuperAdminTaskController extends Controller
{
    public function taskEdit($slug, $projectID, $taskId)
    {
        $objUser = Auth::user();

        $currentWorkspace = Workspace::with('users')->where('slug',$slug)->first();

     
        if ($objUser->getGuard() == 'client') {
            $project = Project::select('projects.*')->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            $projects = Project::select('projects.*')->join('client_projects', 'client_projects.project_id', '=', 'projects.id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->get();
        } else {
            // $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            $project = Project::select('projects.*')->where('projects.id', '=', $projectID)->first();
            // $projects = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->get();

            $projects = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->get();
   
            // dd( $projects);
    
        }
        $users = User::select('users.*')->join('user_projects', 'user_projects.user_id', '=', 'users.id')->where('project_id', '=', $projectID)->get();
        $task = Task::find($taskId);
     
        $task->assign_to = explode(",", $task->assign_to);
     
     
        return view('layouts.super-admin.task.edit', compact('currentWorkspace', 'project', 'projects', 'users', 'task'));
    }

    public function taskUpdate(Request $request, $slug, $projectID, $taskID)
    {

        // $request->validate(
        //     [
        //         'project_id' => 'required',
        //         'title' => 'required',
        //         'priority' => 'required',
        //         'assign_to' => 'required',
        //         'start_date' => 'required',
        //         'due_date' => 'required',
        //     ]
        // );

        
        // dd('asd');
        $objUser = Auth::user();
        $currentWorkspace = Workspace::with('users')->where('slug',$slug)->first();

        if ($objUser->getGuard() == 'client') {
            $project = Project::where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
        } else {
            // $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $request->project_id)->first();
            $project = Project::select('projects.*')->where('projects.id', '=', $projectID)->first();
        }

        // dd($project);
        if ($project) {
            $post = $request->all();
        
            $post['assign_to'] = implode(",", $request->assign_to);
            $post['tags'] = Utility::convertTagsToJsonArray($request->tags);
            $task = Task::find($taskID);
            $task->update($post);

            return redirect()->back()->with('success', __('Task Updated Successfully!'));
        } else {
            return redirect()->back()->with('error', __("You can't Edit Task!"));
        }
    }
}
