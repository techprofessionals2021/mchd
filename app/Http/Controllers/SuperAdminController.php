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
        $workspace = Workspace::get();

        return view('layouts.super-admin.workspace.index',compact('workspace'));
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
