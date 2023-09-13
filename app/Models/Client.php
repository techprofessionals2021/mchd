<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    use Notifiable;
    protected $guard = 'client';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
                'lang', 'name', 'email', 'password','avatar','currant_workspace','email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getPermission($project_id){
        $data = ClientProject::where('client_id','=',$this->id)->where('project_id','=',$project_id)->first();
        return json_decode($data->permission, true );
    }

    public function getGuard(){
        return $this->guard;
    }

    public function workspace()
    {
        return $this->belongsToMany('App\Models\Workspace', 'client_workspaces', 'client_id', 'workspace_id');
    }
    public function currentWorkspace()
    {
        return $this->hasOne('App\Models\Workspace', 'id', 'currant_workspace');
    }
    public function getInvoices($workspace_id){
        return Invoice::where('workspace_id','=',$workspace_id)->where('client_id','=',$this->id)->get();
    }
}
