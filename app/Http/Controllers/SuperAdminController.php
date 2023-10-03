<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ClientProject;
use App\Models\Stage;
use App\Models\Task;
use App\Models\User;
use App\Models\UserProject;
use App\Models\UserWorkspace;
use App\Models\Workspace;
use App\Models\WorkspacePermission;
use App\Models\Utility;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;


class SuperAdminController extends Controller
{
    public function index($slug = '')
    {

        $user = User::count();
        $workspace = Workspace::count();
        $task = Task::count();
        $project = Project::count();

        
        return view('layouts.super-admin.home',compact('user','workspace','task','project'));
    }


    public function workspace()
    {
        $workspace = Workspace::where('is_active','1')->get();

        return view('layouts.super-admin.workspace.index',compact('workspace'));
    }


    public function delete_workspace($id)
    {
        $workspace = Workspace::find($id);
        
        $workspace->is_active = 0;
        $workspace->save();

        return redirect()->route('superadmin.workspace');

    }


    public function delete_user($id)
    {
        $user = User::find($id);

        // dd($user);
        
        $user->delete();


        return redirect()->route('superadmin.workspace');

    }


    public function role()
    {

        $role = WorkspacePermission::get();


        return view('layouts.super-admin.role.index',compact('role'));

    }

    
    public function role_store(Request $request)
    {

        $check_workspace_permission = WorkspacePermission::where('role',$request->role)->first();

        if(isset($check_workspace_permission)){
            return redirect()->back()->withErrors(['error' => 'Role Already Exists']);
        }

        $default_permission = json_encode([

            "invite user",
            "create project",
            "show calendar",
            "show timesheet",
            "project report"
        
        ]);

        $role = New WorkspacePermission;

        $role->role = $request->role;
        $role->permission = $default_permission;
        $role->save();


        return redirect()->route('superadmin.role');

    }

    



    public function user()
    {
        $user = User::get();

        return view('layouts.super-admin.user.index',compact('user'));
    }

    public function project()
    {
        $project = Project::get();

        return view('layouts.super-admin.project.index',compact('project'));
    }

    public function task()
    {
        $task = Task::with('user')->get();

        return view('layouts.super-admin.task.index',compact('task'));
    }





}
