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
        $now = Carbon::now(); // Fecha actual

        // ======================================================
        // 1. KPI: INGRESOS DEL MES (Suma de cuotas PAGADAS)
        // ======================================================
        $ingresosQuery = Cuota::where('estado_cuota', 'Pagada')
            ->whereMonth('fecha_pago', $now->month)
            ->whereYear('fecha_pago', $now->year);

        if (!$esAdmin) {
            // Si es asesor, solo suma lo de SUS ventas
            $ingresosQuery->whereHas('venta', function($q) use ($user) {
                $q->where('vendedor_id', $user->id);
            });
        }
        $ingresosMes = $ingresosQuery->sum('monto_cuota');


        // ======================================================
        // 2. KPI: PAGOS POR VALIDAR (Vouchers subidos)
        // ======================================================
        // Corregido: Buscamos cuotas que tengan reportes 'Pendiente'
        $validacionesQuery = Cuota::whereHas('reportes', function ($q) {
            $q->where('estado', 'Pendiente');
        });
        
        if (!$esAdmin) {
            $validacionesQuery->whereHas('venta', function($q) use ($user) {
                $q->where('vendedor_id', $user->id);
            });
        }
        $pagosPendientesValidar = $validacionesQuery->count();


        // ======================================================
        // 3. KPI: VENTAS DEL MES (Matrículas Nuevas)
        // ======================================================
        $ventasMesQuery = Venta::where('estado', '!=', 'Anulada')
            ->whereMonth('fecha_venta', $now->month)
            ->whereYear('fecha_venta', $now->year);
        
        if (!$esAdmin) {
            $ventasMesQuery->where('vendedor_id', $user->id);
        }
        // ¡AQUÍ ESTABA EL ERROR! Aseguramos que la variable se cree:
        $ventasMes = $ventasMesQuery->count();


        // ======================================================
        // 4. KPI: PROSPECTOS TOTALES
        // ======================================================
        $prospectosQuery = Cliente::where('estado', 'Prospecto');
        
        if (!$esAdmin) {
            $prospectosQuery->where('creado_por_vendedor_id', $user->id);
        }
        $totalProspectos = $prospectosQuery->count();


        // ======================================================
        // 5. TABLA: ÚLTIMAS VENTAS
        // ======================================================
        $ultimasVentas = Venta::with(['cliente', 'grupo.programa', 'contrato'])
            ->where('estado', '!=', 'Anulada');
            
        if (!$esAdmin) {
            $ultimasVentas->where('vendedor_id', $user->id);
        }
        
        $ultimasVentas = $ultimasVentas->latest('fecha_venta')->take(5)->get();
        

        // ======================================================
        // 6. GRÁFICO: RANKING DE ASESORES (MES ACTUAL)
        // ======================================================
        $labelsGrafico = []; 
        $dataGrafico = [];
        $labelsProspectos = []; 
        $dataProspectos = [];

        // 2. Solo consultamos si es Admin
        if ($esAdmin) {
            
            // --- Gráfico Ventas ---
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
        // ======================================================
        // RETORNO A LA VISTA
        // ======================================================
        // Ahora todas las variables están definidas arriba
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
