<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\RoleController; // Importamos el controlador que creamos

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar las rutas web para tu aplicación. Estas
| rutas son cargadas por el RouteServiceProvider y todas ellas
| serán asignadas al grupo de middleware "web".
|
*/

// --- 1. RUTAS PÚBLICAS ---

// Redirige la ruta raíz ("/") a la página de login
Route::get('/', function () {
    return redirect()->route('login');
});

// Agrega todas las rutas de autenticación (login, register, logout, etc.)
// Esto fue generado por `php artisan ui bootstrap --auth`
Auth::routes();


// --- 2. RUTAS PROTEGIDAS (Requieren Login) ---

// Todo lo que esté dentro de este grupo requerirá que el usuario
// haya iniciado sesión (middleware 'auth').
Route::middleware(['auth'])->group(function () {

    // Dashboard principal
    // Esta es la ruta a la que son redirigidos al loguearse (nuestra 'HOME')
    Route::get('/dashboard', function() {
        return view('dashboard'); 
    })->name('dashboard');


    // --- 3. RUTAS DE ADMINISTRACIÓN (SOLO ROL 'Admin') ---

    // Agrupamos todas las rutas de admin bajo el prefijo "admin"
    // y las protegemos con el middleware de rol 'Admin'.
    Route::prefix('admin')->middleware(['role:Admin'])->group(function () {

        // CRUD de Roles
        // Esto crea automáticamente las 7 rutas (index, create, store, etc.)
        // y las apunta a tu 'RoleController'.
        Route::resource('roles', RoleController::class);

        // (Aquí es donde pondremos el CRUD de Usuarios)
        // Route::resource('usuarios', UserController::class);

        // (Aquí es donde pondremos el CRUD de Cursos)
        // Route::resource('cursos', CursoController::class);

    });

    // (Aquí es donde pondremos las rutas para el rol 'Asesor')
    // Route::middleware(['role:Admin|Asesor'])->group(function () { ... });

    // (Aquí es donde pondremos las rutas para el rol 'Cliente')
    // Route::middleware(['role:Cliente'])->group(function () { ... });

});