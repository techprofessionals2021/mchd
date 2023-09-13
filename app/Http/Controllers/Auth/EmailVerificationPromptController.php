<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

class EmailVerificationPromptController extends Controller
{

    public function __invoke(Request $request, $lang='')
    {
        if(empty($lang))
        {
            $lang = env('DEFAULT_LANG');
        }
        \App::setLocale($lang);
                
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(RouteServiceProvider::HOME)
                    : view('auth.verify-email', ['statuss' => session('statuss'), 'lang' => $lang]);
    }
}
