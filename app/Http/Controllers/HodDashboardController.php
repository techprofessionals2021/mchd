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


 
}
