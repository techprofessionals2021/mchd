<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartUserRole extends Model
{
    use HasFactory;

    public function userWorkspaces() {
        return $this->belongsToMany(UserWorkspace::class, 'workspace_depart_role_pivots', 'depart_user_role_id', 'user_workspace_id');
    }
}
