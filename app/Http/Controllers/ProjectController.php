<?php

namespace App\Http\Controllers;

use App\Exports\projectsExport;
use App\Http\Resources\MeetingResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\TaskResource;
use App\Imports\projectsImport;
use App\Models\ActivityLog;
use App\Models\BugComment;
use App\Models\BugFile;
use App\Models\BugReport;
use App\Models\BugStage;
use App\Models\Client;
use App\Models\ClientProject;
use App\Models\Comment;
use App\Models\Mail\SendInvication;
use App\Models\Mail\SendLoginDetail;
use App\Models\Mail\SendWorkspaceInvication;
use App\Models\Mail\ShareProjectToClient;
use App\Models\Meeting;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\Stage;
use App\Models\SubTask;
use App\Models\Task;
use App\Models\TaskFile;
use App\Models\ModelHasRole;
use App\Models\Timesheet;
use App\Models\TimeTracker;
use App\Models\User;
use App\Models\UserProject;
use App\Models\UserWorkspace;
use App\Models\Utility;
use App\Models\WorkspaceType;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Jenssegers\Date\Date;
use Maatwebsite\Excel\Facades\Excel;

class ProjectController extends Controller
{

    public function index($slug)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);


        $user = User::find($objUser->id);
        $permissions = $user->getPermissionWorkspace($currentWorkspace->id);
        // dd($permissions);
        if (!$permissions) {
            $permissions = [];
        }
        if ($objUser->getGuard() == 'client') {
            $projects = Project::select('projects.*')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->get();
        }

        else if(auth()->user()->hasRole('HOD')){

            // Get the 'HOD' role
        $hodRole = Role::where('name', 'HOD')->first();

            if ($hodRole) {
                // Find the 'HOD' role for the authenticated user
                $tags = ModelHasRole::where('model_id', Auth::id())
                    ->where('role_id', $hodRole->id)
                    ->first();


                    if ($tags) {
                        $tagsArray = json_decode($tags->tag, true);



                        if (isset($tagsArray)) {
                            $projects = Project::where(function ($query) use ($tagsArray) {
                                foreach ($tagsArray as $tag) {
                                    $query->orWhereJsonContains('tags', $tag);
                                }
                            })->get();

                        }

                        else{
                            $projects = Project::where('is_active','0')->get();
                        }

                        // dd($projects);
                    } else {
                    // Handle the case where no tags are found for the 'HOD' role
                    $projects = [];
                }
            } else {
                // Handle the case where the 'HOD' role does not exist
                $projects = [];
            }

        }
        else {
            $projects = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->get();
        }

        return view('projects.index', compact('currentWorkspace', 'projects','permissions'));
    }

    public function filterProducts(Request $request,$slug)
    {
        // dd(json_decode(Utility::convertTagsToJsonArray($request->tags)));
        $tags =  json_decode(Utility::convertTagsToJsonArray($request->tags)) ?? [];
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($objUser->getGuard() == 'client') {
            $projects = Project::select('projects.*')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->get();
        } else {
            if(is_null($request->tags)){

                $projects = Project::select('projects.*')
                ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
                ->where('user_projects.user_id', '=', $objUser->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->get();

            }else{

                $projects = Project::select('projects.*')
                ->join('user_projects', 'projects.id', '=', 'user_projects.project_id')
                ->where('user_projects.user_id', '=', $objUser->id)
                ->where('projects.workspace', '=', $currentWorkspace->id)
                ->where(function ($query) use ($tags) {
                    foreach ($tags as $tag) {
                        // dd($tag);
                        $query->orWhereJsonContains('tags', $tag);
                        // $query->whereIn('tags', $tags);
                    }
                })
                ->get();

                // dd($tags);


            }
        }

        $user = User::find($objUser->id);
        $permissions = $user->getPermissionWorkspace($currentWorkspace->id);

        return view('projects.index', compact('currentWorkspace', 'projects','permissions'));

    }

    public function tracker($slug, $id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $treckers = TimeTracker::where('project_id', $id)->get();
        $project = Project::where('id', $id)->first();
        $workspace_type = WorkspaceType::get();

        if (isset($project) && $project != null) {
            return view('projects.tracker', compact('currentWorkspace', 'treckers', 'id', 'project','workspace_type'));
        } else {
            return redirect()->back()->with('error', __('Tracker Not Found.'));
        }
    }
    public function store($slug, Request $request)
    {

        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $user = $currentWorkspace->id;
        $request->validate(['name' => 'required']);

        $post = $request->all();
        $post['tags'] = Utility::convertTagsToJsonArray($request->tags);
        $post['workspace'] = $currentWorkspace->id;
        $post['created_by'] = $objUser->id;
        $userList = [];
        if (isset($post['users_list'])) {
            $userList = $post['users_list'];
        }
        $userList[] = $objUser->email;
        $userList = array_filter($userList);
        $objProject = Project::create($post);

        $data = [];
        $data['basic_details']  = 'on';
        $data['member']  = 'on';
        $data['client']  = 'on';
        $data['attachment']  = 'on';
        $data['bug_report']  = 'on';
        $data['task']  = 'on';
        $data['password_protected']  ='off';
        $objProject->copylinksetting =  json_encode($data);
        $objProject->save();

        foreach ($userList as $email) {
            $permission = 'Member';
            $registerUsers = User::where('email', $email)->first();
            if ($registerUsers) {
                if ($registerUsers->id == $objUser->id) {
                    $permission = 'Owner';
                }
            } else {
                $arrUser = [];
                $arrUser['name'] = 'No Name';
                $arrUser['email'] = $email;
                $password = Str::random(8);
                $arrUser['password'] = Hash::make($password);
                $arrUser['currant_workspace'] = $objProject->workspace;
                $arrUser['lang'] = $currentWorkspace->lang;
                $registerUsers = User::create($arrUser);
                $registerUsers->password = $password;

                $assignPlan = $registerUsers->assignPlan(1);

                try {
                    Mail::to($email)->send(new SendLoginDetail($registerUsers));
                } catch (\Exception $e) {
                    $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                }
            }
            $this->inviteUser($registerUsers, $objProject, $permission);
        }

        $settings = Utility::getPaymentSetting($user);
        $uArr = [
            // 'user_name' => $user->name,
            'app_name'  => env('APP_NAME'),
            'user_name' => \Auth::user()->name,
            'project_name' => $objProject->name,
            'app_url' => env('APP_URL'),
        ];

        if (isset($settings['project_notificaation']) && $settings['project_notificaation'] == 1) {

            // $msg = $objProject->name . " created by " . \Auth::user()->name . '.';
            Utility::send_slack_msg('New Project', $user , $uArr);
        }

        if (isset($settings['telegram_project_notificaation']) && $settings['telegram_project_notificaation'] == 1) {
            // $msg = $objProject->name . " created by " . \Auth::user()->name . '.';
            Utility::send_telegram_msg('New Project' ,$uArr, $user);
        }

        //webhook
        $module ='New Project';
        // $webhook=  Utility::webhookSetting($module);
        $webhook=  Utility::webhookSetting($module , $user);

        if($webhook)
        {
            $parameter = json_encode($objProject);
            // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
            $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
            // if($status == true)
            // {
            //     return redirect()->back()->with('success', __('Project successfully created!'));
            // }
            // else
            // {
            //     return redirect()->back()->with('error', __('Webhook call failed.'));
            // }
        }

        return redirect()->back()->with('success', __('Project Created Successfully!') . ((isset($smtp_error)) ? ' <br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
    }

    public function export()
    {
        $name = 'projects_' . date('Y-m-d i:h:s');
        $data = Excel::download(new projectsExport(), $name . '.xlsx');
        ob_end_clean();

        return $data;
    }

    public function import(Request $request)
    {

        $slug = $request->slug;

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $objUser = Auth::user();

        $rules = [
            'file' => 'required|mimes:csv,txt',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $customers = (new projectsImport())->toArray(request()->file('file'))[0];

        $totalCustomer = count($customers) - 1;
        // dd($totalCustomer);

        $errorArray = [];
        for ($i = 1; $i <= count($customers) - 1; $i++) {
            $customer = $customers[$i];

            $customerData = new Project();
            // dd($customer[0]);
            $customerData->name = $customer[0];
            $customerData->status = $customer[1];
            $customerData->description = $customer[2];
            $customerData->start_date = $customer[3];

            $customerData->end_date = $customer[4];
            $customerData->budget = $customer[5];
            $customerData->workspace = $currentWorkspace->id;

            $customerData->created_by = $objUser->id;

            if (empty($customerData)) {
                $errorArray[] = $customerData;
            } else {
                $customerData->save();
            }

            $Data = new UserProject();

            $Data->user_id = $objUser->id;
            $Data->project_id = $customerData->id;
            $Data->is_active = "1";

            if (empty($Data)) {
                $errorArray[] = $Data;
            } else {
                $Data->save();
            }
        }
        $errorRecord = [];
        if (empty($errorArray)) {
            $data['status'] = 'success';
            $data['msg'] = __('Record successfully imported');
        } else {
            $data['status'] = 'error';
            $data['msg'] = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalCustomer . ' ' . 'record');

            foreach ($errorArray as $errorData) {

                $errorRecord[] = implode(',', $errorData);
            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }

    public function importFile($slug)
    {
        return view('projects.import', compact("slug"));
    }

    public function inviteUser(User $user, Project $project, $permission)
    {

        $authuser = Auth::user();
        $authusername  = User::where('id', '=', $authuser->id)->first();
        // assign workspace first
        $is_assigned = false;
        foreach ($user->workspace as $workspace) {
            if ($workspace->id == $project->workspace) {
                $is_assigned = true;
            }
        }

        if (!$is_assigned) {
            UserWorkspace::create(
                [
                    'user_id' => $user->id,
                    'workspace_id' => $project->workspace,
                    'permission' => $permission,
                ]
            );
            try {
                Mail::to($user->email)->send(new SendWorkspaceInvication($user, $project->workspaceData));
            } catch (\Exception $e) {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }
        }

        // assign project
        $arrData = [];
        $arrData['user_id'] = $user->id;
        $arrData['project_id'] = $project->id;
        $is_invited = UserProject::where($arrData)->first();
        if (!$is_invited) {
            $arrData['permission'] = json_encode(Utility::getAllPermission());
            UserProject::create($arrData);
            if ($permission != 'Owner') {
                try {

                    $uArr = [
                        'user_name' => $user->name,
                        'app_name'  => env('APP_NAME'),
                        'owner_name' => $authusername->name,
                        'project_name' => $project->name,
                        'project_status' => $project->status,
                        'app_url' => env('APP_URL'),
                    ];

                    // Send Email
                    $resp = Utility::sendEmailTemplate('Project Assigned', $user->id, $uArr);
                    // Mail::to($user->email)->send(new SendInvication($user, $project));
                } catch (\Exception $e) {
                    $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                }
                Utility::sendNotification('project_assign', $project->workspaceData, $user->id, $project);
            }
        }
    }

    public function invite(Request $request, $slug, $projectID)
    {

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $post = $request->all();
        $userList = $post['users_list'];

        $objProject = Project::find($projectID);
        foreach ($userList as $email) {
            $permission = 'Member';
            $registerUsers = User::where('email', $email)->first();
            if ($registerUsers) {
                $this->inviteUser($registerUsers, $objProject, $permission);
            } else {
                $arrUser = [];
                $arrUser['name'] = 'No Name';
                $arrUser['email'] = $email;
                $password = Str::random(8);
                $arrUser['password'] = Hash::make($password);
                $arrUser['currant_workspace'] = $objProject->workspace;
                $arrUser['lang'] = $currentWorkspace->lang;
                $registerUsers = User::create($arrUser);
                $registerUsers->password = $password;

                try {
                    Mail::to($email)->send(new SendLoginDetail($registerUsers));
                } catch (\Exception $e) {
                    $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                }

                $this->inviteUser($registerUsers, $objProject, $permission);
            }

            ActivityLog::create(
                [
                    'user_id' => \Auth::user()->id,
                    'user_type' => get_class(\Auth::user()),
                    'project_id' => $objProject->id,
                    'log_type' => 'Invite User',
                    'remark' => json_encode(['user_id' => $registerUsers->id]),
                ]
            );
        }
        return redirect()->back()->with('success', __('Users Invited Successfully!') . ((isset($smtp_error)) ? ' <br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
    }

    public function userPermission($slug, $project_id, $user_id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::find($project_id);
        $user = User::find($user_id);
        $permissions = $user->getPermission($project_id);
        if (!$permissions) {
            $permissions = [];
        }

        return view('projects.user_permission', compact('currentWorkspace', 'project', 'user', 'permissions'));
    }

    public function userPermissionStore($slug, $project_id, $user_id, Request $request)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $userProject = UserProject::where('user_id', '=', $user_id)->where('project_id', '=', $project_id)->first();
        $userProject->permission = json_encode($request->permissions);
        $userProject->save();

        return redirect()->back()->with('success', __('Permission Updated Successfully!'));
    }

    public function show($slug, $projectID)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $workspace_type = WorkspaceType::get();





        if($objUser->id != $currentWorkspace->id){
            // $objUser->currant_workspace = $currentWorkspace->id;
            // $objUser->save();
        }
        // dd($currentWorkspace);
        if ($objUser && $currentWorkspace) {
            if ($objUser->getGuard() == 'client') {
                $project = Project::select('projects.*')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            } else {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            }
            if (isset($project) && $project != null) {
                $chartData = $this->getProjectChart(
                    [
                        'workspace_id' => $currentWorkspace->id,
                        'project_id' => $projectID,
                        'duration' => 'week',
                    ]
                );

                $daysleft = round((((strtotime($project->end_date) - strtotime(date('Y-m-d'))) / 24) / 60) / 60);

                $permissions = Auth::user()->getPermission($project->id);

                $tags = json_decode($project->tags);

                $currentStatus='All';
                if($currentStatus == 'All'){
                    $tasks = Task::with('sub_tasks','stage')->where('project_id', '=', $projectID)
                    ->where(function ($query) use ($objUser) {
                        $query->whereRaw('FIND_IN_SET(?, assign_to)', [$objUser->id])
                              ->orWhereHas('project', function ($subQuery) use ($objUser) {
                                  $subQuery->where('created_by', $objUser->id);
                              });
                    })
                    ->get();
                }else{
                    $tasks = Task::with('sub_tasks','stage')->where('project_id', '=', $projectID)->whereHas('stage',function($query) use($currentStatus){
                        $query->where('name',$currentStatus);
                    })
                    ->where(function ($query) use ($objUser) {
                        $query->whereRaw('FIND_IN_SET(?, assign_to)', [$objUser->id])
                              ->orWhereHas('project', function ($subQuery) use ($objUser) {
                                  $subQuery->where('created_by', $objUser->id);
                              });
                    })
                    ->get();
                }

                $taskResource = TaskResource::collection($tasks);

                $taskStatus = ['All','Todo','In Progress','Review','Done'];



                return view('vue-ui.pages.project.show', compact('currentWorkspace', 'project', 'chartData', 'daysleft', 'permissions','tags','workspace_type','taskResource','currentStatus','taskStatus'));
                // return view('projects.show', compact('currentWorkspace', 'project', 'chartData', 'daysleft', 'permissions','tags','workspace_type'));
            } else {
                return redirect()->back()->with('error', __("Project Not Found."));
            }
        } else {

            return redirect()->back()->with('error', __("Workspace Not Found."));
        }
    }

    public function filterByStatus($slug, $projectID,$currentStatus='All')
    {

        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $workspace_type = WorkspaceType::get();





        if($objUser->id != $currentWorkspace->id){
            // $objUser->currant_workspace = $currentWorkspace->id;
            // $objUser->save();
        }
        // dd($currentWorkspace);
        if ($objUser && $currentWorkspace) {
            if ($objUser->getGuard() == 'client') {
                $project = Project::select('projects.*')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            } else {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            }
            if (isset($project) && $project != null) {
                $chartData = $this->getProjectChart(
                    [
                        'workspace_id' => $currentWorkspace->id,
                        'project_id' => $projectID,
                        'duration' => 'week',
                    ]
                );

                $daysleft = round((((strtotime($project->end_date) - strtotime(date('Y-m-d'))) / 24) / 60) / 60);

                $permissions = Auth::user()->getPermission($project->id);

                $tags = json_decode($project->tags);

                if($currentStatus == 'All'){
                    // dd('if');
                    $tasks = Task::with('sub_tasks','stage')->where('project_id', '=', $projectID)
                    ->where(function ($query) use ($objUser) {
                        $query->whereRaw('FIND_IN_SET(?, assign_to)', [$objUser->id])
                              ->orWhereHas('project', function ($subQuery) use ($objUser) {
                                  $subQuery->where('created_by', $objUser->id);
                              });
                    })
                    ->get();
                }else{
                    // dd('else');
                    $tasks = Task::with('sub_tasks','stage')->where('project_id', '=', $projectID)->whereHas('stage',function($query) use($currentStatus){
                        $query->where('name',$currentStatus);
                    })
                    ->where(function ($query) use ($objUser) {
                        $query->whereRaw('FIND_IN_SET(?, assign_to)', [$objUser->id])
                              ->orWhereHas('project', function ($subQuery) use ($objUser) {
                                  $subQuery->where('created_by', $objUser->id);
                              });
                    })
                    ->get();
                }

                // dd($tasks);
                $taskResource = TaskResource::collection($tasks);

                $taskStatus = ['All','Todo','In Progress','Review','Done'];



                return view('vue-ui.pages.project.show', compact('currentWorkspace', 'project', 'chartData', 'daysleft', 'permissions','tags','workspace_type','taskResource','currentStatus','taskStatus'));
                // return view('projects.show', compact('currentWorkspace', 'project', 'chartData', 'daysleft', 'permissions','tags','workspace_type'));
            } else {
                return redirect()->back()->with('error', __("Project Not Found."));
            }
        } else {

            return redirect()->back()->with('error', __("Workspace Not Found."));
        }
    }

    public function searchTasks(Request $request, $slug,$projectID,$currentStatus='All'){
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $workspace_type = WorkspaceType::get();

        if($objUser->id != $currentWorkspace->id){
            // $objUser->currant_workspace = $currentWorkspace->id;
            // $objUser->save();
        }
        // dd($currentWorkspace);
        if ($objUser && $currentWorkspace) {
            if ($objUser->getGuard() == 'client') {
                $project = Project::select('projects.*')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            } else {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            }
            if (isset($project) && $project != null) {
                $chartData = $this->getProjectChart(
                    [
                        'workspace_id' => $currentWorkspace->id,
                        'project_id' => $projectID,
                        'duration' => 'week',
                    ]
                );

                $daysleft = round((((strtotime($project->end_date) - strtotime(date('Y-m-d'))) / 24) / 60) / 60);

                $permissions = Auth::user()->getPermission($project->id);

                $tags = json_decode($project->tags);



                if($currentStatus == 'All'){
                    // dd('if');
                    $tasks = Task::with('sub_tasks','stage')->where('project_id', '=', $projectID)
                    ->where('title','LIKE',"%{$request->search}%")
                    ->where(function ($query) use ($objUser) {
                        $query->whereRaw('FIND_IN_SET(?, assign_to)', [$objUser->id])
                              ->orWhereHas('project', function ($subQuery) use ($objUser) {
                                  $subQuery->where('created_by', $objUser->id);
                              });
                    })
                    ->get();
                }else{
                    // dd('else');
                    $tasks = Task::with('sub_tasks','stage')->where('project_id', '=', $projectID)
                    ->where('title','LIKE',"%{$request->search}%")->whereHas('stage',function($query) use($currentStatus){
                        $query->where('name',$currentStatus);
                    })
                    ->where(function ($query) use ($objUser) {
                        $query->whereRaw('FIND_IN_SET(?, assign_to)', [$objUser->id])
                              ->orWhereHas('project', function ($subQuery) use ($objUser) {
                                  $subQuery->where('created_by', $objUser->id);
                              });
                    })
                    ->get();
                }

               $taskResource = TaskResource::collection($tasks);
               $taskStatus = ['All','Todo','In Progress','Review','Done'];
                return view('vue-ui.pages.project.show', compact('currentWorkspace', 'project', 'chartData', 'daysleft', 'permissions','tags','workspace_type','taskResource','taskStatus','currentStatus'));
                // return view('projects.show', compact('currentWorkspace', 'project', 'chartData', 'daysleft', 'permissions','tags','workspace_type'));
            } else {
                return redirect()->back()->with('error', __("Project Not Found."));
            }
        } else {

            return redirect()->back()->with('error', __("Workspace Not Found."));
        }

        $tasks = Task::with('sub_tasks')->where('project_id', '=', $projectID)
        ->where('title','LIKE',"%{$request->search}%")->get();
    }

    public function getProjectChart($arrParam)
    {
        $arrDuration = [];
        if ($arrParam['duration'] && $arrParam['duration'] == 'week') {
            $previous_week = Utility::getFirstSeventhWeekDay(-1);
            foreach ($previous_week['datePeriod'] as $dateObject) {
                $arrDuration[$dateObject->format('Y-m-d')] = $dateObject->format('D');
            }
        }

        $arrTask = [
            'label' => [],
            'color' => [],
        ];
        $stages = Stage::where('workspace_id', '=', $arrParam['workspace_id'])->orderBy('order');

        foreach ($arrDuration as $date => $label) {
            $objProject = Task::select('status', DB::raw('count(*) as total'))->whereDate('updated_at', '=', $date)->groupBy('status');

            if (isset($arrParam['project_id'])) {
                $objProject->where('project_id', '=', $arrParam['project_id']);
            }
            if (isset($arrParam['workspace_id'])) {
                $objProject->whereIn('project_id', function ($query) use ($arrParam) {
                    $query->select('id')->from('projects')->where('workspace', '=', $arrParam['workspace_id']);
                });
            }
            $data = $objProject->pluck('total', 'status')->all();

            foreach ($stages->pluck('name', 'id')->toArray() as $id => $stage) {
                $arrTask[$id][] = isset($data[$id]) ? $data[$id] : 0;
            }
            $arrTask['label'][] = __($label);
        }
        $arrTask['stages'] = $stages->pluck('name', 'id')->toArray();
        $arrTask['color'] = $stages->pluck('color')->toArray();

        return $arrTask;
    }


    public function edit($slug, $projectID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        // dd($currentWorkspace);
        $objUser = Auth::user();



    //    dd($currentWorkspace->users);
        if(auth()->user()->type != 'super-admin'){
            $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
        }else{

            $project = Project::select('projects.*')
            // ->where('projects.workspace', '=', $currentWorkspace->id)
            ->where('projects.id', '=', $projectID)
            ->first();
            // dd($project);
        }
        // dd(auth()->user()->type);
        // dd($project);
        $users = User::select('users.*')->join('user_projects', 'user_projects.user_id', '=', 'users.id')->where('project_id', '=', $project->id)->get();

        // dd($currentWorkspace->users);
        // dd($project->users->pluck('id'));
        return view('projects.edit', compact('currentWorkspace', 'project','users'));
    }

    public function create($slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        return view('projects.create', compact('currentWorkspace'));
    }


    public function popup($slug, $projectID)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();

        return view('projects.invite', compact('currentWorkspace', 'project'));
    }

    public function userDelete($slug, $project_id, $user_id)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $project_id)->first();
        if ($currentWorkspace->permission == 'Owner') {
            if (count($project->user_tasks($user_id)) == 0) {
                UserProject::where('user_id', '=', $user_id)->where('project_id', '=', $project->id)->delete();

                return redirect()->back()->with('success', __('User Deleted Successfully!'));
            } else {
                return redirect()->back()->with('warning', __('Please Remove User From Tasks!'));
            }
        } else {
            return redirect()->route('projects.index', $slug)->with('error', __("You can't Delete Project!"));
        }
    }

    public function sharePopup($slug, $projectID)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();

        return view('projects.share', compact('currentWorkspace', 'project'));
    }

    public function clientDelete($slug, $project_id, $client_id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::find($project_id)->first();
        if ($currentWorkspace->permission == 'Owner') {
            ClientProject::where('client_id', '=', $client_id)->where('project_id', '=', $project->id)->delete();

            return redirect()->back()->with('success', __('Client Deleted Successfully!'));
        } else {
            return redirect()->route('projects.index', $slug)->with('error', __("You can't Delete Project!"));
        }
    }

    public function share($slug, $projectID, Request $request)
    {

        $authuser = Auth::user();
        $authusername  = User::where('id', '=', $authuser->id)->first();
        $project = Project::find($projectID);
        foreach ($request->clients as $client_id) {
            $client = Client::find($client_id);
            $user = Client::find($client_id);

            if (ClientProject::where('client_id', '=', $client_id)->where('project_id', '=', $projectID)->count() == 0) {
                ClientProject::create(
                    [
                        'client_id' => $client_id,
                        'project_id' => $projectID,
                        'permission' => json_encode(Utility::getAllPermission()),
                    ]
                );
            }

            try {
                $uArr = [
                    'user_name' => $client->name,
                    'app_name'  => env('APP_NAME'),
                    'owner_name' => $authusername->name,
                    'project_name' => $project->name,
                    'project_status' => $project->status,
                    'app_url' => env('APP_URL'),
                ];


                // Send Email
                $resp = Utility::sendclientEmailTemplate('Project Assigned', $user->id, $uArr);
            } catch (\Exception $e) {
                $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
            }

            ActivityLog::create(
                [
                    'user_id' => \Auth::user()->id,
                    'user_type' => get_class(\Auth::user()),
                    'project_id' => $project->id,
                    'log_type' => 'Share with Client',
                    'remark' => json_encode(['client_id' => $client->id]),
                ]
            );
        }

        return redirect()->back()->with('success', __('Project Share Successfully!') . ((isset($smtp_error)) ? ' <br> <span class="text-danger">' . $smtp_error . '</span>' : ''));
    }

    public function update(Request $request, $slug, $projectID)
    {
        $request->validate(
            [
                'name' => 'required',
            ]
        );
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
        $users = $request->users_list;
        $data = $request->all();
        $data['tags'] = Utility::convertTagsToJsonArray($request->tags);
        array_push($users,auth()->id());
        // dd($users);
        $project->update($data);

        $project->users()->syncWithPivotValues($users,['permission' => json_encode(Utility::getAllPermission())]);


        return redirect()->back()->with('success', __('Project Updated Successfully!'));
    }

    public function destroy($slug, $projectID)
    {
        $objUser = Auth::user();
        $project = Project::find($projectID);

        if ($project->created_by == $objUser->id) {
            UserProject::where('project_id', '=', $projectID)->delete();
            ProjectFile::where('project_id', '=', $projectID)->delete();
            $project->delete();

            return redirect()->route('projects.index', $slug)->with('success', __('Project Deleted Successfully!'));
        } else {
            return redirect()->route('projects.index', $slug)->with('error', __("You can't Delete Project!"));
        }
    }

    public function leave($slug, $projectID)
    {
        $objUser = Auth::user();
        $userProject = Project::find($projectID);
        UserProject::where('project_id', '=', $userProject->id)->where('user_id', '=', $objUser->id)->delete();

        return redirect()->route('projects.index', $slug)->with('success', __('Project Leave Successfully!'));
    }


    public function taskBoard($slug, $projectID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $workspace_type = WorkspaceType::get();
        $objUser = Auth::user();

        if ($objUser && $currentWorkspace) {

            if ($objUser->getGuard() == 'client') {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            } else {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            }

            if ($project) {
                $stages = $statusClass = [];

                $permissions = Auth::user()->getPermission($projectID);

                if ($project && (isset($permissions) && in_array('show task', $permissions)) || (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner')) {
                    $stages = Stage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->get();

                    foreach ($stages as &$status) {
                        $statusClass[] = 'task-list-' . str_replace(' ', '_', $status->id);
                        $task = Task::where('project_id', '=', $projectID);
                        if ($currentWorkspace->permission != 'Owner' && $objUser->getGuard() != 'client') {
                            if (isset($objUser) && $objUser) {
                                $task->whereRaw("find_in_set('" . $objUser->id . "',assign_to)");
                            }
                        }
                        $task->orderBy('order');
                        $status['tasks'] = $task->where('status', '=', $status->id)->get();
                    }
                }

                return view('projects.taskboard', compact('currentWorkspace', 'project', 'stages', 'statusClass','workspace_type'));
            } else {
                return redirect()->back()->with('error', __('Task Not Found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Workspace Not Found.'));
        }
    }

    public function taskCreate($slug, $projectID)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($objUser->getGuard() == 'client') {
            $project = Project::select('projects.*')->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            $projects = Project::select('projects.*')->join('client_projects', 'client_projects.project_id', '=', 'projects.id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->get();
        } else
        {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            $projects = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->get();
        }

        $users = User::select('users.*')->join('user_projects', 'user_projects.user_id', '=', 'users.id')->where('project_id', '=', $projectID)->get();

        return view('projects.taskCreate', compact('currentWorkspace', 'project', 'projects', 'users'));
    }

    public function taskStore(Request $request, $slug, $projectID)
    {
        // dd($request->all());
        $request->validate(
            [
                'project_id' => 'required',
                'title' => 'required',
                'priority' => 'required',
                'assign_to' => 'required',
                'start_date' => 'required',
                'due_date' => 'required',
            ]
        );
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $user = $currentWorkspace->id;
        $project_name = Project::where('id', $request->project_id)->first();

        if ($objUser->getGuard() == 'client') {
            $project = Project::where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $request->project_id)->first();
        }

        if ($project) {
            $post = $request->all();
            $stage = Stage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->first();
            if ($stage) {
                $post['milestone_id'] = !empty($request->milestone_id) ? $request->milestone_id : 0;
                $post['status'] = $stage->id;
                $post['assign_to'] = implode(",", $request->assign_to);
                $post['tags'] = Utility::convertTagsToJsonArray($request->tags);
                $task = Task::create($post);
                $task->assignees()->attach($request->assign_to);
                if ($request->get('synchronize_type') == 'google_calender') {

                    $type = 'task';
                    $request1 = new Task();
                    $request1->title = $request->title;
                    // dd($request1);
                    $request1->start_date = $request->start_date;
                    $request1->end_date = $request->due_date;

                    $a =  Utility::addCalendarData($request1, $type , $slug);
                }

                ActivityLog::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'user_type' => get_class(\Auth::user()),
                        'project_id' => $projectID,
                        'log_type' => 'Create Task',
                        'remark' => json_encode(['title' => $task->title]),
                    ]
                );

                $settings = Utility::getPaymentSetting($user);
                $uArr = [
                    // 'user_name' => $user->name,
                    'project_name' => $project_name->name,
                    'user_name' => \Auth::user()->name,
                    'task_title' => $task->title,
                    'app_name'  => env('APP_NAME'),
                    'app_url' => env('APP_URL'),
                ];

                if (isset($settings['task_notificaation']) && $settings['task_notificaation'] == 1) {

                    Utility::send_slack_msg('New Task', $user , $uArr);
                    // $msg = $request->title . " of " . $project_name->name . " created by " . \Auth::user()->name . '.';
                    // Utility::send_slack_msg($msg, $user);
                }

                if (isset($settings['telegram_task_notificaation']) && $settings['telegram_task_notificaation'] == 1) {
                    // $msg = $request->title . " of " . $project_name->name . " created by " . \Auth::user()->name . '.';
                    Utility::send_telegram_msg('New Task' ,$uArr, $user);
                }

                Utility::sendNotification('task_assign', $currentWorkspace, $request->assign_to, $task);

                //webhook
                $module ='New Task';
                // $webhook=  Utility::webhookSetting($module);
                $webhook=  Utility::webhookSetting($module , $user);

                if($webhook)
                {
                    $parameter = json_encode($task);
                    // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                    $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
                    // if($status == true)
                    // {
                    //     return redirect()->back()->with('success', __('Task successfully created!'));
                    // }
                    // else
                    // {
                    //     return redirect()->back()->with('error', __('Webhook call failed.'));
                    // }
                }

                if ($objUser->getGuard() == 'client') {
                    return redirect()->route(
                        'client.projects.task.board',
                        [
                            $currentWorkspace->slug,
                            $request->project_id,
                        ]
                    )->with('success', __('Task Create Successfully!'));
                } else {
                    // return redirect()->route(
                    //     'projects.task.board',
                    //     [
                    //         $currentWorkspace->slug,
                    //         $request->project_id,
                    //     ]
                    // )->with('success', __('Task Create Successfully!'));


                    return redirect()->back()->with('success', __('Task Create Successfully!'));
                }
            } else {
                return redirect()->back()->with('error', __('Please add stages first.'));
            }
        } else {
            return redirect()->back()->with('error', __("You can't Add Task!"));
        }
    }
    public function taskOrderUpdate(Request $request, $slug, $projectID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project_name = Project::where('id', $projectID)->first();
        $user1 = $currentWorkspace->id;
        if (isset($request->sort)) {
            foreach ($request->sort as $index => $taskID) {
                $task = Task::find($taskID);
                $task->order = $index;
                $task->save();
            }
        }

        if ($request->new_status != $request->old_status) {
            $new_status = Stage::find($request->new_status);
            $old_status = Stage::find($request->old_status);
            $user = Auth::user();
            $task = Task::find($request->id);
            $task->status = $request->new_status;
            $task->save();

            $name = $user->name;
            $id = $user->id;

            ActivityLog::create(
                [
                    'user_id' => $id,
                    'user_type' => get_class($user),
                    'project_id' => $projectID,
                    'log_type' => 'Move',
                    'remark' => json_encode(
                        [
                            'title' => $task->title,
                            'old_status' => $old_status->name,
                            'new_status' => $new_status->name,
                        ]
                    ),
                ]
            );

            $settings = Utility::getPaymentSetting($user1);

            $uArr = [
                // 'user_name' => $user->name,
                'project_name' => $project_name->name,
                'user_name' => \Auth::user()->name,
                'task_title' => $task->title,
                'old_stage' => $old_status->name,
                'new_stage' => $new_status->name,
                'app_url' => env('APP_URL'),
                'app_name'  => env('APP_NAME'),
            ];

            if (isset($settings['taskmove_notificaation']) && $settings['taskmove_notificaation'] == 1) {
                Utility::send_slack_msg('Task Stage Updated', $user1 , $uArr);
            }

            if (isset($settings['telegram_taskmove_notificaation']) && $settings['telegram_taskmove_notificaation'] == 1) {
                Utility::send_telegram_msg('Task Stage Updated' ,$uArr, $user1);
            }

            //webhook
            $module ='Task Stage Updated';
            // $webhook=  Utility::webhookSetting($module);
            $webhook=  Utility::webhookSetting($module , $user1);

            if($webhook)
            {
                $parameter = json_encode($task);
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
                // if($status == true)
                // {
                //     return redirect()->back()->with('success', __('Task Stage successfully created!'));
                // }
                // else
                // {
                //     return redirect()->back()->with('error', __('Webhook call failed.'));
                // }
            }

            return $task->toJson();
        }
    }

    public function taskEdit($slug, $projectID, $taskId)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);


        if ($objUser->getGuard() == 'client') {
            $project = Project::select('projects.*')->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            $projects = Project::select('projects.*')->join('client_projects', 'client_projects.project_id', '=', 'projects.id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->get();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.id', '=', $projectID)->first();
            $projects = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->get();
        }
        $users = User::select('users.*')->join('user_projects', 'user_projects.user_id', '=', 'users.id')->where('project_id', '=', $projectID)->get();
        $task = Task::find($taskId);
        $task->assign_to = explode(",", $task->assign_to);
        // dd($taskId);
        // dd( $project);
        return view('projects.taskEdit', compact('currentWorkspace', 'project', 'projects', 'users', 'task'));
    }

    public function taskUpdate(Request $request, $slug, $projectID, $taskID)
    {

        $request->validate(
            [
                'project_id' => 'required',
                'title' => 'required',
                'priority' => 'required',
                'assign_to' => 'required',
                'start_date' => 'required',
                'due_date' => 'required',
            ]
        );
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($objUser->getGuard() == 'client') {
            $project = Project::where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.id', '=', $request->project_id)->first();
        }
        if ($project) {
            $post = $request->all();
            $post['assign_to'] = implode(",", $request->assign_to);
            $post['tags'] = Utility::convertTagsToJsonArray($request->tags);
            $task = Task::find($taskID);
            $task->update($post);
            $task->assignees()->sync($request->assign_to);
            return redirect()->back()->with('success', __('Task Updated Successfully!'));
        } else {
            return redirect()->back()->with('error', __("You can't Edit Task!"));
        }
    }

    public function taskDestroy($slug, $projectID, $taskID)
    {

        $objUser = Auth::user();
        $task = Task::find($taskID);

        try {
            if ($task) {
                $task = Task::where('id', $taskID)->delete();
                return redirect()->back()->with('success', __('Task Deleted Successfully!'));
            } else {
                return redirect()->back()->with('error', __("You can't Delete Task!"));
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', __("You can't Delete Task!"));
        }
    }

    public function taskShow($slug, $projectID, $taskID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $task = Task::find($taskID);
        $completedSubTaskPercentage = $task->subTaskPercentage();
        $project  = Project::find($projectID);
        if(Auth::user() != null){
            $objUser         = Auth::user();
        }else{
            $objUser         = User::where('id',$project->created_by)->first();
        }

        $clientID = '';
        if ($objUser->getGuard() == 'client') {
            $clientID = $objUser->id;
        }

        $tags = json_decode($task->tags);

        return view('projects.taskShow', compact('tags','currentWorkspace', 'task', 'clientID','completedSubTaskPercentage'));
    }

    public function taskDrag(Request $request, $slug, $projectID, $taskID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $task = Task::find($taskID);
        $task->start_date = $request->start;
        $task->due_date = $request->end;
        $task->save();
    }

    public function commentStore(Request $request, $slug, $projectID, $taskID, $clientID = '')
    {
        $task = Task::find($taskID);
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project_name = Project::where('id', $projectID)->first();
        $user1 = $currentWorkspace->id;
        $post = [];
        $post['task_id'] = $taskID;
        $post['comment'] = $request->comment;
        if ($clientID) {
            $post['created_by'] = $clientID;
            $post['user_type'] = 'Client';
        } else {
            $post['created_by'] = Auth::user()->id;
            $post['user_type'] = 'User';
        }
        $comment = Comment::create($post);
        if ($comment->user_type == 'Client') {
            $user = $comment->client;
        } else {
            $user = $comment->user;
        }
        if (empty($clientID)) {
            $comment->deleteUrl = route(
                'comment.destroy',
                [
                    $currentWorkspace->slug,
                    $projectID,
                    $taskID,
                    $comment->id,
                ]
            );
        }

        $settings = Utility::getPaymentSetting($user1);

        $uArr = [
            // 'user_name' => $user->name,
            'project_name' => $project_name->name,
            'user_name' => \Auth::user()->name,
            'task_title' => $task->title,
            'app_url' => env('APP_URL'),
            'app_name'  => env('APP_NAME'),
        ];
        if (isset($settings['taskcom_notificaation']) && $settings['taskcom_notificaation'] == 1) {

            Utility::send_slack_msg('New Task Comment', $user1 , $uArr);
        }

        if (isset($settings['telegram_taskcom_notificaation']) && $settings['telegram_taskcom_notificaation'] == 1) {
            Utility::send_telegram_msg('New Task Comment' ,$uArr, $user1);

        }

        //webhook
        $module ='New Task Comment';
        // $webhook=  Utility::webhookSetting($module);
        $webhook=  Utility::webhookSetting($module , $user1);

        if($webhook)
        {
            $parameter = json_encode($task);
            // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
            $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
            // if($status == true)
            // {
            //     return redirect()->back()->with('success', __('Task successfully created!'));
            // }
            // else
            // {
            //     return redirect()->back()->with('error', __('Webhook call failed.'));
            // }
        }

        return $comment->toJson();
    }

    public function commentDestroy(Request $request, $slug, $projectID, $taskID, $commentID)
    {
        $comment = Comment::find($commentID);
        $comment->delete();

        return "true";
    }

    public function commentStoreFile(Request $request, $slug, $projectID, $taskID, $clientID = '')
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $request->validate(['file' => 'required']);
        $dir = 'app/public/tasks/';
        $fileName = $taskID . time() . "_" . $request->file->getClientOriginalName();
        // $request->file->storeAs('tasks', $fileName);

        $path = Utility::upload_file($request, 'file', $fileName, $dir, []);
        if ($path['flag'] == 1) {
            // Utility::upload_file($request,'file',$fileName,$dir,[]);
            $file = $path['url'];
        } else {
            return redirect()->back()->with('error', __($path['msg']));
        }

        $post['task_id'] = $taskID;
        $post['file'] = $fileName;
        $post['name'] = $request->file->getClientOriginalName();
        $post['extension'] = "." . $request->file->getClientOriginalExtension();
        $post['file_size'] = round(($request->file->getMaxFilesize() / 1024) / 1024, 2) . ' MB';
        if ($clientID) {
            $post['created_by'] = $clientID;
            $post['user_type'] = 'Client';
        } else {
            $post['created_by'] = Auth::user()->id;
            $post['user_type'] = 'User';
        }
        $TaskFile = TaskFile::create($post);
        $user = $TaskFile->user;
        $TaskFile->deleteUrl = '';
        if (empty($clientID)) {
            $TaskFile->deleteUrl = route(
                'comment.destroy.file',
                [
                    $currentWorkspace->slug,
                    $projectID,
                    $taskID,
                    $TaskFile->id,
                ]
            );
        }

        return $TaskFile->toJson();
    }

    public function commentDestroyFile(Request $request, $slug, $projectID, $taskID, $fileID)
    {
        $commentFile = TaskFile::find($fileID);

        if ($commentFile) {
            // $path = storage_path('tasks/' . $commentFile->file);
            $logo = Utility::get_file('tasks/');
            $path = $logo . $commentFile->file;
            if (file_exists($path)) {
                \File::delete($path);
            }
            $commentFile->delete();

            return "true";
        } else {
            return "false";
        }
    }

    public function getSearchJson($slug, $search)
    {
        $user = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($user->getGuard() == 'client') {
            $objProject = Project::select(
                [
                    'projects.id',
                    'projects.name',
                ]
            )->join('client_projects', 'client_projects.project_id', '=', 'projects.id')->where('client_projects.client_id', '=', $user->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.name', 'LIKE', $search . "%")->get();
            $arrProject = [];
            foreach ($objProject as $project) {
                $arrProject[] = [
                    'text' => $project->name,
                    'link' => route(
                        'client.projects.show',
                        [
                            $currentWorkspace->slug,
                            $project->id,
                        ]
                    ),
                ];
            }
        } else {
            $objProject = Project::select(
                [
                    'projects.id',
                    'projects.name',
                ]
            )->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $user->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.name', 'LIKE', $search . "%")->get();
            $arrProject = [];
            foreach ($objProject as $project) {
                $arrProject[] = [
                    'text' => $project->name,
                    'link' => route(
                        'projects.show',
                        [
                            $currentWorkspace->slug,
                            $project->id,
                        ]
                    ),
                ];
            }
        }

        if ($user->getGuard() == 'client') {
            $arrTask = [];
            $objTask = Task::select(
                [
                    'tasks.project_id',
                    'tasks.title',
                ]
            )->join('projects', 'tasks.project_id', '=', 'projects.id')->join('client_projects', 'client_projects.project_id', '=', 'projects.id')->where('client_projects.client_id', '=', $user->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('tasks.title', 'LIKE', $search . "%")->get();
            foreach ($objTask as $task) {
                $arrTask[] = [
                    'text' => $task->title,
                    'link' => route(
                        'client.projects.task.board',
                        [
                            $currentWorkspace->slug,
                            $task->project_id,
                        ]
                    ),
                ];
            }
        } else {
            $objTask = Task::select(
                [
                    'tasks.project_id',
                    'tasks.title',
                ]
            )->join('projects', 'tasks.project_id', '=', 'projects.id')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $user->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('tasks.title', 'LIKE', $search . "%")->get();
            $arrTask = [];
            foreach ($objTask as $task) {
                $arrTask[] = [
                    'text' => $task->title,
                    'link' => route(
                        'projects.task.board',
                        [
                            $currentWorkspace->slug,
                            $task->project_id,
                        ]
                    ),
                ];
            }
        }

        return json_encode(
            [
                'Projects' => $arrProject,
                'Tasks' => $arrTask,
            ]
        );
    }

    public function milestone($slug, $projectID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::find($projectID);

        return view('projects.milestone', compact('currentWorkspace', 'project'));
    }

    public function milestoneStore($slug, $projectID, Request $request)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project_name = Project::where('id', $projectID)->first();
        $user1 = $currentWorkspace->id;
        $project = Project::find($projectID);


        $rules = [
            'title' => 'required',
            'status' => 'required',
            'cost' => 'required',
            'summary' => 'required',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $milestone = new Milestone();
        $milestone->project_id = $project->id;
        $milestone->title = $request->title;
        $milestone->status = $request->status;
        $milestone->cost = $request->cost;
        $milestone->summary = $request->summary;
        $milestone->save();

        ActivityLog::create(
            [
                'user_id' => \Auth::user()->id,
                'user_type' => get_class(\Auth::user()),
                'project_id' => $project->id,
                'log_type' => 'Create Milestone',
                'remark' => json_encode(['title' => $milestone->title]),
            ]
        );

        $settings = Utility::getPaymentSetting($user1);
        $uArr = [
            // 'user_name' => $user->name,
            'project_name' => $project_name->name,
            'user_name' => \Auth::user()->name,
            'milestone_title' => $milestone->title,
            'app_url' => env('APP_URL'),
            'app_name'  => env('APP_NAME'),
        ];
        if (isset($settings['milestone_notificaation']) && $settings['milestone_notificaation'] == 1) {
            Utility::send_slack_msg('New Milestone', $user1 , $uArr);
        }

        if (isset($settings['telegram_milestone_notificaation']) && $settings['telegram_milestone_notificaation'] == 1) {
            Utility::send_telegram_msg('New Milestone' ,$uArr, $user1);
        }

        //webhook
        $module ='New Milestone';
        // $webhook=  Utility::webhookSetting($module);
        $webhook=  Utility::webhookSetting($module , $user1);

        if($webhook)
        {
            $parameter = json_encode($milestone);
            // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
            $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
            // if($status == true)
            // {
            //     return redirect()->back()->with('success', __('Milestone successfully created!'));
            // }
            // else
            // {
            //     return redirect()->back()->with('error', __('Webhook call failed.'));
            // }
        }

        return redirect()->back()->with('success', __('Milestone Created Successfully!'));
    }

    public function milestoneEdit($slug, $milestoneID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $milestone = Milestone::find($milestoneID);

        return view('projects.milestoneEdit', compact('currentWorkspace', 'milestone'));
    }

    public function milestoneUpdate($slug, $milestoneID, Request $request)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $user1 = $currentWorkspace->id;
        $request->validate(
            [
                'title' => 'required',
                'status' => 'required',
                'cost' => 'required',
            ]
        );

        $milestone = Milestone::find($milestoneID);
        $milestone->title = $request->title;
        $milestone->status = $request->status;
        $milestone->cost = $request->cost;
        $milestone->summary = $request->summary;
        $milestone->progress = $request->progress;
        $milestone->start_date = $request->start_date;
        $milestone->end_date = $request->end_date;
        $milestone->save();

        $settings = Utility::getPaymentSetting($user1);
        $project_name = Project::where('id', $milestone->project_id)->first();

        $uArr = [
            // 'user_name' => $user->name,
            'project_name' => $project_name->name,
            'user_name' => \Auth::user()->name,
            'milestone_title' => $milestone->title,
            'milestone_status' => $milestone->status,
            'app_url' => env('APP_URL'),
            'app_name'  => env('APP_NAME'),
        ];

        if (isset($settings['milestonest_notificaation']) && $settings['milestonest_notificaation'] == 1) {

            Utility::send_slack_msg('Milestone Status Updated', $user1 , $uArr);
        }

        if (isset($settings['telegram_milestonest_notificaation']) && $settings['telegram_milestonest_notificaation'] == 1) {
            Utility::send_telegram_msg('Milestone Status Updated' ,$uArr, $user1);
        }

        //webhook
        $module ='Milestone Status Updated';
        // $webhook=  Utility::webhookSetting($module);
        $webhook=  Utility::webhookSetting($module , $user1);

        if($webhook)
        {
            $parameter = json_encode($milestone);
            // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
            $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
            // if($status == true)
            // {
            //     return redirect()->back()->with('success', __('Milestone successfully created!'));
            // }
            // else
            // {
            //     return redirect()->back()->with('error', __('Webhook call failed.'));
            // }
        }

        return redirect()->back()->with('success', __('Milestone Updated Successfully!'));
    }

    public function milestoneDestroy($slug, $milestoneID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $milestone = Milestone::find($milestoneID);
        $milestone->delete();

        return redirect()->back()->with('success', __('Milestone deleted Successfully!'));
    }

    public function milestoneShow($slug, $milestoneID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $milestone = Milestone::find($milestoneID);

        return view('projects.milestoneShow', compact('currentWorkspace', 'milestone'));
    }

    public function subTaskStore(Request $request, $slug, $projectID, $taskID, $clientID = '')
    {
        // dd($request->all());
        $post = [];
        $post['task_id'] = $taskID;
        $post['name'] = $request->name;
        $post['due_date'] = $request->due_date;
        $post['status'] = 0;

        if ($clientID) {
            $post['created_by'] = $clientID;
            $post['user_type'] = 'Client';
        } else {
            $post['created_by'] = Auth::user()->id;
            $post['user_type'] = 'User';
        }
        $subtask = SubTask::create($post);
        if ($subtask->user_type == 'Client') {
            $user = $subtask->client;
        } else {
            $user = $subtask->user;
        }
        $subtask->updateUrl = route(
            'subtask.update',
            [
                $slug,
                $projectID,
                $subtask->id,
            ]
        );
        $subtask->deleteUrl = route(
            'subtask.destroy',
            [
                $slug,
                $projectID,
                $subtask->id,
            ]
        );

        return $subtask->toJson();
    }

    public function subTaskUpdate($slug, $projectID, $subtaskID)
    {
        $subtask = SubTask::find($subtaskID);
        $subtask->status = (int) !$subtask->status;
        $subtask->save();

        return $subtask->toJson();
    }

    public function subTaskDestroy($slug, $projectID, $subtaskID)
    {
        $subtask = SubTask::find($subtaskID);
        $subtask->delete();

        return "true";
    }
    public function customSubTaskDestroy($slug, $projectID, $subtaskID)
    {
        $subtask = SubTask::find($subtaskID);
        $subtask->delete();

        return redirect()->back();
    }

    public function fileUpload($slug, $id, Request $request)
    {
        $project = Project::find($id);
        $request->validate(['file' => 'required']);
        $file_name = $request->file->getClientOriginalName();
        $file_path = $project->id . "_" . md5(time()) . "_" . $request->file->getClientOriginalName();
        // $request->file->storeAs('project_files', $file_path);

        $dir = 'project_files/';
        $path = Utility::upload_file($request, 'file', $file_path, $dir, []);
        if ($path['flag'] == 1) {
            $file = $path['url'];
        } else {
            return redirect()->back()->with('error', __($path['msg']));
        }

        $file = ProjectFile::create(
            [
                'project_id' => $project->id,
                'file_name' => $file_name,
                'file_path' => $file_path,
            ]
        );
        $return = [];
        $return['is_success'] = true;
        $return['download'] = route(
            'projects.file.download',
            [
                $slug,
                $project->id,
                $file->id,
            ]
        );
        $return['delete'] = route(
            'projects.file.delete',
            [
                $slug,
                $project->id,
                $file->id,
            ]
        );

        ActivityLog::create(
            [
                'user_id' => \Auth::user()->id,
                'user_type' => get_class(\Auth::user()),
                'project_id' => $project->id,
                'log_type' => 'Upload File',
                'remark' => json_encode(['file_name' => $file_name]),
            ]
        );

        return response()->json($return);
    }

    public function fileDownload($slug, $id, $file_id)
    {

        $project = Project::find($id);

        $file = ProjectFile::find($file_id);
        if ($file) {
            // $file_path = storage_path('project_files/' . $file->file_path);
            // $filename = $file->file_name;

            // return \Response::download(
            //     $file_path, $filename, [
            //         'Content-Length: ' . filesize($file_path),
            //     ]
            // );
            $logo = Utility::get_file('project_files/');

            $settings = Utility::getAdminPaymentSettings();
            try {
                if ($settings['storage_setting'] == 'local') {
                    $file_path = storage_path('project_files/' . $file->file_path);
                } else {
                    $file_path = $logo . $file->file_path;
                }

                // dd($file_path);
                $filename = $file->file_name;

                return \Response::download(
                    $file_path,
                    $filename,
                    [
                        'Content-Length: ' . filesize($file_path),
                    ]
                );
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __("File Not Exists."));
            }
        } else {
            return redirect()->back()->with('error', __('File is not exist.'));
        }
    }

    public function fileDelete($slug, $id, $file_id)
    {
        $project = Project::find($id);

        $file = ProjectFile::find($file_id);
        if ($file) {
            // $path = storage_path('project_files/' . $file->file_path);
            $logo = Utility::get_file('project_files/');
            $path = $logo . $file->file_path;
            if (file_exists($path)) {
                \File::delete($path);
            }
            $file->delete();

            return response()->json(['is_success' => true], 200);
        } else {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('File is not exist.'),
                ],
                200
            );
        }
    }

    // Timesheet
    public function timesheet($slug)
    {
        $project_id = '-1';

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $objUser = Auth::user();


        if ($currentWorkspace) {

            if ($objUser->getGuard() == 'client') {
                $timesheets = Timesheet::select('timesheets.*')->join('projects', 'projects.id', '=', 'timesheets.project_id')->join('tasks', 'tasks.id', '=', 'timesheets.task_id')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('client_projects.permission', 'LIKE', '%show timesheet%')->get();
            } elseif ($currentWorkspace->permission == 'Owner') {
                $timesheets = Timesheet::select('timesheets.*')->join('projects', 'projects.id', '=', 'timesheets.project_id')->join('tasks', 'tasks.id', '=', 'timesheets.task_id')->where('projects.workspace', '=', $currentWorkspace->id)->get();
            } else {
                $timesheets = Timesheet::select('timesheets.*')->join('projects', 'projects.id', '=', 'timesheets.project_id')->join('tasks', 'timesheets.task_id', '=', 'tasks.id')->where('projects.workspace', '=', $currentWorkspace->id)->whereRaw("find_in_set('" . $objUser->id . "',tasks.assign_to)")->get();
            }


            return view('projects.timesheet', compact('currentWorkspace', 'timesheets', 'project_id'));
        } else {
            return redirect()->back()->with('error', __('Workspace Not Found.'));
        }
    }

    public function timesheetCreate($slug)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $projects = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->get();

        return view('projects.timesheetCreate', compact('currentWorkspace', 'projects'));
    }

    public function getTask($slug, $project_id = null)
    {

        if ($project_id) {
            $currentWorkspace = Utility::getWorkspaceBySlug($slug);
            $objUser = Auth::user();
            if ($currentWorkspace->permission == 'Owner') {
                $tasks = Task::where('project_id', '=', $project_id)->get();
            } else {
                $tasks = Task::where('project_id', '=', $project_id)->whereRaw("find_in_set('" . $objUser->id . "',assign_to)")->get();
            }

            return response()->json($tasks);
        }
    }

    public function timesheetStore($slug, Request $request)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $request->validate(
            [
                'task_id' => 'required',
                'date' => 'required',
                'time' => 'required',
            ]
        );

        $timesheet = new Timesheet();
        $timesheet->project_id = $request->project_id;
        $timesheet->task_id = $request->task_id;
        $timesheet->date = $request->date;
        $timesheet->time = $request->time;
        $timesheet->description = $request->description;
        $timesheet->save();

        ActivityLog::create(
            [
                'user_id' => \Auth::user()->id,
                'user_type' => get_class(\Auth::user()),
                'project_id' => $request->project_id,
                'log_type' => 'Create Timesheet',
                'remark' => json_encode(['name' => \Auth::user()->name]),
            ]
        );

        return redirect()->back()->with('success', __('Timesheet Created Successfully!'));
    }

    public function timesheetEdit($slug, $timesheetID)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $projects = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->get();

        $timesheet = Timesheet::find($timesheetID);

        return view('projects.timesheetEdit', compact('currentWorkspace', 'timesheet', 'projects'));
    }

    public function timesheetUpdate($slug, $timesheetID, Request $request)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $request->validate(
            [
                'task_id' => 'required',
                'date' => 'required',
                'time' => 'required',
            ]
        );

        $timesheet = Timesheet::find($timesheetID);
        $timesheet->project_id = $request->project_id;
        $timesheet->task_id = $request->task_id;
        $timesheet->date = $request->date;
        $timesheet->time = $request->time;
        $timesheet->description = $request->description;
        $timesheet->save();

        return redirect()->back()->with('success', __('Timesheet Updated Successfully!'));
    }

    public function timesheetDestroy($slug, $timesheetID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $timesheet = Timesheet::find($timesheetID);
        $timesheet->delete();

        return redirect()->back()->with('success', __('Timesheet deleted Successfully!'));
    }

    public function clientPermission($slug, $project_id, $client_id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::find($project_id);
        $client = Client::find($client_id);
        $permissions = $client->getPermission($project_id);
        if (!$permissions) {
            $permissions = [];
        }

        return view('projects.client_permission', compact('currentWorkspace', 'project', 'client', 'permissions'));
    }

    public function clientPermissionStore($slug, $project_id, $client_id, Request $request)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $clientProject = ClientProject::where('client_id', '=', $client_id)->where('project_id', '=', $project_id)->first();
        $clientProject->permission = json_encode($request->permissions);
        $clientProject->save();

        return redirect()->back()->with('success', __('Permission Updated Successfully!'));
    }

    public function bugReport($slug, $project_id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $objUser = Auth::user();
        if ($objUser->getGuard() == 'client') {
            $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $project_id)->first();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $project_id)->first();
        }

        $stages = $statusClass = [];
        $permissions = Auth::user()->getPermission($project_id);

        if ($project && (isset($permissions) && in_array('show bug report', $permissions)) || (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner')) {
            $stages = BugStage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->get();

            foreach ($stages as &$status) {
                $statusClass[] = 'task-list-' . str_replace(' ', '_', $status->id);
                $bug = BugReport::where('project_id', '=', $project_id);
                if ($currentWorkspace->permission != 'Owner' && $objUser->getGuard() != 'client') {
                    if (isset($objUser) && $objUser) {
                        $bug->where('assign_to', '=', $objUser->id);
                    }
                }
                $bug->orderBy('order');

                $status['bugs'] = $bug->where('status', '=', $status->id)->get();
            }
            return view('projects.bug_report', compact('currentWorkspace', 'project', 'stages', 'statusClass'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function bugReportCreate($slug, $project_id)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($objUser->getGuard() == 'client') {
            $project = Project::where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $project_id)->first();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $project_id)->first();
        }
        $arrStatus = BugStage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->pluck('name', 'id')->all();
        $users = User::select('users.*')->join('user_projects', 'user_projects.user_id', '=', 'users.id')->where('project_id', '=', $project_id)->get();

        return view('projects.bug_report_create', compact('currentWorkspace', 'project', 'users', 'arrStatus'));
    }

    public function bugReportStore(Request $request, $slug, $project_id)
    {
        $request->validate(
            [
                'title' => 'required',
                'priority' => 'required',
                'assign_to' => 'required',
                'status' => 'required',
            ]
        );
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($objUser->getGuard() == 'client') {
            $project = Project::where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $project_id)->first();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $project_id)->first();
        }

        if ($project) {
            $post = $request->all();
            $post['project_id'] = $project_id;
            $bug = BugReport::create($post);

            ActivityLog::create(
                [
                    'user_id' => $objUser->id,
                    'user_type' => get_class($objUser),
                    'project_id' => $project_id,
                    'log_type' => 'Create Bug',
                    'remark' => json_encode(['title' => $bug->title]),
                ]
            );
            Utility::sendNotification('bug_assign', $currentWorkspace, $request->assign_to, $bug);

            return redirect()->back()->with('success', __('Bug Create Successfully!'));
        } else {
            return redirect()->back()->with('error', __("You can't Add Bug!"));
        }
    }

    public function bugReportOrderUpdate(Request $request, $slug, $project_id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if (isset($request->sort)) {
            foreach ($request->sort as $index => $taskID) {
                $bug = BugReport::find($taskID);
                $bug->order = $index;
                $bug->save();
            }
        }
        if ($request->new_status != $request->old_status) {
            $new_status = BugStage::find($request->new_status);
            $old_status = BugStage::find($request->old_status);
            $user = Auth::user();
            $bug = BugReport::find($request->id);
            $bug->status = $request->new_status;
            $bug->save();

            $name = $user->name;
            $id = $user->id;

            ActivityLog::create(
                [
                    'user_id' => $id,
                    'user_type' => get_class($user),
                    'project_id' => $project_id,
                    'log_type' => 'Move Bug',
                    'remark' => json_encode(
                        [
                            'title' => $bug->title,
                            'old_status' => $old_status->name,
                            'new_status' => $new_status->name,
                        ]
                    ),
                ]
            );

            return $bug->toJson();
        }
    }

    public function bugReportEdit($slug, $project_id, $bug_id)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($objUser->getGuard() == 'client') {
            $project = Project::where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $project_id)->first();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $project_id)->first();
        }
        $users = User::select('users.*')->join('user_projects', 'user_projects.user_id', '=', 'users.id')->where('project_id', '=', $project_id)->get();
        $bug = BugReport::find($bug_id);
        $arrStatus = BugStage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->pluck('name', 'id')->all();

        return view('projects.bug_report_edit', compact('currentWorkspace', 'project', 'users', 'bug', 'arrStatus'));
    }

    public function bugReportUpdate(Request $request, $slug, $project_id, $bug_id)
    {
        $request->validate(
            [
                'title' => 'required',
                'priority' => 'required',
                'assign_to' => 'required',
                'status' => 'required',
            ]
        );
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($objUser->getGuard() == 'client') {
            $project = Project::where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $project_id)->first();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $project_id)->first();
        }
        if ($project) {
            $post = $request->all();
            $bug = BugReport::find($bug_id);
            $bug->update($post);

            return redirect()->back()->with('success', __('Bug Updated Successfully!'));
        } else {
            return redirect()->back()->with('error', __("You can't Edit Bug!"));
        }
    }

    public function bugReportDestroy($slug, $project_id, $bug_id)
    {
        $objUser = Auth::user();
        $bug = BugReport::where('id', $bug_id)->delete();

        return redirect()->back()->with('success', __('Bug Deleted Successfully!'));
    }

    public function bugReportShow($slug, $project_id, $bug_id)
    {
        $project = Project::find($project_id);

        if(Auth::user() != null){
            $objUser         = Auth::user();
        }else{
            $objUser         = User::where('id',$project->created_by)->first();
        }


        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $bug = BugReport::find($bug_id);

        $clientID = '';
        if ($objUser->getGuard() == 'client') {
            $clientID = $objUser->id;
        }

        return view('projects.bug_report_show', compact('currentWorkspace', 'bug', 'clientID'));
    }

    public function bugCommentStore(Request $request, $slug, $project_id, $bugID, $clientID = '')
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $post = [];
        $post['bug_id'] = $bugID;
        $post['comment'] = $request->comment;
        if ($clientID) {
            $post['created_by'] = $clientID;
            $post['user_type'] = 'Client';
        } else {
            $post['created_by'] = Auth::user()->id;
            $post['user_type'] = 'User';
        }
        $comment = BugComment::create($post);
        if ($comment->user_type == 'Client') {
            $user = $comment->client;
        } else {
            $user = $comment->user;
        }
        if (empty($clientID)) {
            $comment->deleteUrl = route(
                'bug.comment.destroy',
                [
                    $currentWorkspace->slug,
                    $project_id,
                    $bugID,
                    $comment->id,
                ]
            );
        }

        return $comment->toJson();
    }

    public function bugCommentDestroy(Request $request, $slug, $project_id, $bug_id, $comment_id)
    {
        $comment = BugComment::find($comment_id);
        $comment->delete();

        return "true";
    }

    public function bugStoreFile(Request $request, $slug, $project_id, $bug_id, $clientID = '')
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $request->validate(['file' => 'required|mimes:zip,rar,jpeg,jpg,png,gif,svg,pdf,txt,doc,docx,application/octet-stream,audio/mpeg,mpga,mp3,wav|max:204800']);
        $fileName = $bug_id . time() . "_" . $request->file->getClientOriginalName();
        $request->file->storeAs('tasks', $fileName);
        $post['bug_id']    = $bug_id;
        $post['file']      = $fileName;
        $post['name']      = $request->file->getClientOriginalName();
        $post['extension'] = "." . $request->file->getClientOriginalExtension();
        $post['file_size'] = round(($request->file->getMaxFilesize() / 1024) / 1024, 2) . ' MB';
        if ($clientID) {
            $post['created_by'] = $clientID;
            $post['user_type']  = 'Client';
        } else {
            $post['created_by'] = Auth::user()->id;
            $post['user_type']  = 'User';
        }
        $TaskFile            = BugFile::create($post);
        $user                = $TaskFile->user;
        $TaskFile->deleteUrl = '';
        if (empty($clientID)) {
            $TaskFile->deleteUrl = route(
                'bug.comment.destroy.file',
                [
                    $currentWorkspace->slug,
                    $project_id,
                    $bug_id,
                    $TaskFile->id,
                ]
            );
        }

        return $TaskFile->toJson();
    }

    public function bugDestroyFile(Request $request, $slug, $project_id, $bug_id, $file_id)
    {
        $commentFile = BugFile::find($file_id);
        if ($commentFile) {
            // $path = storage_path('tasks/' . $commentFile->file);
            $logo = Utility::get_file('tasks/');
            $path =  $logo . $commentFile->file;
            if (file_exists($path)) {
                \File::delete($path);
            }
            $commentFile->delete();

            return "true";
        } else {
            return "false";
        }
    }

    public function allTasks($slug)
    {
        // dd('asd');
        $userObj = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $workspace_type = WorkspaceType::get();

        if ($userObj->getGuard() == 'client') {
            $projects = Project::select('projects.*')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')->where('client_projects.client_id', '=', $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->get();
        }


        else {
            $projects = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->get();
        }
        $stages = Stage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->get();
        $users = User::select('users.*')->join('user_workspaces', 'user_workspaces.user_id', '=', 'users.id')->where('user_workspaces.workspace_id', '=', $currentWorkspace->id)->get();

        return view('projects.tasks', compact('currentWorkspace', 'projects', 'users', 'stages','workspace_type'));
    }

    public function ajax_tasks($slug, Request $request)
    {




        $userObj = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);


        if (auth()->user()->hasRole('HOD')) {

            $hodRole = Role::where('name', 'HOD')->first();
            if ($hodRole) {
                // Find the 'HOD' role for the authenticated user
                $tags = ModelHasRole::where('model_id', Auth::id())
                    ->where('role_id', $hodRole->id)
                    ->first();


                    if ($tags) {
                        $tagsArray = json_decode($tags->tag, true);

                        // dd($tagsArray);



                        if (isset($tagsArray)) {


                            // $tasks = Task::where(function ($query) use ($tagsArray) {
                            //     if (!empty($tagsArray)) {
                            //         foreach ($tagsArray as $tag) {
                            //             $query->orWhereJsonContains('tags', $tag);
                            //         }
                            //     }
                            // })->get();

                            $tasks = Task::where(function ($query) use ($tagsArray) {
                                if (!empty($tagsArray)) {
                                    foreach ($tagsArray as $tag) {
                                        $query->orWhere(function ($subQuery) use ($tag) {
                                            $subQuery->whereNotNull('tags') // Ensure 'tags' is not NULL
                                                ->where('tags', '!=', '')    // Ensure 'tags' is not empty
                                                ->whereJsonContains('tags', $tag);
                                        });
                                    }
                                }
                            })->get();



                            // dd($tasks);


                        }

                        else{
                            $tasks = Task::get();
                        }

                        // dd($projects);
                    } else {
                    // Handle the case where no tags are found for the 'HOD' role
                    $tasks = [];
                }
            } else {
                // Handle the case where the 'HOD' role does not exist
                $tasks = [];
            }
        }


        if ($currentWorkspace->permission == 'Owner') {
            $tasks = Task::select(
                [
                    'tasks.*',
                    'stages.name as stage',
                    'stages.complete',
                ]
            )->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id);
        }



        else {
            $tasks = Task::select(
                [
                    'tasks.*',
                    'stages.name as stage',
                    'stages.complete',
                ]
            )->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)");
        }


        if ($request->project) {
            $tasks->where('tasks.project_id', '=', $request->project);
        }
        if ($request->assign_to) {
            $tasks->whereRaw("find_in_set('" . $request->assign_to . "',assign_to)");
        }
        if ($request->due_date_order) {
            if ($request->due_date_order == 'today') {

                $tasks->whereDate('due_date', Carbon::today());
            } else if ($request->due_date_order == 'expired') {

                $tasks->whereDate('due_date', '<', Carbon::today());
            } else if ($request->due_date_order == 'in_7_days') {

                $tasks->where(['due_date' => Carbon::parse()->between(Carbon::now(), Carbon::now()->addDays(7))]);
            } else {

                $sort = explode(',', $request->due_date_order);

                $tasks->orderBy($sort[0], $sort[1]);
            }
        }
        if ($request->priority) {
            $tasks->where('priority', '=', $request->priority);
        }
        if ($request->status) {
            $tasks->where('tasks.status', '=', $request->status);
        }
        if ($request->start_date && $request->end_date) {
            $tasks->whereBetween(
                'tasks.due_date',
                [
                    $request->start_date,
                    $request->end_date,
                ]
            );
        }
        $tasks = $tasks->get();


        $data = [];
        foreach ($tasks as $task) {
            $tmp = [];
            $tmp['title'] = '<a href="' . route(
                'projects.task.board',
                [
                    $currentWorkspace->slug,
                    $task->project_id,
                ]
            ) . '" class="text-body">' . $task->title . '</a>';
            $tmp['project_name'] = $task->project->name;
            // $tmp['milestone'] = ($milestone = $task->milestone()) ? $milestone->title : '';

            $due_date = '<span class="text-' . (($task->due_date < date('Y-m-d') && $task->stage != 'Done' ) ? 'red' : 'success') . '">' . date('Y-m-d', strtotime($task->due_date)) . '</span> ';
            $tmp['due_date'] = $due_date;

            if ($currentWorkspace->permission == 'Owner' || Auth::user()->getGuard() == 'client') {
                $tmp['user_name'] = "";
                foreach ($task->users() as $user) {
                    if (isset($user) && $user) {
                        $tmp['user_name'] .= '<span class="badge bg-secondary p-2 px-3 rounded">' . $user->name . '</span> ';
                    }
                }
            }

            if ($task->complete == 1) {
                $tmp['status'] = '<span class="status_badge badge bg-success p-2 px-3 rounded">' . __($task->stage) . '</span>';
            } else {
                $tmp['status'] = '<span class="status_badge badge bg-primary p-2 px-3 rounded">' . __($task->stage) . '</span>';
            }

            if ($task->priority == "High") {
                $tmp['priority'] = '<span class="priority_badge badge bg-danger p-2 px-3 rounded">' . __('High') . '</span>';
            } elseif ($task->priority == "Medium") {
                $tmp['priority'] = '<span class="priority_badge badge bg-info p-2 px-3 rounded">' . __('Medium') . '</span>';
            } else {
                $tmp['priority'] = '<span class="priority_badge badge bg-success p-2 px-3 rounded">' . __('Low') . '</span>';
            }

            if ($currentWorkspace->permission == 'Owner') {
                $tmp['action'] = '
                <a href="#" class="action-btn btn-info  btn btn-sm d-inline-flex align-items-center"  data-toggle="popover"  title="' . __('Edit Task') . '"  data-ajax-popup="true" data-size="lg" data-title="' . __('Edit Task') . '" data-url="' . route(
                    'tasks.edit',
                    [
                        $currentWorkspace->slug,
                        $task->project_id,
                        $task->id,
                    ]
                ) . '"><i class="ti ti-pencil"></i></a>
                <a href="#" class="action-btn btn-danger  btn btn-sm d-inline-flex align-items-center bs-pass-para" data-toggle="popover" title="' . __('Delete') . '" data-confirm="' . __('Are You Sure?') . '" data-confirm-yes="delete-form-' . $task->id . '">
                    <i class="ti ti-trash"></i></a>
                <form id="delete-form-' . $task->id . '" action="' . route(
                    'tasks.destroy',
                    [
                        $currentWorkspace->slug,
                        $task->project_id,
                        $task->id,
                    ]
                ) . '" method="POST" style="display: none;">
                                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                                            <input type="hidden" name="_method" value="DELETE">
                                        </form>';
            }
            $data[] = array_values($tmp);
        }
        return response()->json(['data' => $data], 200);
    }

    public function gantt($slug, $projectID, $duration = 'Week')
    {
        $objUser = Auth::user();
        $workspace_type = WorkspaceType::get();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $is_client = '';

        if ($objUser->getGuard() == 'client') {
            $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            $is_client = 'client.';
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
        }
        $tasks = [];
        $permissions = Auth::user()->getPermission($projectID);

        if ($project && (isset($permissions) && in_array('show gantt', $permissions)) || (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner')) {
            if ($objUser->getGuard() == 'client' || $currentWorkspace->permission == 'Owner') {
                $tasksobj = Task::where('project_id', '=', $project->id)->orderBy('start_date')->get();
            } else {
                $tasksobj = Task::where('project_id', '=', $project->id)->where('assign_to', '=', $objUser->id)->orderBy('start_date')->get();
            }
            foreach ($tasksobj as $task) {
                $tmp = [];
                $tmp['id'] = 'task_' . $task->id;
                $tmp['name'] = $task->title;
                $tmp['start'] = $task->start_date;
                $tmp['end'] = $task->due_date;
                $tmp['custom_class'] = strtolower($task->priority);
                $tmp['progress'] = $task->subTaskPercentage();
                $tmp['extra'] = [
                    'priority' => __($task->priority),
                    'comments' => count($task->comments),
                    'duration' => Date::parse($task->start_date)->format('d M Y H:i A') . ' - ' . Date::parse($task->due_date)->format('d M Y H:i A'),
                ];
                $tasks[] = $tmp;
            }
        }

        return view('projects.gantt', compact('currentWorkspace', 'project', 'tasks', 'duration', 'is_client','workspace_type'));
    }

    public function ganttPost($slug, $projectID, Request $request)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($objUser->getGuard() == 'client') {
            $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
        }
        if ($project) {
            if ($objUser->getGuard() == 'client' || $currentWorkspace->permission == 'Owner') {
                $id = trim($request->task_id, 'task_');
                $task = Task::find($id);
                $task->start_date = $request->start;
                $task->due_date = $request->end;
                $task->save();

                return response()->json(
                    [
                        'is_success' => true,
                        'message' => __("Time Updated"),
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'is_success' => false,
                        'message' => __("You can't change Date!"),
                    ],
                    400
                );
            }
        } else {
            return response()->json(
                [
                    'is_success' => false,
                    'message' => __("Something is wrong."),
                ],
                400
            );
        }
    }

    public function projectsTimesheet(Request $request, $slug, $project_id = 0)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        return view('projects.timesheet', compact('currentWorkspace', 'project_id'));
    }

    public function filterTimesheetTableView(Request $request, $slug)
    {
        $tasks = [];
        $project = Project::find($request->project_id);

        if(Auth::user() != null){
            $objUser         = Auth::user();
        }else{
            if(isset($project)){
                $objUser         = User::where('id',$project->created_by)->first();
            }
        }

        $week = $request->week;
        $project_id = $request->project_id;


        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($request->has('week')) {
            if ($objUser->getGuard() == 'client') {
                $timesheets = Timesheet::select('timesheets.*')->join('projects', 'projects.id', '=', 'timesheets.project_id')->join('tasks', 'tasks.id', '=', 'timesheets.task_id')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('client_projects.permission', 'LIKE', '%show timesheet%');
            } elseif ($currentWorkspace->permission == 'Owner') {
                $timesheets = Timesheet::select('timesheets.*')->join('projects', 'projects.id', '=', 'timesheets.project_id')->join('tasks', 'tasks.id', '=', 'timesheets.task_id')->where('projects.workspace', '=', $currentWorkspace->id);
            } else {
                $timesheets = Timesheet::select('timesheets.*')->join('projects', 'projects.id', '=', 'timesheets.project_id')->join('tasks', 'timesheets.task_id', '=', 'tasks.id')->where('projects.workspace', '=', $currentWorkspace->id)->whereRaw("find_in_set('" . $objUser->id . "',tasks.assign_to)");
            }

            $days = Utility::getFirstSeventhWeekDay($week);

            $first_day = $days['first_day'];
            $seventh_day = $days['seventh_day'];

            $onewWeekDate = $first_day->format('M d') . ' - ' . $seventh_day->format('M d, Y');
            $selectedDate = $first_day->format('Y-m-d') . ' - ' . $seventh_day->format('Y-m-d');

            $timesheets = $timesheets->whereDate('date', '>=', $first_day->format('Y-m-d'))->whereDate('date', '<=', $seventh_day->format('Y-m-d'));

            if ($project_id == '-1') {
                $timesheets = $timesheets->get()->groupBy(
                    [
                        'project_id',
                        'task_id',
                    ]
                )->toArray();
            } else {
                $timesheets = $timesheets->where('projects.id', $project_id)->get()->groupBy('task_id')->toArray();
            }

            $task_ids = array_keys($timesheets);

            $returnHTML = Project::getProjectAssignedTimesheetHTML($currentWorkspace, $timesheets, $days, $project_id);

            $totalrecords = count($timesheets);

            if ($project_id != '-1') {
                if ($objUser->getGuard() == 'client') {
                    $projects = Project::select('projects.*')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->get();
                } else {
                    $projects = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->get();
                }

                if ($currentWorkspace->permission == 'Owner') {

                    $tasks = Task::where('project_id', '=', $project_id)->whereNotIn('id', $task_ids)->pluck('title', 'id');
                } else {
                    $tasks = Task::where('project_id', '=', $project_id)->whereNotIn('id', $task_ids)->whereRaw("find_in_set('" . $objUser->id . "',assign_to)")->pluck('title', 'id');
                }
            }

            return response()->json(
                [
                    'success' => true,
                    'totalrecords' => $totalrecords,
                    'selectedDate' => $selectedDate,
                    'tasks' => $tasks,
                    'onewWeekDate' => $onewWeekDate,
                    'html' => $returnHTML,
                ]
            );
        }
    }

    public function appendTimesheetTaskHTML(Request $request, $slug)
    {
        $project_id = $request->has('project_id') ? $request->project_id : null;
        $task_id = $request->has('task_id') ? $request->task_id : null;
        $selected_dates = $request->has('selected_dates') ? $request->selected_dates : null;

        $returnHTML = '';

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $project = Project::find($project_id);

        if ($project) {
            $task = Task::find($task_id);

            if ($task && $selected_dates) {
                $twoDates = explode(' - ', $selected_dates);

                $first_day = $twoDates[0];
                $seventh_day = $twoDates[1];

                $period = CarbonPeriod::create($first_day, $seventh_day);

                $returnHTML .= '<tr><td><span class="task-name ml-3">' . $task->title . '</span></td>';

                foreach ($period as $key => $dateobj) {
                    $returnHTML .= '<td><div role="button" class="form-control border-dark wid-120" data-ajax-timesheet-popup="true" data-type="create" data-task-id="' . $task->id . '" data-date="' . $dateobj->format('Y-m-d') . '" data-url="' . route(
                        'project.timesheet.create',
                        [
                            'slug' => $currentWorkspace->slug,
                            'project_id' => $project_id,
                        ]
                    ) . '">00:00</div></td>';
                }

                $returnHTML .= '<td><div  role="button"class="total form-control border-dark wid-120">00:00</div></td></tr>';
            }
        }

        return response()->json(
            [
                'success' => true,
                'html' => $returnHTML,
            ]
        );
    }

    public function projectTimesheetCreate(Request $request, $slug, $project_id)
    {
        $parseArray = [];

        $objUser = Auth::user();

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $project_id = $request->has('project_id') ? $request->project_id : null;
        $task_id = $request->has('task_id') ? $request->task_id : null;
        $selected_date = $request->has('date') ? $request->date : null;
        $user_id = $request->has('user_id') ? $request->user_id : null;

        $created_by = $user_id != null ? $user_id : $objUser->id;

        if ($objUser->getGuard() == 'client') {
            $projects = Project::select('projects.*')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id);
        } else {
            $projects = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id);
        }

        if ($project_id) {
            $project = $projects->where('projects.id', '=', $project_id)->pluck('projects.name', 'projects.id')->all();

            if (!empty($project) && count($project) > 0) {
                $project_id = key($project);
                $project_name = $project[$project_id];

                $task = Task::where(
                    [
                        'project_id' => $project_id,
                        'id' => $task_id,
                    ]
                )->pluck('title', 'id')->all();

                $task_id = key($task);
                $task_name = $task[$task_id];

                $tasktime = Timesheet::where('task_id', $task_id)->where('created_by', $created_by)->pluck('time')->toArray();

                $totaltasktime = Utility::calculateTimesheetHours($tasktime);

                $totalhourstimes = explode(':', $totaltasktime);

                $totaltaskhour = $totalhourstimes[0];
                $totaltaskminute = $totalhourstimes[1];

                $parseArray = [
                    'project_id' => $project_id,
                    'project_name' => $project_name,
                    'task_id' => $task_id,
                    'task_name' => $task_name,
                    'date' => $selected_date,
                    'totaltaskhour' => $totaltaskhour,
                    'totaltaskminute' => $totaltaskminute,
                ];

                return view('projects.timesheet-create', compact('currentWorkspace', 'parseArray'));
            }
        } else {
            $projects = $projects->get();

            return view('projects.timesheet-create', compact('currentWorkspace', 'projects', 'project_id', 'selected_date'));
        }
    }

    public function projectTimesheetStore(Request $request, $slug, $project_id)
    {
        $objUser = Auth::user();
        $project = Project::find($request->project_id);

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($project) {
            $request->validate(
                [
                    'date' => 'required',
                    'time_hour' => 'required',
                    'time_minute' => 'required',
                ]
            );

            $hour = $request->time_hour;
            $minute = $request->time_minute;

            $time = ($hour != '' ? ($hour < 10 ? '0' + $hour : $hour) : '00') . ':' . ($minute != '' ? ($minute < 10 ? '0' + $minute : $minute) : '00');

            $timesheet = new Timesheet();
            $timesheet->project_id = $request->project_id;
            $timesheet->task_id = $request->task_id;
            $timesheet->date = $request->date;
            $timesheet->time = $time;
            $timesheet->description = $request->description;
            $timesheet->created_by = $objUser->id;
            $timesheet->save();

            return redirect()->back()->with('success', __('Timesheet Created Successfully!'));
        }
    }

    public function projectTimesheetEdit(Request $request, $slug, $timesheet_id, $project_id)
    {

        $objUser = Auth::user();

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $task_id = $request->has('task_id') ? $request->task_id : null;

        $user_id = $request->has('date') ? $request->user_id : null;
        $created_by = $user_id != null ? $user_id : $objUser->id;

        $project_view = '';

        if ($request->has('project_view')) {
            $project_view = $request->project_view;
        }

        if ($objUser->getGuard() == 'client') {
            $projects = Project::select('projects.*')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id);
        } else {
            $projects = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id);
        }

        $timesheet = Timesheet::find($timesheet_id);

        if ($timesheet) {
            $project = $projects->where('projects.id', '=', $project_id)->pluck('projects.name', 'projects.id')->all();

            if (!empty($project) && count($project) > 0) {
                $project_id = key($project);
                $project_name = $project[$project_id];

                $task = Task::where(
                    [
                        'project_id' => $project_id,
                        'id' => $task_id,
                    ]
                )->pluck('title', 'id')->all();

                $task_id = key($task);
                $task_name = $task[$task_id];

                $tasktime = Timesheet::where('task_id', $task_id)->where('created_by', $created_by)->pluck('time')->toArray();

                $totaltasktime = Utility::calculateTimesheetHours($tasktime);

                $totalhourstimes = explode(':', $totaltasktime);

                $totaltaskhour = $totalhourstimes[0];
                $totaltaskminute = $totalhourstimes[1];

                $time = explode(':', $timesheet->time);

                $parseArray = [
                    'project_id' => $project_id,
                    'project_name' => $project_name,
                    'task_id' => $task_id,
                    'task_name' => $task_name,
                    'time_hour' => $time[0] < 10 ? $time[0] : $time[0],
                    'time_minute' => $time[1] < 10 ? $time[1] : $time[1],
                    'totaltaskhour' => $totaltaskhour,
                    'totaltaskminute' => $totaltaskminute,
                ];

                return view('projects.timesheet-edit', compact('timesheet', 'currentWorkspace', 'parseArray', 'project_id'));
            }
        }
    }

    public function projectTimesheetUpdate(Request $request, $slug, $timesheet_id, $project_id)
    {
        $project = Project::find($request->project_id);

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($project) {
            $request->validate(
                [
                    'date' => 'required',
                    'time_hour' => 'required',
                    'time_minute' => 'required',
                ]
            );

            $hour = $request->time_hour;
            $minute = $request->time_minute;

            $time = ($hour != '' ? ($hour < 10 ? '0' + $hour : $hour) : '00') . ':' . ($minute != '' ? ($minute < 10 ? '0' + $minute : $minute) : '00');

            $timesheet = Timesheet::find($timesheet_id);
            $timesheet->project_id = $request->project_id;
            $timesheet->task_id = $request->task_id;
            $timesheet->date = $request->date;
            $timesheet->time = $time;
            $timesheet->description = $request->description;
            $timesheet->save();

            return redirect()->back()->with('success', __('Timesheet Updated Successfully!'));
        }
    }
    public function members($slug, $id)
    {

        $project = Project::with('users')->find($id);
        $members = $project->users;
        $data = [];
        foreach ($members as $key => $member) {
            $data[$key]['id'] = $member->id;
            $data[$key]['name'] = $member->name;
        }
        return $data;
    }


    public function copyproject($slug,$id)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $id)->first();

        return view('projects.copy', compact('currentWorkspace', 'project'));
    }

    public function copyprojectstore(Request $request,$slug,$id)
    {
        $project                          = Project::find($id);
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $duplicate                          = new Project();
        $duplicate['name']                  = $project->name;
        $duplicate['status']                = $project->status;
        $duplicate['description']           = $project->description;
        $duplicate['start_date']            = $project->start_date;
        $duplicate['end_date']              = $project->end_date;
        $duplicate['budget']                = $project->budget;
        $duplicate['workspace']             = $currentWorkspace->id;
        $duplicate['created_by']            = $project->created_by;
        $duplicate['is_active']             = $project->is_active;
        $duplicate->save();


        if(isset($request->user) && in_array("user", $request->user)){
            $users = UserProject::where('project_id',$project->id)->get();
            foreach($users as $user){
                $users = new UserProject();
                $users['user_id'] = $user->user_id;
                $users['project_id'] = $duplicate->id;
                $users['is_active'] = $user->is_active;
                $users['permission'] = $user->permission;
                $users->save();
            }
        }
        else{
            $objUser = Auth::user();
            $users              = new UserProject();
            $users['user_id']   = $objUser->id;
            $users['project_id']= $duplicate->id;
            $users->save();
        }

        if(isset($request->client) && in_array("client", $request->client)){
            $clients = ClientProject::where('project_id',$project->id)->get();
            foreach($clients as $client){
                $clients = new ClientProject();
                $clients['client_id']   = $client->client_id;
                $clients['project_id']  = $duplicate->id;
                $clients['is_active']   = $client->is_active;
                $clients['permission']  = $client->permission;
                $clients->save();
            }
        }

        if(isset($request->task) && in_array("task", $request->task)){
            $tasks = Task::where('project_id',$project->id)->get();
            foreach($tasks as $task){
                $project_task                   = new Task();
                $project_task['title']          = $task->title;
                $project_task['priority']       = $task->priority;
                $project_task['description']    = $task->description;
                $project_task['start_date']     = $task->start_date;
                $project_task['due_date']       = $task->due_date;
                $project_task['assign_to']      = $task->assign_to;
                $project_task['project_id']     = $duplicate->id;
                $project_task['milestone_id']   = $task->milestone_id;
                $project_task['status']         = $task->status;
                $project_task['order']          = $task->order;
                $project_task->save();

                if(in_array("sub_task",$request->task)){
                    $sub_tasks = SubTask::where('task_id',$task->id)->get();
                    foreach($sub_tasks as $sub_task){
                        $subtask                = new SubTask();
                        $subtask['name']        = $sub_task->name;
                        $subtask['due_date']    = $sub_task->due_date;
                        $subtask['task_id']     = $project_task->id;
                        $subtask['user_type']   = $sub_task->user_type;
                        $subtask['created_by']  = $sub_task->created_by;
                        $subtask['status']      = $sub_task->status;
                        $subtask->save();
                    }

                }
                if(in_array("task_comment",$request->task)){
                    $task_comments = Comment::where('task_id',$task->id)->get();
                    foreach($task_comments as $task_comment){
                        $comment                = new Comment();
                        $comment['comment']     = $task_comment->comment;
                        $comment['created_by']  = $task_comment->created_by;
                        $comment['task_id']     = $project_task->id;
                        $comment['user_type']   = $task_comment->user_type;
                        $comment->save();
                    }

                }
                if(in_array("task_files",$request->task)){
                    $task_files = TaskFile::where('task_id',$task->id)->get();
                    foreach($task_files as $task_file){
                        $file               = new TaskFile();
                        $file['file']       = $task_file->file;
                        $file['name']       = $task_file->name;
                        $file['extension']  = $task_file->extension;
                        $file['file_size']  = $task_file->file_size;
                        $file['created_by'] = $task_file->created_by;
                        $file['task_id']    = $project_task->id;
                        $file['user_type']  = $task_file->user_type;
                        $file->save();
                    }
                }
            }
        }

        if(isset($request->bug) && in_array("bug", $request->bug)){
            $bugs = BugReport::where('project_id',$project->id)->get();
            foreach($bugs as $bug){
                $project_bug                   = new BugReport();
                $project_bug['title']          = $bug->title;
                $project_bug['priority']       = $bug->priority;
                $project_bug['description']    = $bug->description;
                $project_bug['assign_to']      = $bug->assign_to;
                $project_bug['project_id']     = $duplicate->id;
                $project_bug['status']         = $bug->status;
                $project_bug['order']          = $bug->order;
                $project_bug->save();

                if(in_array("bug_comment",$request->bug)){
                    $bug_comments = BugComment::where('bug_id',$bug->id)->get();
                    foreach($bug_comments as $bug_comment){
                        $bugcomment                 = new BugComment();
                        $bugcomment['comment']      = $bug_comment->comment;
                        $bugcomment['created_by']   = $bug_comment->created_by;
                        $bugcomment['bug_id']       = $project_bug->id;
                        $bugcomment['user_type']    = $bug_comment->user_type;
                        $bugcomment->save();
                    }

                }
                if(in_array("bug_files",$request->bug)){
                    $bug_files = BugFile::where('bug_id',$bug->id)->get();
                    foreach($bug_files as $bug_file){
                        $bugfile               = new BugFile();
                        $bugfile['file']       = $bug_file->file;
                        $bugfile['name']       = $bug_file->name;
                        $bugfile['extension']  = $bug_file->extension;
                        $bugfile['file_size']  = $bug_file->file_size;
                        $bugfile['created_by'] = $bug_file->created_by;
                        $bugfile['bug_id']     = $project_bug->id;
                        $bugfile['user_type']  = $bug_file->user_type;
                        $bugfile->save();
                    }
                }
            }
        }
        if(isset($request->milestone) && in_array("milestone", $request->milestone)){
            $milestones = Milestone::where('project_id',$project->id)->get();
            foreach ($milestones as $milestone) {
                $post                   = new Milestone();
                $post['project_id']     = $duplicate->id;
                $post['title']          = $milestone->title;
                $post['status']         = $milestone->status;
                $post['cost']           = $milestone->cost;
                $post['summary']        = $milestone->summary;
                $post['progress']       = $milestone->progress;
                $post['start_date']     = $milestone->start_date;
                $post['end_date']       = $milestone->end_date;
                $post->save();
            }
        }
        if(isset($request->project_file) && in_array("project_file",$request->project_file)){
            $project_files = ProjectFile::where('project_id',$project->id)->get();
            foreach ($project_files as $project_file) {
                $ProjectFile                = new ProjectFile();
                $ProjectFile['project_id']  = $duplicate->id;
                $ProjectFile['file_name']   = $project_file->file_name;
                $ProjectFile['file_path']   = $project_file->file_path;
                $ProjectFile->save();
            }
        }
        if(isset($request->activity) && in_array('activity',$request->activity))
        {
            $where_in_array = [];
            if( isset($request->milestone) && in_array("milestone", $request->milestone))
            {
                array_push($where_in_array,"Create Milestone");
            }
            if(isset($request->task) && in_array("task", $request->task))
            {
                array_push($where_in_array,"Create Task","Move");
            }
            if(isset($request->bug) && in_array("bug", $request->bug))
            {
                array_push($where_in_array,"Create Bug","Move Bug");
            }
            if(isset($request->client) && in_array("client", $request->client))
            {
                array_push($where_in_array,"Share with Client");
            }
            if(isset($request->user) && in_array("user", $request->user))
            {
                array_push($where_in_array,"Invite User");
            }
            if(isset($request->project_file) && in_array("project_file", $request->project_file))
            {
                array_push($where_in_array,"Upload File");
            }
            if(count($where_in_array) > 0)
            {
                $activities = ActivityLog::where('project_id',$project->id)->whereIn('log_type',$where_in_array)->get();
                foreach($activities as $activity){
                    $activitylog                = new ActivityLog();
                    $activitylog['user_id']     = $activity->user_id;
                    $activitylog['user_type']   = $activity->user_type;
                    $activitylog['project_id']  = $duplicate->id;
                    $activitylog['log_type']    = $activity->log_type;
                    $activitylog['remark']      = $activity->remark;
                    $activitylog->save();
                }
            }
        }
        return redirect()->back()->with('success', 'Project Created Successfully');
    }


    // Project copy links functions


    // public function projectPassCheck(Request $request, $slug  , $id )
    // {


    //     $id=\Illuminate\Support\Facades\Crypt::decrypt($id);
    //     $project = Project::find($id);
    //     if ( ($request->password == base64_decode($project->password))) {
    //         $ps_status = base64_encode('true');
    //         return redirect()->route('projects.link',[$id,$ps_status,$slug]);
    //     }
    //     else{
    //         $ps_status = base64_encode('false');
    //         return redirect()->route('projects.link',[$id,$ps_status,$slug ]);
    //     }
    // }

    public function projectlink(Request $request, $slug , $projectID ,$lang='')
    {

        $projectID=\Illuminate\Support\Facades\Crypt::decrypt($projectID);
        $project = Project::find($projectID);

        if(Auth::user() != null){
            $objUser         = Auth::user();
        }else{
            $objUser         = User::where('id',$project->created_by)->first();
        }
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        $lang = !empty($lang) ? $lang : (!empty($currentWorkspace->lang) ? $currentWorkspace->lang : env('DEFAULT_ADMIN_LANG')) ;
        \App::setLocale($lang);


        $data = [];
        $data['basic_details']  = isset($request->basic_details) ? 'on' : 'off';
        $data['member']  = isset($request->member) ? 'on' : 'off';
        $data['milestone']  = isset($request->milestone) ? 'on' : 'off';
        $data['activity']  = isset($request->activity) ? 'on' : 'off';
        $data['attachment']  = isset($request->attachment) ? 'on' : 'off';
        $data['bug_report']  = isset($request->bug_report) ? 'on' : 'off';
        $data['task']  = isset($request->task) ? 'on' : 'off';
        $data['tracker_details']  = isset($request->tracker_details) ? 'on' : 'off';
        $data['timesheet']  = isset($request->timesheet) ? 'on' : 'off';
        $data['password_protected']  = isset($request->password_protected) ? 'on' : 'off';




        if ($objUser->getGuard() == 'client') {
            $project = Project::select('projects.*')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
        }

        if ($project) {

            //chartdata
            $chartData = $this->getProjectChart(
                [
                    'workspace_id' => $currentWorkspace->id,
                    'project_id' => $projectID,
                    'duration' => 'week',
                ]
            );
            if(date('Y-m-d') == $project->end_date || date('Y-m-d') >= $project->end_date){
                $daysleft = 0;
            }
            else{
                $daysleft = round((((strtotime($project->end_date) - strtotime(date('Y-m-d'))) / 24) / 60) / 60);
            }


            //treckers
            $treckers = TimeTracker::where('project_id', $projectID)->get();


            //taskboard

            $stages_task = $statusClass_task = [];

            $permissions = $objUser->getPermission($projectID);

            if ($project && (isset($permissions) && in_array('show task', $permissions)) || (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner')) {
                $stages_task = Stage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->get();

                foreach ($stages_task as &$status) {
                    $statusClass_task[] = 'task-list-' . str_replace(' ', '_', $status->id);
                    $task = Task::where('project_id', '=', $projectID);
                    // if ($currentWorkspace->permission != 'Owner' && $objUser->getGuard() != 'client') {
                    //     if (isset($objUser) && $objUser) {
                    //         $task->whereRaw("find_in_set('" . $objUser->id . "',assign_to)");
                    //     }
                    // }
                    $task->orderBy('order');
                    $status['tasks'] = $task->where('status', '=', $status->id)->get();
                }
            }


            //Bug Report
            $stages_bug = $statusClass_bug = [];
            $permissions = $objUser->getPermission($projectID);

            if ($project && (isset($permissions) && in_array('show bug report', $permissions)) || (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner')) {
                $stages_bug = BugStage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->get();

                foreach ($stages_bug as &$status) {
                    $statusClass_bug[] = 'task-list-' . str_replace(' ', '_', $status->id);
                    $bug = BugReport::where('project_id', '=', $projectID);
                    // if ($currentWorkspace->permission != 'Owner' && $objUser->getGuard() != 'client') {
                    //     if (isset($objUser) && $objUser) {
                    //         $bug->where('assign_to', '=', $objUser->id);
                    //     }
                    // }
                    $bug->orderBy('order');

                    $status['bugs'] = $bug->where('status', '=', $status->id)->get();
                }
            }
            $data = $request->session()->all();
            if(\Session::get('copy_pass_true'. $projectID) == $project->password . '-' . $projectID){

                return view('projects.copylink', compact('currentWorkspace','data','project', 'chartData', 'daysleft','treckers', 'stages_task','stages_bug', 'statusClass_task', 'statusClass_bug','lang'));
            }else{

                if(isset(json_decode($project->copylinksetting)->password_protected) && json_decode($project->copylinksetting)->password_protected == 'off' )
                {
                    return view('projects.copylink', compact('currentWorkspace','data','project', 'chartData', 'daysleft','treckers', 'stages_task','stages_bug', 'statusClass_task', 'statusClass_bug','lang'));

                }elseif(isset(json_decode($project->copylinksetting)->password_protected) && json_decode($project->copylinksetting)->password_protected == 'on' && $request->password == base64_decode($project->password)){

                    \Session::put('copy_pass_true'.$projectID, $project->password . '-' . $projectID);

                    return view('projects.copylink', compact('currentWorkspace','data','project', 'chartData', 'daysleft','treckers', 'stages_task','stages_bug', 'statusClass_task', 'statusClass_bug','lang'));

                }else{

                    return view('projects.copylink_password', compact('projectID' , 'slug','currentWorkspace'));
                }
            }



        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }



    }

    public function copylink_setting_create($slug , $projectID)
    {
        $project = Project::find($projectID);
        if(Auth::user() != null){
            $objUser         = Auth::user();
        }else{
            $objUser         = User::where('id',$project->created_by)->first();
        }

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
        $result = json_decode($project->copylinksetting);


        return view('projects.copylink_setting', compact('currentWorkspace','project','projectID','slug','result'));
    }

    public function copylinksetting(Request $request, $id, $slug  )
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $objUser = Auth::user();
        // $id = Crypt::decryptString($id);

        $data = [];
        $data['basic_details']  = isset($request->basic_details) ? 'on' : 'off';
        $data['member']  = isset($request->member) ? 'on' : 'off';
        $data['milestone']  = isset($request->milestone) ? 'on' : 'off';
        $data['client']  = isset($request->client) ? 'on' : 'off';
        $data['progress']  = isset($request->progress) ? 'on' : 'off';
        $data['activity']  = isset($request->activity) ? 'on' : 'off';
        $data['attachment']  = isset($request->attachment) ? 'on' : 'off';
        $data['bug_report']  = isset($request->bug_report) ? 'on' : 'off';
        $data['task']  = isset($request->task) ? 'on' : 'off';
        $data['tracker_details']  = isset($request->tracker_details) ? 'on' : 'off';
        $data['timesheet']  = isset($request->timesheet) ? 'on' : 'off';
        $data['password_protected']  = isset($request->password_protected) ? 'on' : 'off';
        $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $id)->first();


        if(isset($request->password_protected) && $request->password_protected == 'on' ){
            $project->password = base64_encode($request->password);

        }else{
            $project->password = null;
        }


        $project->copylinksetting = (count($data) > 0 ) ? json_encode($data) : null;
        $project->save();
        return redirect()->back()->with('success', __('Copy Link Setting Save Successfully!'));
    }
    public function projectCalender($slug, $projectID)
    {
        $objUser = Auth::user();
        $workspace_type = WorkspaceType::get();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($objUser && $currentWorkspace) {
            if ($objUser->getGuard() == 'client') {
                $project = Project::select('projects.*')->join('client_projects', 'projects.id', '=', 'client_projects.project_id')->where('client_projects.client_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            } else {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            }
            if (isset($project) && $project != null) {
                $chartData = $this->getProjectChart(
                    [
                        'workspace_id' => $currentWorkspace->id,
                        'project_id' => $projectID,
                        'duration' => 'week',
                    ]
                );

                $daysleft = round((((strtotime($project->end_date) - strtotime(date('Y-m-d'))) / 24) / 60) / 60);

                $permissions = Auth::user()->getPermission($project->id);

                $tags = json_decode($project->tags);
                // $meetings = auth()->meeting() Meeting::get();
                $meetings = Meeting::whereHas('members', function ($query) use ($objUser) {
                    $query->where('member_id', $objUser->id);
                })->orWhere('meeting_cundocter_id', $objUser->id)
                ->get();


                // $currentWorkspace->getUsers();
                $WSUsers = UserResource::collection($currentWorkspace->users);
                $meetingCollection = MeetingResource::collection($meetings);
                return view('vue-ui.pages.project.calender', compact('currentWorkspace', 'project', 'chartData', 'daysleft', 'permissions','tags','WSUsers','meetingCollection','workspace_type'));
                // return view('projects.show', compact('currentWorkspace', 'project', 'chartData', 'daysleft', 'permissions','tags'));
            } else {
                return redirect()->back()->with('error', __("Project Not Found."));
            }
        } else {

            return redirect()->back()->with('error', __("Workspace Not Found."));
        }

    }


    public function customProjectTaskBoard($slug, $projectID)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $workspace_type = WorkspaceType::get();
        $objUser = Auth::user();

        if ($objUser && $currentWorkspace) {

            if ($objUser->getGuard() == 'client') {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            } else {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            }

            if ($project) {
                //
                $chartData = $this->getProjectChart(
                    [
                        'workspace_id' => $currentWorkspace->id,
                        'project_id' => $projectID,
                        'duration' => 'week',
                    ]
                );

                $daysleft = round((((strtotime($project->end_date) - strtotime(date('Y-m-d'))) / 24) / 60) / 60);


                //
                $stages = $statusClass = [];

                $permissions = Auth::user()->getPermission($projectID);

                if ($project && (isset($permissions) && in_array('show task', $permissions)) || (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner')) {
                    $stages = Stage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->get();

                    foreach ($stages as &$status) {
                        $statusClass[] = 'task-list-' . str_replace(' ', '_', $status->id);
                        $task = Task::where('project_id', '=', $projectID);
                        if ($currentWorkspace->permission != 'Owner' && $objUser->getGuard() != 'client') {
                            if (isset($objUser) && $objUser) {
                                $task->whereRaw("find_in_set('" . $objUser->id . "',assign_to)");
                            }
                        }
                        $task->orderBy('order');
                        $status['tasks'] = $task->where('status', '=', $status->id)
                        ->where(function ($query) use ($objUser) {
                            $query->whereRaw('FIND_IN_SET(?, assign_to)', [$objUser->id])
                                  ->orWhereHas('project', function ($subQuery) use ($objUser) {
                                      $subQuery->where('created_by', $objUser->id);
                                  });
                        })
                        ->get();
                    }
                }
                // dd('sdsd');
                return view('vue-ui.pages.project.taskboard', compact('chartData','currentWorkspace', 'project', 'stages', 'statusClass','workspace_type'));
            } else {
                return redirect()->back()->with('error', __('Task Not Found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Workspace Not Found.'));
        }
    }

    public function customGantt($slug, $projectID, $duration = 'Week')
    {
        $objUser = Auth::user();
        $workspace_type = WorkspaceType::get();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $is_client = '';

        if ($objUser->getGuard() == 'client') {
            $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
            $is_client = 'client.';
        } else {
            $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('projects.id', '=', $projectID)->first();
        }
        $tasks = [];
        $permissions = Auth::user()->getPermission($projectID);

        if ($project && (isset($permissions) && in_array('show gantt', $permissions)) || (isset($currentWorkspace) && $currentWorkspace->permission == 'Owner')) {
            if ($objUser->getGuard() == 'client' || $currentWorkspace->permission == 'Owner') {
                $tasksobj = Task::where('project_id', '=', $project->id)->orderBy('start_date')
                ->where(function ($query) use ($objUser) {
                    $query->whereRaw('FIND_IN_SET(?, assign_to)', [$objUser->id])
                          ->orWhereHas('project', function ($subQuery) use ($objUser) {
                              $subQuery->where('created_by', $objUser->id);
                          });
                })
                ->get();
            } else {
                $tasksobj = Task::where('project_id', '=', $project->id)->where('assign_to', '=', $objUser->id)->orderBy('start_date')
                ->where(function ($query) use ($objUser) {
                    $query->whereRaw('FIND_IN_SET(?, assign_to)', [$objUser->id])
                          ->orWhereHas('project', function ($subQuery) use ($objUser) {
                              $subQuery->where('created_by', $objUser->id);
                          });
                })
                ->get();
            }
            foreach ($tasksobj as $task) {
                $tmp = [];
                $tmp['id'] = 'task_' . $task->id;
                $tmp['name'] = $task->title;
                $tmp['start'] = $task->start_date;
                $tmp['end'] = $task->due_date;
                $tmp['custom_class'] = strtolower($task->priority);
                $tmp['progress'] = $task->subTaskPercentage();
                $tmp['extra'] = [
                    'priority' => __($task->priority),
                    'comments' => count($task->comments),
                    'duration' => Date::parse($task->start_date)->format('d M Y H:i A') . ' - ' . Date::parse($task->due_date)->format('d M Y H:i A'),
                ];
                $tasks[] = $tmp;
            }
        }
        //
        $chartData = $this->getProjectChart(
            [
                'workspace_id' => $currentWorkspace->id,
                'project_id' => $projectID,
                'duration' => 'week',
            ]
        );
        //
        return view('vue-ui.pages.project.gantt', compact('chartData','currentWorkspace', 'project', 'tasks', 'duration', 'is_client','workspace_type'));
    }
}
