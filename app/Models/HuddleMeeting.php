<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HuddleMeeting extends Model
{
    use HasFactory;
    public $guarded = [];


    public function members(){
        return $this->BelongsToMany(User::class,'huddle_meeting_users','huddle_meeting_id','member_id');
     }
}
