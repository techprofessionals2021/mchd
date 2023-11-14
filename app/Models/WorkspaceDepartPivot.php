<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceDepartPivot extends Model
{
    use HasFactory;


    public function departments()
    {
        return $this->belongsToMany(Department::class, 'workspace_depart_pivots', 'id', 'department_id');
    }


}
