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
use App\Http\Controllers\CobranzaController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;



// --- 1. RUTAS PÚBLICAS ---


Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('contrato/confirmar/{token_acceso}', [ContratoController::class, 'mostrar'])->name('contratos.mostrar');
Route::post('contrato/confirmar/{token_acceso}', [ContratoController::class, 'confirmar'])->name('contratos.confirmar');

Auth::routes();


// --- 2. RUTAS PROTEGIDAS (Requieren Login) ---

Route::middleware(['auth'])->group(function () {

    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil/info', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


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
        Route::put('/ventas/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular');
        Route::get('/cobranzas', [CobranzaController::class, 'index'])->name('cobranzas.index');
        Route::get('/cobranzas/{cuota}/pagar', [CobranzaController::class, 'edit'])->name('cobranzas.edit');
        Route::put('/cobranzas/{cuota}', [CobranzaController::class, 'update'])->name('cobranzas.update');
        Route::get('/cobranzas/{venta}', [CobranzaController::class, 'show'])->name('cobranzas.show');

        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/exportar/{tipo}', [ReporteController::class, 'exportar'])->name('reportes.exportar');

    });

    Route::middleware(['role:Cliente'])->group(function () {
        Route::get('/mi-portal', [PortalController::class, 'index'])->name('portal.index');
    });


});