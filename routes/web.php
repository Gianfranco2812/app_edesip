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
use App\Http\Controllers\AdminPagoController;
use App\Http\Controllers\MetodoPagoController;





Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/firmar-contrato/{token}', [ContratoController::class, 'vistaPublica'])->name('contratos.publico');
Route::post('/firmar-contrato/{token}/procesar', [ContratoController::class, 'procesarFirma'])->name('contratos.procesar');
Route::get('/ventas/{id}/previsualizar-contrato', [VentaController::class, 'previsualizar'])->name('ventas.previsualizar');

Auth::routes();




Route::middleware(['auth'])->group(function () {

    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil/info', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/contratos/{id}/pdf', [ContratoController::class, 'verPdf'])->name('contratos.ver_pdf');


    



    Route::prefix('admin')->middleware(['role:Admin'])->group(function () {

        Route::resource('roles', RoleController::class);

        Route::resource('usuarios', UserController::class);

        Route::resource('tipos-programa', TipoProgramaController::class);
        Route::resource('plantillas-contrato', PlantillaContratoController::class);
        Route::resource('programas', ProgramaController::class);
        Route::resource('grupos', GrupoController::class);
    });


    Route::middleware(['role:Admin|Asesor'])->group(function () {
        
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::resource('clientes', ClienteController::class); 
        Route::resource('ventas', VentaController::class);
        Route::put('/ventas/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular');
        Route::get('/cobranzas', [CobranzaController::class, 'index'])->name('cobranzas.index');
        Route::get('/cobranzas/{cuota}/pagar', [CobranzaController::class, 'edit'])->name('cobranzas.edit');
        Route::put('/cobranzas/{cuota}', [CobranzaController::class, 'update'])->name('cobranzas.update');
        Route::get('/cobranzas/{venta}', [CobranzaController::class, 'show'])->name('cobranzas.show');

        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/exportar/{tipo}', [ReporteController::class, 'exportar'])->name('reportes.exportar');

        Route::post('/admin/pagos/{id}/aprobar', [AdminPagoController::class, 'aprobar'])->name('pagos.aprobar');
        Route::post('/admin/pagos/{id}/rechazar', [AdminPagoController::class, 'rechazar'])->name('pagos.rechazar');

        Route::resource('metodos_pago', MetodoPagoController::class);
        Route::put('/metodos_pago/{id}/toggle', [MetodoPagoController::class, 'toggleEstado'])->name('metodos_pago.toggle');

    });

    Route::middleware(['auth', 'role:Cliente'])->prefix('portal')->name('portal.')->group(function () {

        Route::get('/home', [PortalController::class, 'index'])->name('home');
        Route::get('/pagos', [PortalController::class, 'pagos'])->name('pagos');
        Route::get('/mis-datos', [PortalController::class, 'perfil'])->name('perfil');
        Route::post('/pagos/reportar', [PortalController::class, 'reportarPago'])->name('pagos.reportar');

});


});