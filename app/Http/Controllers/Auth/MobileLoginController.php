<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileLoginController extends Controller
{
    use AuthenticatesUsers;

    public function login(Request $request){
        if (!auth('api')->check()){
            abort(401);
        }

        $user = auth('api')->user();

        $request->session()->regenerate();

        $this->clearLoginAttempts($request);
        Auth::login($user, true);

        return redirect(route('home'));
    }
}
