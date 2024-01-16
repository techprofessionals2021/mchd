<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserWorkspace;
use App\Models\Workspace;
use App\Models\Project;
use App\Models\Task;
use DB;

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
        // dd($id);
        try {
            DB::beginTransaction();

            $workspace = Workspace::find($id);
    
            $workspace->delete();


            // $user_workspace = UserWorkspace::where('workspace_id', $workspace->id)->get();

            // foreach ($user_workspace as $key => $value) {
            //     $value->is_active = 0;
            //     $value->save();
            // }

            DB::commit(); // Commit the transaction if all operations were successful.
        } catch (\Exception $e) {
            DB::rollback(); // Rollback the transaction if an error occurred.
            // Handle the error, log it, or return a response.
        }



        return redirect()->route('superadmin.workspace');

    }

    public function selectDefaultWorkspace($workspace_id){
      $workspace = WorkSpace::find($workspace_id);

      if (isset($workspace)) {
       $allWorkspaces =  workspace::where('is_default',1)->get();
       foreach ($allWorkspaces as $key => $singleWorkspace) {
            $singleWorkspace->is_default = 0;
            $singleWorkspace->save();
       }
        $workspace->is_default = 1;
        $workspace->save();
      }else {
        return redirect()->back()->with('error','No Workspace found');
      }
      return redirect()->back()->with('success','Workspace selected successfully');
    }
}
