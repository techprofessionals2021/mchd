<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    public function workspaces(){
        return $this->belongsToMany(Workspace::class,'workspace_depart_pivots');
    }
    public function users(){
        return $this->belongsToMany(User::class,'department_users');
    }
    public function projects(){
        return $this->hasMany(Project::class);
    }
}
