<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProject extends Model
{
    protected $fillable = [
        'user_id','project_id','is_active','permission'
    ];

    public function projects()
    {
        return $this->hasMany(Project::class, 'id', 'project_id');
    }
}
