<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Cuota;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class DashboardController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $esAdmin = $user->hasRole('Admin');
        $now = Carbon::now(); 


        $ingresosQuery = Cuota::where('estado_cuota', 'Pagada')
            ->whereMonth('fecha_pago', $now->month)
            ->whereYear('fecha_pago', $now->year);

        if (!$esAdmin) {

            $ingresosQuery->whereHas('venta', function($q) use ($user) {
                $q->where('vendedor_id', $user->id);
            });
        }
        $ingresosMes = $ingresosQuery->sum('monto_cuota');



        $validacionesQuery = Cuota::whereHas('reportes', function ($q) {
            $q->where('estado', 'Pendiente');
        });
        
        if (!$esAdmin) {
            $validacionesQuery->whereHas('venta', function($q) use ($user) {
                $q->where('vendedor_id', $user->id);
            });
        }
        $pagosPendientesValidar = $validacionesQuery->count();


        $ventasMesQuery = Venta::where('estado', '!=', 'Anulada')
            ->whereMonth('fecha_venta', $now->month)
            ->whereYear('fecha_venta', $now->year);
        
        if (!$esAdmin) {
            $ventasMesQuery->where('vendedor_id', $user->id);
        }

        $ventasMes = $ventasMesQuery->count();



        $prospectosQuery = Cliente::where('estado', 'Prospecto');
        
        if (!$esAdmin) {
            $prospectosQuery->where('creado_por_vendedor_id', $user->id);
        }
        $totalProspectos = $prospectosQuery->count();



        $ultimasVentas = Venta::with(['cliente', 'grupo.programa', 'contrato'])
            ->where('estado', '!=', 'Anulada');
            
        if (!$esAdmin) {
            $ultimasVentas->where('vendedor_id', $user->id);
        }
        
        $ultimasVentas = $ultimasVentas->latest('fecha_venta')->take(5)->get();
        


        $labelsGrafico = []; 
        $dataGrafico = [];
        $labelsProspectos = []; 
        $dataProspectos = [];

    
        if ($esAdmin) {
            

            $rankingGrafico = \App\Models\User::role(['Asesor', 'Admin'])
                ->withCount(['ventas' => function ($q) use ($now) {
                    $q->whereMonth('fecha_venta', $now->month)
                        ->whereYear('fecha_venta', $now->year)
                        ->where('estado', '!=', 'Anulada');
                }])
                ->orderBy('ventas_count', 'desc')->take(5)->get();

            $labelsGrafico = $rankingGrafico->pluck('name');
            $dataGrafico   = $rankingGrafico->pluck('ventas_count');

            // --- Gráfico Prospectos ---
            $rankingProspectos = \App\Models\User::role(['Asesor', 'Admin'])
                ->withCount(['clientes' => function ($q) {
                    $q->where('estado', 'Prospecto');
                }])
                ->orderBy('clientes_count', 'desc')->take(5)->get();

            $labelsProspectos = $rankingProspectos->pluck('name');
            $dataProspectos   = $rankingProspectos->pluck('clientes_count');
        }

        return view('dashboard', compact(
        'ingresosMes', 
        'pagosPendientesValidar', 
        'ventasMes', 
        'totalProspectos',
        'ultimasVentas',
        'esAdmin',          
        'labelsGrafico',    
        'dataGrafico',
        'labelsProspectos',
        'dataProspectos'       
    ));
    }
}
