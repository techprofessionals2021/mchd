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
use Spatie\Permission\Models\Role;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



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


    public function delete_user($id)
    {
        $user = User::find($id);
        
        $user->delete();


        return redirect()->route('superadmin.workspace');

    }


    public function role()
    {

        $role = Role::get();


        return view('layouts.super-admin.role.index',compact('role'));

    }

    
    public function role_store(Request $request)
    {

        $role = Role::where('name',$request->name)->first();

        if(isset($role)){
            return redirect()->back()->withErrors(['error' => 'Role Already Exists']);
        }

        
        $role = New Role;

        $role->name = $request->name;
        $role->guard_name = 'web';
        $role->save();


        return redirect()->route('superadmin.role');

    }

    



    public function user()
    {
        $user = User::get();

        $role = Role::all();

        return view('layouts.super-admin.user.index',compact('user','role'));
    }


    public function update_user(Request $request)
    {

        $tags = Utility::convertTagsToJsonArray($request->tags);
 

       
        $user = User::find($request->user_id);
       
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();




        $roleName = $request->role;

        // Check if the user already has the role
        $existingRole = $user->roles()->where('name', $roleName)->first();
        
        if (isset($existingRole)) {
    
            return redirect()->route('superadmin.user')->with('error', 'The User Already Has this role.');
            // The user already has this role; no need to update or insert.
        } else {
            // The user does not have this role, so assign it.
            $role = Role::where('name', $roleName)->first();
        
            if (isset($role)) {
              
                // The role exists; assign it to the user.
                $user->assignRole($role);

                $user->roles()->updateExistingPivot($role->id, ['tag' => $tags]);
                
            } else {
                // The role doesn't exist; create it and then assign it to the user.
                $role = Role::create(['name' => $roleName]);
                $user->assignRole($role);
            }
        }
        
        // dd($role);

      
        return redirect()->route('superadmin.user')->with('success', 'User Updated Successfully and Role Assigned.');

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
