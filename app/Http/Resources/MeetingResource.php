<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MeetingResource extends JsonResource
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
        return [
            // 'key' => rand(10,10000),
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'colorScheme' => $this->color,

            $this->mergeWhen(isset($this->time_in), [
                'time' => [
                    'start' => $this->meeting_date.' '.date('H:i', strtotime($this->time_in)),
                    'end' => $this->meeting_date.' '.date('H:i', strtotime($this->time_out)),
                ],
                'start_time' => date('H:i', strtotime($this->time_in)),
                'end_time' => date('H:i', strtotime($this->time_out)),
            ]),
            $this->mergeWhen(!isset($this->time_in), [
                'time' => [
                    'start' => $this->meeting_date,
                    'end' => $this->meeting_date,
                ],
            ]),

            'assignee' => UserResource::collection($this->members)
        ];
    }
}
