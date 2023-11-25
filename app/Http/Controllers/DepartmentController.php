<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\DepartUserRole;
use App\Models\Department;
use App\Models\Workspace;

class DepartmentController extends Controller
{

    public function index()
    {

        $depart_user_role = DepartUserRole::get();

        return view('layouts.super-admin.depart-role.index',compact('depart_user_role'));


    }    


    public function store(Request $request)
    {

        $depart_user_role = DepartUserRole::where('name',$request->name)->first();

        if(isset($depart_user_role)){
            return redirect()->back()->withErrors(['error' => 'Depart Role Already Exists']);
        }

        $depart_user_role = new DepartUserRole;

        $depart_user_role->name = $request->name;
        $depart_user_role->slug = Str::slug($request->name);
        $depart_user_role->save();


        return redirect()->route('superadmin.depart.role');


    }   
    
    

    public function department_index()
    {

        $department = Department::get();

        $workspace = Workspace::get();




        return view('layouts.super-admin.department.index',compact('department','workspace'));


    }
    
    
    public function department_store(Request $request)
    {
   
        $department = Department::where('name',$request->name)->first();

        if(isset($department)){
            return redirect()->back()->withErrors(['error' => 'Department Already Exists']);
        }

        $department = new Department;

      
        $department->name = $request->name;
        $department->slug = Str::slug($request->name);
        $department->save();


        // $workspace = new Workspace;


        // $workspace->departments()->attach($department->id,['workspace_id' => $request->workspace_id]);


        return redirect()->route('superadmin.department');


    }   

}
