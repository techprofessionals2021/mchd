<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        // dd(route('tasks.show',[auth()->user()->currentWorkspace->slug,$this->project_id,$this->id]));
        return [
            'key' => rand(10,10000),
            'id' => $this->id,
            'title' => $this->title,
            'due_date' => $this->due_date,
            'priority' => $this->priority,
            'status' => $this->stage,
            'assignee'=> UserResource::collection($this->users()),
            'modal_url' => route('tasks.show',[auth()->user()->currentWorkspace->slug,$this->project_id,$this->id]),
            'modal_url_edit' => route('tasks.edit',[auth()->user()->currentWorkspace->slug,$this->project_id,$this->id]),
            'modal_url_destory' => route('tasks.destroy',[auth()->user()->currentWorkspace->slug, $this->project_id, $this->id]),

            'children' => SubTaskResource::collection($this->sub_tasks)
        ];
    }
}
