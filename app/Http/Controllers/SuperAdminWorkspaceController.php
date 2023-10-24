<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserWorkspace;
use App\Models\Workspace;
use App\Models\Project;
use App\Models\Task;

class SuperAdminWorkspaceController extends Controller
{
    public function workspace()
    {
        $workspace = Workspace::with('projects')->where('is_active','1')->get();


        return view('layouts.super-admin.workspace.index',compact('workspace'));
    }


    public function workspace_projects($workspace_id)
    {

        $project = Project::with('workspaceData')->where('workspace',$workspace_id)->get();

     

        // foreach ($workspace as $key => $value) {
        //    dd($value->projects);
        // }


        return view('layouts.super-admin.workspace.project',compact('project'));
    }



    public function workspace_tasks($project_id)
    {

    
        $task = Task::where('project_id',$project_id)->get();

      
        // foreach ($workspace as $key => $value) {
        //    dd($value->projects);
        // }


        return view('layouts.super-admin.workspace.task',compact('task','project_id'));
    }


    public function delete_workspace($id)
    {
        // dd($id);
        try {
            DB::beginTransaction();
        
            $workspace = Workspace::find($id);
            if (!$workspace) {
                // Handle the case where the workspace is not found.
                // You might want to throw an exception or return a response.
            }
        
            $workspace->is_active = 0;
            $workspace->save();
        
            $user_workspace = UserWorkspace::where('workspace_id', $workspace->id)->get();
        
            foreach ($user_workspace as $key => $value) {
                $value->is_active = 0;
                $value->save();
            }
        
            DB::commit(); // Commit the transaction if all operations were successful.
        } catch (\Exception $e) {
            DB::rollback(); // Rollback the transaction if an error occurred.
            // Handle the error, log it, or return a response.
        }
       
        

        return redirect()->route('superadmin.workspace');

    }
}
