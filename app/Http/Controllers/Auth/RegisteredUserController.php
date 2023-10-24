<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Utility;
use App\Models\UserWorkspace;
use App\Models\WorkspaceType;
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

        // $workspace_type = Workspace::where('workspace_type_id','2')->get();

        $workspace_type = Workspace::whereHas('workspaceType', function ($query) {
            $query->where('slug', 'depart');
        })->get();
        // dd($workspace_type);

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
        return view('custom-auth.register', compact('lang','workspace_type'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // if(env('RECAPTCHA_MODULE') == 'on')
        // {
        //     $validation['g-recaptcha-response'] = 'required|captcha';
        // }else{
        //     $validation = [];
        // }
        // $this->validate($request, $validation);
        // try {
        $request->validate([
            'name' => 'required|string|max:255',
            'workspace' => 'required', 'string', 'max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required','string', 'min:8', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'workspace'=>$request->workspace,
            'password' => Hash::make($request->password),
            'plan'=>1,
            'lang'      => env('DEFAULT_ADMIN_LANG') ?? 'en',
        ]);


        // $objWorkspace = Workspace::create(['created_by'=>$user->id,'name'=>$request->workspace, 'currency_code' => 'USD', 'paypal_mode' => 'sandbox']);
        $setting = Utility::getAdminPaymentSettings();



        $userWorkspace               =   new UserWorkspace();
        $userWorkspace->user_id      =     $user->id;
        $userWorkspace->workspace_id =    $request->workspace_id;
        $userWorkspace->permission   = 'Member';

        if(empty($userWorkspace))
        {
            $errorArray[] = $userWorkspace;
        }
        else
        {
            $userWorkspace->save();
        }

            $user->currant_workspace = $request->workspace_id;
            User::userDefaultDataRegister($user);

            Auth::login($user);

            // if($setting['email_verification'] == 'on'){


            //     try{

            //         $user->save();
            //         event(new Registered($user));
            //         UserWorkspace::create(['user_id'=> $user->id,'workspace_id'=>$objWorkspace->id,'permission'=>'Owner']);
            //         if(empty($lang))
            //         {
            //             $lang = env('default_language');
            //         }
            //         \App::setLocale($lang);


            //     }catch(\Exception $e){

            //         $user->delete();
            //         $userWorkspace->delete();

            //         // dd($user);
            //         return redirect('/register/lang?')->with('statuss', __('Email SMTP settings does not configure so please contact to your site admin.'));
            //     }

            //     return view('auth.verify-email', compact('lang'));
            // }else{

                $user->email_verified_at = date('h:i:s');

                $user->save();
                return redirect(RouteServiceProvider::HOME);
            // }

        // } catch (\Exception $e) {
        //     dd($e->getMessage());
        //     return redirect()->back('error','Some thing went wrong');
        //     //throw $th;
        // }
    }
}



