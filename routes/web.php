<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\RoleController; 
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TipoProgramaController;
use App\Http\Controllers\Admin\PlantillaContratoController;
use App\Http\Controllers\Admin\ProgramaController;
use App\Http\Controllers\Admin\GrupoController; 


// --- 1. RUTAS PÚBLICAS ---


Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();


// --- 2. RUTAS PROTEGIDAS (Requieren Login) ---

Route::middleware(['auth'])->group(function () {


    Route::get('/dashboard', function() {
        return view('dashboard'); 
    })->name('dashboard');


    // --- 3. RUTAS DE ADMINISTRACIÓN (SOLO ROL 'Admin') ---

    Route::prefix('admin')->middleware(['role:Admin'])->group(function () {

        Route::resource('roles', RoleController::class);

        Route::resource('usuarios', UserController::class);

        //Route::resource('cursos', CursoController::class);
        Route::resource('tipos-programa', TipoProgramaController::class);
        Route::resource('plantillas-contrato', PlantillaContratoController::class);
        Route::resource('programas', ProgramaController::class);
        Route::resource('grupos', GrupoController::class);

    });


});