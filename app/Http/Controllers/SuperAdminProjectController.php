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

class SuperAdminProjectController extends Controller
{
    public function edit($slug, $projectID)
    {



        // $currentWorkspace = Utility::getWorkspaceBySlug($slug);

      
        $objUser = Auth::user();
        $currentWorkspace = Workspace::with('users')->where('slug',$slug)->first();
    
        // dd($currentWorkspace);
  
        // dd($currentWorkspace->users);

        if(auth()->user()->type != 'super-admin'){
            $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
        }else{

            $project = Project::select('projects.*')
            // ->where('projects.workspace', '=', $currentWorkspace->id)
            ->where('projects.id', '=', $projectID)
            ->first();
            // dd($project->users->pluck('id')->toArray());
          
        }
        // dd(auth()->user()->type);
        // dd($project);
        $users = User::select('users.*')->join('user_projects', 'user_projects.user_id', '=', 'users.id')->where('project_id', '=', $project->id)->get();

        // dd($currentWorkspace->users);
        // dd($project->users->pluck('id'));
        return view('layouts.super-admin.project.edit', compact('currentWorkspace', 'project','users'));
    }

    public function update(Request $request, $slug, $projectID)
    {
        $request->validate(
            [
                'name' => 'required',
            ]
        );
        $objUser = Auth::user();
        $currentWorkspace = Workspace::with('users')->where('slug',$slug)->first();

        // $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
        $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
        // dd($project);
        $users = $request->users_list;
        // dd($users);
        $data = $request->all();
        $data['tags'] = Utility::convertTagsToJsonArray($request->tags);
        array_push($users,auth()->id());
        // dd($data);
        $project->update($data);

        $project->users()->syncWithPivotValues($users,['permission' => json_encode(Utility::getAllPermission())]);


        return redirect()->back()->with('success', __('Project Updated Successfully!'));
    }
}
