<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWorkspace extends Model
{
    protected $fillable = [
        'user_id','workspace_id','permission','workspace_permission','tags','is_active'
    ];


    public function departUserRoles() {
        return $this->belongsToMany(DepartUserRole::class, 'workspace_depart_role_pivots', 'user_workspace_id', 'depart_user_role_id');
    }
}
