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
}
