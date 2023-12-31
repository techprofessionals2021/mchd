<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'priority',
        'description',
        'start_date',
        'due_date',
        'assign_to',
        'project_id',
        'milestone_id',
        'status',
        'order',
        'tags'
    ];

    public function getAssignedUsersAttribute()
    {
        $userIds = explode(',', $this->attributes['assign_to']);

        // Assuming your User model is in the 'App\Models' namespace
        return User::whereIn('id', $userIds)->get();
    }

    public function assignees(){
        return $this->belongsToMany(User::class,'task_users');
    }

    public function project()
    {
        return $this->hasOne('App\Models\Project', 'id', 'project_id');
    }

    public function users()
    {
        return User::whereIn('id',explode(',',$this->assign_to))->get();
    }
    public function filterTaskUsersByRoles($depart_id,$role_ids)
    {
        return User::whereIn('id',explode(',',$this->assign_to))->whereHas('departments',function($query)use(&$depart_id,&$role_ids){
            $query->where('department_id', $depart_id)
            ->whereIn('role_id', $role_ids);
        })
        ->get();
    }


    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'assign_to');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'task_id', 'id')->orderBy('id', 'DESC');
    }

    public function taskFiles()
    {
        return $this->hasMany('App\Models\TaskFile', 'task_id', 'id')->orderBy('id', 'DESC');
    }

    public function milestone()
    {
        return $this->milestone_id ? Milestone::find($this->milestone_id) : null;
    }

    public function sub_tasks()
    {
        return $this->hasMany('App\Models\SubTask', 'task_id', 'id')->orderBy('id', 'DESC');
    }

    public function taskCompleteSubTaskCount()
    {
        return $this->sub_tasks->where('status', '=', '1')->count();
    }

    public function taskTotalSubTaskCount()
    {
        return $this->sub_tasks->count();
    }

    public function subTaskPercentage()
    {
        $completedChecklist = $this->taskCompleteSubTaskCount();
        $allChecklist = max($this->taskTotalSubTaskCount(), 1);

        $percentageNumber = ceil(($completedChecklist / $allChecklist) * 100);
        $percentageNumber = $percentageNumber > 100 ? 100 : ($percentageNumber < 0 ? 0 : $percentageNumber);

        return (int) number_format($percentageNumber);
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'status');
    }

}
