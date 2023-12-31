<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    protected $fillable = [
        'workspace_id',
        'user_id',
        'type',
        'data',
        'is_read',
    ];

    public function toHtml(){

        $data = json_decode($this->data);

        $link = '#';
        $icon = 'fa fa-bell';
        $icon_color = 'bg-primary';
        $text = '';

        if($this->type == 'task_assign'){
            $project = Project::find($data->project_id);
            if($project){
                $link = route('projects.task.board',[$this->workspace_id,$data->project_id]);
                $text = __('You have been assigned New task')." <b>".$data->title."</b> ".__('in project')." <b>".$project->name."</b>";
                $icon = "fa fa-clock-o";
                if($data->priority == 'Low'){
                    $icon_color = 'bg-success';
                }elseif($data->priority == 'High'){
                    $icon_color = 'bg-danger';
                }
            }else{
                return '';
            }
        }
        elseif($this->type == 'project_assign'){
            $link = route('projects.show',[$this->workspace_id,$data->id]);
            $text = __('You have been assigned a new project ')." <b>".$data->name."</b>";
            $icon = "fa fa-suitcase";
        }
        elseif($this->type == 'bug_assign'){
            $project = Project::find($data->project_id);
            if($project){
                $link = route('projects.bug.report',[$this->workspace_id,$data->project_id]);
                $text = __('New bug assign')." <b>".$data->title."</b> ".__('in project')." <b>".$project->name."</b>";
                $icon = "fa fa-bug";
                if($data->priority == 'Low'){
                    $icon_color = 'bg-success';
                }elseif($data->priority == 'High'){
                    $icon_color = 'bg-danger';
                }
            }
        }

        $user = User::find($this->user_id);
        $name = '';
        if($user && trim($user->name) != '')
            foreach (explode(' ', $user->name) as $word)
                $name .= strtoupper($word[0]);

        $date = $this->created_at->diffForHumans();

        $html = '<a href="'.$link.'" class="list-group-item list-group-item-action p-1">
                    <div class="d-flex" data-toggle="tooltip" data-placement="right" data-title="'.$date.'">
                        <div class="notification_icon_size m-t-10 ">
                            <span class="avatar bg-primary text-white rounded-circle px-2 py-2">'.$name.'</span>
                        </div>
                        <div class="flex-fill m-l-10">

                            <p class="text-lg lh-140 mb-0">
                                '.$text.'
                            </p>
                              <small class="float-right text-muted">'.$date.'</small>
                        </div>
                    </div>
                </a>';

        return $html;

// <div class="h6 text-sm mb-0 text-2xl">'.$user->name.'

// </div>
    }
}
