<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubTaskResource extends JsonResource
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
            'key' => rand(10,10000),
            'id' => $this->id,
            'title' => $this->name,
            'due_date' => $this->due_date,
            'priority' => '',
            // 'modal_url' => '/',

        ];
    }
}
