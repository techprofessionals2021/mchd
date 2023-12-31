<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class SuperAdminPermissionController extends Controller
{
    public function index()
    {


        $permissions = Permission::all();

        return view('layouts.super-admin.permission.index',compact('permissions'));
    }


    public function store(Request $request)
    {
        $permissions = new Permission;
        $permissions->name = Str::slug($request->name);
        $permissions->guard_name = 'web';
        $permissions->save();
       

        return redirect()->route('superadmin.permission.index')->with('success', 'Permission Added Successfully.');

        // Redirect or return a response
    }


    public function delete($id)
    {
        $permission = Permission::find($id);

        
        $permission->delete();


        return redirect()->route('superadmin.permission.index')->with('success', 'Permission Deleted Successfully.');

        // Redirect or return a response
    }


    public function update_is_active(Permission $permission)
    {
        // dd($permission->id);
       
        $permission->update(['is_active' => !$permission->is_active]);

        return response()->json(['message' => 'is_active attribute toggled successfully']);
    }
}
