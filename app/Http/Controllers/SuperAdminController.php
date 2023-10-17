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
use Spatie\Permission\Models\Permission;
use App\Models\RolehasPermission;
use App\Models\Project;
use App\Models\ModelHasRole;
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


    public function delete_user($id)
    {
        $user = User::find($id);
        
        $user->delete();


        return redirect()->route('superadmin.workspace');

    }


    public function role()
    {

        $role = Role::with('permissions')->get();

        // dd($role);

        $permission = Permission::get();

        // $role_has_permission = RolehasPermission::get();

        // dd($role_has_permission);


        return view('layouts.super-admin.role.index',compact('role','permission'));

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


       
    public function assign_permission(Request $request)
    {

        $roleId = $request->input('role_id');
        $permissionIds = $request->input('permissions');

        // Get the role and permissions models
        $role = Role::find($roleId);
        $permissions = Permission::find($permissionIds);
    
        // Attach permissions to the role
        $role->permissions()->sync($permissions);
    


        return redirect()->route('superadmin.role')->with('success', 'Permission Assigned Successfully.');

    }


    
    public function get_permission_by_role($role_id)
    {
        // Retrieve permissions based on the provided role_id
        $permissions = Permission::whereHas('roles', function ($query) use ($role_id) {
            $query->where('role_id', $role_id);
        })->get();

        // You can return the permissions as JSON or any other format you prefer
        return response()->json($permissions);
    }

    



    public function user()
    {
        // $userId = $request->query('user_id');
        // dd($userId);
        // dd($request->user_id);
        $user = User::where('type','!=','admin')->where('type','!=','super-admin')->get();

        // dd($user);

        $role = Role::all();

        $workspace = Workspace::where('is_active','1')->get();

        $hodRole = Role::where('name', 'hod')->first();

        $executiveRole = Role::where('name', 'executive')->first();

        $hodUsers = User::role($hodRole)->get();

        // dd($hodUsers);

        $executiveUsers = User::role($executiveRole)->get();
        // dd($hodUsers);

        // dd($workspace);

        return view('layouts.super-admin.user.index',compact('user','role','workspace','hodUsers','executiveUsers'));
    }


  
    public function get_user_role($id)
    {
        $user = User::where('id', $id)->first(); // Retrieve a single user by their ID

        // dd($user);

        $roles = $user->roles;

        $firstRole = $roles->first();

        // dd($firstRole);

        $model_has_role = ModelHasRole::where('role_id',$firstRole->id)->where('model_id',$id)->first();


        $hodRole = Role::where('name', 'hod')->first();

        $executiveRole = Role::where('name', 'executive')->first();

        $hodUsers = User::role($hodRole)->where('id','!=',$id)->get();

    

        $executiveUsers = User::role($executiveRole)->where('id','!=',$id)->get();

        
        // dd($executiveUsers);

        // dd($model_has_role);

        $data = [
            'user' => $user,
            'role' => $firstRole,
            'model_has_role' => $model_has_role,
            'hodUsers' => $hodUsers,
            'executiveUsers' => $executiveUsers,
         
        ];
        
        // dd($firstRole);

        return response()->json($data);
    }

    public function update_user(Request $request)
    {

        $tags = Utility::convertTagsToJsonArray($request->tags);
        $hods = json_encode($request->hods);
        $executives = json_encode($request->executives);


        // dd( $tags , $hods , $executives);

 

       
        $user = User::find($request->user_id);
       
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();




        $roleName = $request->role;

        // Check if the user already has the role
        $model_role  = ModelHasRole::where('model_id',$request->user_id)->delete();
        
        if (isset($existingRole)) {
           
            $user->assignRole($role);

            $user->roles()->updateExistingPivot($role->id, ['tag' => $tags,'workspace_id' => $request->workspace_id,'hods' => $hods,'executives' => $executives]);
    
        
        } else {
      
            $role = Role::where('name', $roleName)->first();
        
            if (isset($role)) {
              
                // The role exists; assign it to the user.
                $user->assignRole($role);

                $user->roles()->updateExistingPivot($role->id, ['tag' => $tags,'workspace_id' => $request->workspace_id,'hods' => $hods,'executives' => $executives]);
                
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
