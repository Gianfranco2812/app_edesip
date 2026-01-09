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

        // 1. Si es CLIENTE, va a su portal
        if ($user->hasRole('Cliente')) {
            return route('portal.home'); 
        }

        // 2. Si es ADMIN o ASESOR, va al Dashboard
        // Asegúrate de que la ruta 'dashboard' esté definida en web.php
        return route('dashboard'); 
    }

    public function __construct()
    {
        // IMPORTANTE: El login debe usar 'guest', NO 'auth'
        // Esto permite que solo personas NO logueadas vean el formulario de login
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }
}
