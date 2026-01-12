<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Donde redirigir tras el login exitoso
     */
    public function redirectTo()
    {
        $user = auth()->user();

        if ($user->hasRole('Cliente')) {
            return route('portal.home'); 
        }

        return route('dashboard'); 
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }
}
