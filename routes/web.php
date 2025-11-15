<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\RoleController; 
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TipoProgramaController;
use App\Http\Controllers\Admin\PlantillaContratoController;
use App\Http\Controllers\Admin\ProgramaController;
use App\Http\Controllers\Admin\GrupoController; 
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ContratoController;


// --- 1. RUTAS PÚBLICAS ---


Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('contrato/confirmar/{token_acceso}', [ContratoController::class, 'mostrar'])->name('contratos.mostrar');
Route::post('contrato/confirmar/{token_acceso}', [ContratoController::class, 'confirmar'])->name('contratos.confirmar');

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

        Route::resource('tipos-programa', TipoProgramaController::class);
        Route::resource('plantillas-contrato', PlantillaContratoController::class);
        Route::resource('programas', ProgramaController::class);
        Route::resource('grupos', GrupoController::class);
    });

        // --- 4. RUTAS DE ADMINISTRACIÓN Y ASESOR ---

    Route::middleware(['role:Admin|Asesor'])->group(function () {
        
        Route::resource('clientes', ClienteController::class); 
        Route::resource('ventas', VentaController::class);

    });


});