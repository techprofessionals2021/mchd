<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Meeting extends Model
{
    use HasFactory;
    protected $guarded = [];

 public function members(){
    return $this->BelongsToMany(User::class,'meeting_users','meeting_id','member_id');
 }
}
