<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWorkspace extends Model
{
    protected $fillable = [
        'user_id','workspace_id','permission','workspace_permission','tags','is_active'
    ];
}
