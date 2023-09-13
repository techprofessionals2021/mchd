<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChMessage extends Model
{
       protected $fillable = [
        'from',
        'to',
        'message',
        'is_read',
    ];
    public function from_data(){
        return $this->hasOne('App\Models\User','id','from');
    }
}
