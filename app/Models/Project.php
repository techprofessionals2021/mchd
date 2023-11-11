<?php

namespace App\Models;

use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Project extends Model
{
    protected $fillable = [
        'name',
        'status',
        'description',
        'start_date',
        'end_date',
        'tags',
        'budget',
        'workspace',
        'department_id',
        'created_by',
        'is_active',
    ];

    public function creater()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }

    public function workspaceData()
    {
        return $this->hasOne('App\Models\Workspace', 'id', 'workspace');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_projects', 'project_id', 'user_id')->withPivot('is_active','permission')->orderBy('id', 'ASC')->withTimestamps();
    }

    public function task()
    {

            return $this->hasMany('App\Models\Task', 'project_id', 'id');

    }

    public function clientids()
    {
        return $this->belongsToMany('App\Models\Client', 'client_projects', 'project_id', 'client_id')->withPivot('is_active')->pluck('client_id');
    }

     public function clients()
    {
        return $this->belongsToMany('App\Models\Client', 'client_projects', 'project_id', 'client_id')->withPivot('is_active')->orderBy('id', 'ASC');
    }


    public function countTask()
    {
        return Task::where('project_id', '=', $this->id)->count();
    }

    public function tasks()
    {
        return Task::where('project_id', '=', $this->id)->get();
    }

    public function user_tasks($user_id){
        return Task::where('project_id','=',$this->id)->where('assign_to','=',$user_id)->get();
    }

    public function custom_user_tasks($searchQuery = '',$currentStatus='All'){
        $user_id = auth()->id();
        if ($currentStatus == 'All') {
            $tasks =  Task::with('project')
            ->where('project_id', $this->id) // Necessary condition
            ->where('title','LIKE',"%{$searchQuery}%")
            ->where(function ($query) use ($user_id) {
                $query->whereRaw('FIND_IN_SET(?, assign_to)', [$user_id])
                      ->orWhereHas('project', function ($subQuery) use ($user_id) {
                          $subQuery->where('created_by', $user_id);
                      });
            })
            ->get();
        }else{
            $tasks =  Task::with('project','stage')
            ->where('project_id', $this->id) // Necessary condition
            ->where('title','LIKE',"%{$searchQuery}%")
            ->where(function ($query) use ($user_id) {
                $query->whereRaw('FIND_IN_SET(?, assign_to)', [$user_id])
                      ->orWhereHas('project', function ($subQuery) use ($user_id) {
                          $subQuery->where('created_by', $user_id);
                      });
            })->whereHas('stage',function($query) use($currentStatus){
                $query->where('name',$currentStatus);
            })
            ->get();
        }

        return TaskResource::collection($tasks);

    }

    public function user_done_tasks($user_id){
        return Task::join('stages','stages.id','=','tasks.status')->where('project_id','=',$this->id)->where('assign_to','=',$user_id)->where('stages.complete','=','1')->get();
    }

    public function timesheet()
    {
        return Timesheet::where('project_id', '=', $this->id)->get();
    }


    public function countTaskComments()
    {
        return Task::join('comments', 'comments.task_id', '=', 'tasks.id')->where('project_id', '=', $this->id)->count();
    }

    public function getProgress()
    {

        $total     = Task::where('project_id', '=', $this->id)->count();
        $totalDone = Task::where('project_id', '=', $this->id)->where('status', '=', 'done')->count();
        if($totalDone == 0)
        {
            return 0;
        }

        return round(($totalDone * 100) / $total);
    }

    public function milestones()
    {
        return $this->hasMany('App\Models\Milestone', 'project_id', 'id');
    }

    public function files()
    {
        return $this->hasMany('App\Models\ProjectFile', 'project_id', 'id');
    }

    public function activities()
    {
        return $this->hasMany('App\Models\ActivityLog', 'project_id', 'id')->orderBy('id', 'desc');
    }

    public static function getProjectAssignedTimesheetHTML($currentWorkspace, $timesheets = [], $days = [], $project_id = null, $seeAsOwner = false)
    {
        $project = Project::find($project_id);
        $permission = '';

        if(Auth::user() != null){
            $objUser         = Auth::user();
        }else{
            $objUser         = User::where('id',$project->created_by)->first();
        }

        if($objUser->getGuard() != 'client') {

            $permission =  UserWorkspace::where('user_id', $objUser->id)->where('workspace_id', $objUser->currant_workspace)->first()->permission;

        }

        if($permission == 'Sub Owner')
        {
            $user_id = $currentWorkspace->created_by;
        }
        else
        {
            $user_id = $objUser->id;
        }


        $i              = $k = 0;
        $allProjects    = false;
        $timesheetArray = $totaltaskdatetimes = [];

        if($project_id == '-1')
        {
            $allProjects = true;


            if($objUser->getGuard() == 'client') {
                $project_timesheets = Timesheet::select('timesheets.*')->join('projects', 'projects.id', '=', 'timesheets.project_id')->join('tasks', 'tasks.id', '=', 'timesheets.task_id')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('client_projects.permission','LIKE','%show timesheet%');
            } elseif ($currentWorkspace->permission == 'Owner') {
                $project_timesheets = Timesheet::select('timesheets.*')->join('projects', 'projects.id', '=', 'timesheets.project_id')->join('tasks', 'tasks.id', '=', 'timesheets.task_id')->where('projects.workspace', '=', $currentWorkspace->id);
            } else {
                $project_timesheets = Timesheet::select('timesheets.*')->join('projects', 'projects.id', '=', 'timesheets.project_id')->join('tasks', 'timesheets.task_id', '=', 'tasks.id')->where('projects.workspace', '=', $currentWorkspace->id)->whereRaw("find_in_set('".$objUser->id."',tasks.assign_to)");
            }

            foreach($timesheets as $project_id => $timesheet)
            {
                $project = Project::find($project_id);

                if($project)
                {
                    $timesheetArray[$k]['project_id']   = $project->id;
                    $timesheetArray[$k]['project_name'] = $project->name;
                    foreach($timesheet as $task_id => $tasktimesheet)
                    {
                        $task = Task::find($task_id);

                        if($task)
                        {
                            $timesheetArray[$k]['taskArray'][$i]['task_id']   = $task->id;
                            $timesheetArray[$k]['taskArray'][$i]['task_name'] = $task->title;

                            $new_projects_timesheet = clone $project_timesheets;

                            // $users = $new_projects_timesheet->where('timesheets.task_id', $task->id)->pluck('created_by')->toArray();

                               $users = $new_projects_timesheet->where('timesheets.task_id', $task->id)->groupBy('timesheets.created_by')->pluck('created_by')->toArray();

                            foreach($users as $count => $user_id)
                            {
                                $times = [];

                                for($j = 0; $j < 7; $j++)
                                {
                                    $date = $days['datePeriod'][$j]->format('Y-m-d');

                                    $filtered_array = array_filter(
                                        $tasktimesheet, function ($val) use ($user_id, $date){
                                        return ($val['created_by'] == $user_id and $val['date'] == $date);
                                    }
                                    );
                                    $key            = array_keys($filtered_array);

                                    $user = User::find($user_id);

                                    $timesheetArray[$k]['taskArray'][$i]['dateArray'][$count]['user_id']          = $user != null ? $user->id : '';
                                    $timesheetArray[$k]['taskArray'][$i]['dateArray'][$count]['user_name']        = $user != null ? $user->name : '';
                                    $timesheetArray[$k]['taskArray'][$i]['dateArray'][$count]['week'][$j]['date'] = $date;

                                    if(!empty($key) && count($key) > 0)
                                    {
                                        $time    = Carbon::parse($tasktimesheet[$key[0]]['time'])->format('H:i');
                                        $times[] = $time;

                                        $timesheetArray[$k]['taskArray'][$i]['dateArray'][$count]['week'][$j]['time'] = $time;
                                        $timesheetArray[$k]['taskArray'][$i]['dateArray'][$count]['week'][$j]['type'] = 'edit';
                                        $timesheetArray[$k]['taskArray'][$i]['dateArray'][$count]['week'][$j]['url']  = route( 'project.timesheet.edit', [
                                                            'slug' => $currentWorkspace->slug,
                                                            'timesheet_id' => $tasktimesheet[$key[0]]['id'],
                                                            'project_id' => $project_id
                                                        ] );
                                    }
                                    else
                                    {
                                        $timesheetArray[$k]['taskArray'][$i]['dateArray'][$count]['week'][$j]['time'] = '00:00';
                                        $timesheetArray[$k]['taskArray'][$i]['dateArray'][$count]['week'][$j]['type'] = 'create';
                                        $timesheetArray[$k]['taskArray'][$i]['dateArray'][$count]['week'][$j]['url']  = route('project.timesheet.create', ['slug' => $currentWorkspace->slug, 'project_id' => $project_id]);
                                    }
                                }

                                $calculatedtasktime                                                    = Utility::calculateTimesheetHours($times);
                                $totaltaskdatetimes[]                                                  = $calculatedtasktime;
                                $timesheetArray[$k]['taskArray'][$i]['dateArray'][$count]['totaltime'] = $calculatedtasktime;
                            }
                        }
                        $i++;
                    }
                }
                $k++;
            }
        }
        else
        {
            foreach($timesheets as $task_id => $timesheet)
            {

                $times = [];
                $task  = Task::find($task_id);

                if($task)
                {

                    $timesheetArray[$i]['task_id']   = $task->id;
                    $timesheetArray[$i]['task_name'] = $task->title;

                    for($j = 0; $j < 7; $j++)
                    {
                        $date = $days['datePeriod'][$j]->format('Y-m-d');

                        $filtered_array = array_filter($timesheet, function ($val) use ($user_id, $date){
                            return ($val['created_by'] == $user_id and $val['date'] == $date);
                        });
                        $key            = array_keys($filtered_array);

                        // $key  = array_search($date, array_column($timesheet, 'date'));

                        $timesheetArray[$i]['dateArray'][$j]['date'] = $date;

                        if($key !== false && count($key) > 0)
                        {

                            $time    = Carbon::parse($timesheet[$key[0]]['time'])->format('H:i');
                            $times[] = $time;

                            foreach($timesheet as $timesheets){

                                if(($date == $timesheets['date']) && ($timesheets['project_id'] == $project_id) && ($timesheets['task_id'] == $task_id) ){
                                    $total_task_time    = Carbon::parse($timesheets['time'])->format('H:i');
                                    $total_task_times[] = $total_task_time;
                                }
                            }

                            $total_task_time              = Utility::calculateTimesheetHours($total_task_times);

                            $timesheetArray[$i]['dateArray'][$j]['total_task_time'] = $total_task_time;
                            $timesheetArray[$i]['dateArray'][$j]['time'] = $time;
                            $timesheetArray[$i]['dateArray'][$j]['type'] = 'edit';
                            $timesheetArray[$i]['dateArray'][$j]['url']  =
                            route( 'project.timesheet.edit', [
                                                            'slug' => $currentWorkspace->slug,
                                                            'timesheet_id' => $timesheet[$key[0]]['id'],
                                                            'project_id' => $project_id
                                                        ] );

                        }
                        else
                        {
                            $timesheetArray[$i]['dateArray'][$j]['time'] = '00:00';
                            $timesheetArray[$i]['dateArray'][$j]['type'] = 'create';
                            $timesheetArray[$i]['dateArray'][$j]['url']  = route('project.timesheet.create', ['slug' => $currentWorkspace->slug, 'project_id' => $project_id]);
                        }
                    }
                    $calculatedtasktime              = Utility::calculateTimesheetHours($times);
                    $totaltaskdatetimes[]            = $calculatedtasktime;
                    $timesheetArray[$i]['totaltime'] = $calculatedtasktime;

                }
                $i++;
            }
            // foreach($timesheets as $task_id => $timesheet)
            // {

            //     $times = [];
            //     $task  = Task::find($task_id);

            //     if($task)
            //     {

            //         $timesheetArray[$i]['task_id']   = $task->id;
            //         $timesheetArray[$i]['task_name'] = $task->title;

            //         for($j = 0; $j < 7; $j++)
            //         {
            //             $date = $days['datePeriod'][$j]->format('Y-m-d');
            //             $key  = array_search($date, array_column($timesheet, 'date'));

            //             $timesheetArray[$i]['dateArray'][$j]['date'] = $date;

            //             if($key !== false)
            //             {

            //                 $time    = Carbon::parse($timesheet[$key]['time'])->format('H:i');
            //                 $times[] = $time;

            //                 $timesheetArray[$i]['dateArray'][$j]['time'] = $time;
            //                 $timesheetArray[$i]['dateArray'][$j]['type'] = 'edit';
            //                 $timesheetArray[$i]['dateArray'][$j]['url']  =
            //                 route( 'project.timesheet.edit', [
            //                                                 'slug' => $currentWorkspace->slug,
            //                                                 'timesheet_id' => $timesheet[$key]['id'],
            //                                                 'project_id' => $project_id
            //                                             ] );
            //             }
            //             else
            //             {
            //                 $timesheetArray[$i]['dateArray'][$j]['time'] = '00:00';
            //                 $timesheetArray[$i]['dateArray'][$j]['type'] = 'create';
            //                 $timesheetArray[$i]['dateArray'][$j]['url']  = route('project.timesheet.create', ['slug' => $currentWorkspace->slug, 'project_id' => $project_id]);
            //             }
            //         }
            //         $calculatedtasktime              = Utility::calculateTimesheetHours($times);
            //         $totaltaskdatetimes[]            = $calculatedtasktime;
            //         $timesheetArray[$i]['totaltime'] = $calculatedtasktime;
            //     }
            //     $i++;
            // }

        }


        $calculatedtotaltaskdatetime = Utility::calculateTimesheetHours($totaltaskdatetimes);

        foreach($days['datePeriod'] as $key => $date)
        {

            $dateperioddate = $date->format('Y-m-d');

            if($objUser->getGuard() == 'client') {
                $new_projects_timesheet = Timesheet::select('timesheets.*')->join('projects', 'projects.id', '=', 'timesheets.project_id')->join('tasks', 'tasks.id', '=', 'timesheets.task_id')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('client_projects.permission','LIKE','%show timesheet%');
            } elseif ($currentWorkspace->permission == 'Owner') {
                $new_projects_timesheet = Timesheet::select('timesheets.*')->join('projects', 'projects.id', '=', 'timesheets.project_id')->join('tasks', 'tasks.id', '=', 'timesheets.task_id')->where('projects.workspace', '=', $currentWorkspace->id);
            } else {
                $new_projects_timesheet = Timesheet::select('timesheets.*')->join('projects', 'projects.id', '=', 'timesheets.project_id')->join('tasks', 'timesheets.task_id', '=', 'tasks.id')->where('projects.workspace', '=', $currentWorkspace->id)->whereRaw("find_in_set('".$objUser->id."',tasks.assign_to)");
            }

            $totalDateTimes[$dateperioddate] = Utility::calculateTimesheetHours($new_projects_timesheet->where('date', $dateperioddate)->pluck('time')->toArray());
        }

        $returnHTML = view('projects.timesheet-week', compact('currentWorkspace' , 'timesheetArray', 'totalDateTimes', 'calculatedtotaltaskdatetime', 'days', 'seeAsOwner', 'allProjects'))->render();

        return $returnHTML;
    }



    public function project_progress()
    {
          $total_task     = Task::where('project_id', '=', $this->id)->count();
            $completed_task =  Task::where('project_id', '=', $this->id)->where('status', '=', 4)->count();

            if($total_task > 0)
            {
                $percentage = intval(($completed_task/$total_task) * 100);


            return [

            'percentage' => $percentage . '%',
                   ];
          }
          else{
             return [

            'percentage' => 0,
                   ];

          }
    }



       public function project_milestone_progress()
    {
            $total_milestone     = Milestone::where('project_id', '=', $this->id)->count();
            $total_progress_sum  = Milestone::where('project_id', '=', $this->id)->sum('progress');

            if($total_milestone > 0)
            {
                $percentage = intval(($total_progress_sum /$total_milestone));


            return [

            'percentage' => $percentage . '%',
                   ];
          }
          else{
             return [

            'percentage' => 0,
                   ];

          }
    }


    public function getTasksWithSubTasks(){
      $tasks = Task::with('sub_tasks')->where('project_id', '=', $this->id)->get();

      return TaskResource::collection($tasks);
    }







}
