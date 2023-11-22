<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Utility;
use App\Models\UserWorkspace;
use App\Models\WorkspaceType;
use App\Models\DepartUserRole;
use App\Models\WorkspaceDepartRolePivot;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;
class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */


      public function __construct()
    {
        $this->middleware('guest');
    }


    public function create()
    {
        // return view('auth.register');
    }

    public function register(Request $request)
    {
       $user = User::create($request->validated());

       event(new Registered($user));

       auth()->login($user);

       return redirect('/')->with('success', "Account successfully registered.");
    }

  public function showRegistrationForm($lang = '')
    {
       $departments = Department::get();
    //    dd($departments->workspaces->first()->id);
        // $workspace_type = Workspace::where('workspace_type_id','2')->get();

        // $workspace_type = Workspace::whereHas('workspaceType', function ($query) {
        //     $query->where('slug', 'depart');
        // })->get();


        $depart_user_role = DepartUserRole::get();

        // dd('asd');
        if ($lang == '') {
            $lang = env('DEFAULT_ADMIN_LANG') ?? 'en';
        }

        \App::setLocale($lang);

        // if(env('SIGNUP_BUTTON') == 'on'){
        //     return view('custom-auth.register', compact('lang'));
        // }else{
        //     return abort('404', 'Page not found');
        // }
        return view('custom-auth.register', compact('lang','depart_user_role','departments'));
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required','string', 'min:8', 'confirmed', Rules\Password::defaults()],
        ]);

        // get workspace id by using depart id
        // $workspace_id = Department::find($request->department_id)->workspaces->first()->id;
        $workspace_id = WorkSpace::where('is_default',1)->first()->id;

        // dd($workspace);

        // create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            // 'workspace'=>$request->workspace,
            'password' => Hash::make($request->password),
            'plan'=>1,
            'lang'      => env('DEFAULT_ADMIN_LANG') ?? 'en',
        ]);


        // $objWorkspace = Workspace::create(['created_by'=>$user->id,'name'=>$request->workspace, 'currency_code' => 'USD', 'paypal_mode' => 'sandbox']);
        $setting = Utility::getAdminPaymentSettings();



        $userWorkspace               =   new UserWorkspace();
        $userWorkspace->user_id      =     $user->id;
        $userWorkspace->workspace_id =    $workspace_id;
        $userWorkspace->permission   = 'Member';

        if(empty($userWorkspace))
        {
            $errorArray[] = $userWorkspace;
        }
        else
        {
            $userWorkspace->save();

            // $userWorkspace->departUserRoles()->attach($request->depart_user_role_id);

        }

            $user->currant_workspace = $workspace_id;

            // assigned depart to a user
            $user->departments()->attach($request->department_id,['role_id' => $request->depart_user_role_id]);
            // dd($user->departments()->attach($request->department_id,['role_id' => $request->depart_user_role_id]));


            User::userDefaultDataRegister($user);

            Auth::login($user);


                $user->email_verified_at = date('h:i:s');

                $user->save();
                return redirect(RouteServiceProvider::HOME);
    }
}



