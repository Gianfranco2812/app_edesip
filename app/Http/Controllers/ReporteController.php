<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Cuota;
use App\Models\User;
use App\Exports\VentasExport;
use App\Exports\IngresosExport;
use App\Exports\MorosidadExport;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        // Fechas por defecto: Mes actual
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // --- 1. REPORTE DE VENTAS (Resumen) ---
        $ventasQuery = Venta::whereDate('fecha_venta', '>=', $fechaInicio)
                            ->whereDate('fecha_venta', '<=', $fechaFin)
                            ->where('estado', '!=', 'Anulada');

        if (!Auth::user()->hasRole('Admin')) {
            $ventasQuery->where('vendedor_id', Auth::id());
        }

        $totalVentas = $ventasQuery->count();
        $montoVendido = $ventasQuery->sum('costo_total_venta');

        // --- 2. REPORTE FINANCIERO (Caja / Ingresos reales) ---
        $ingresosQuery = Cuota::where('estado_cuota', 'Pagada')
                                ->whereDate('fecha_pago', '>=', $fechaInicio)
                                ->whereDate('fecha_pago', '<=', $fechaFin);
        
        if (!Auth::user()->hasRole('Admin')) {
            $ingresosQuery->whereHas('venta', function($q) {
                $q->where('vendedor_id', Auth::id());
            });
        }

        $totalIngresos = $ingresosQuery->sum('monto_cuota');

        // --- 3. REPORTE DE MOROSIDAD (Deuda Vencida) ---
        // Aquí no filtramos por fecha de pago, sino por fecha de vencimiento que ya pasó
        $deudaQuery = Cuota::where('estado_cuota', '!=', 'Pagada')
                            ->whereDate('fecha_vencimiento', '<', now());

        if (!Auth::user()->hasRole('Admin')) {
            $deudaQuery->whereHas('venta', function($q) {
                $q->where('vendedor_id', Auth::id());
            });
        }

        $totalDeudaVencida = $deudaQuery->sum('monto_cuota');
        $cantidadDeudores = $deudaQuery->distinct('venta_id')->count('venta_id');

        return view('reportes.index', compact(
            'fechaInicio', 'fechaFin', 
            'totalVentas', 'montoVendido',
            'totalIngresos',
            'totalDeudaVencida', 'cantidadDeudores'
        ));
    }
    public function exportar(Request $request, $tipo)
    {
        // Fechas (si no vienen, usamos el mes actual)
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->format('Y-m-d'));

        switch ($tipo) {
            case 'ventas':
                return Excel::download(new VentasExport($fechaInicio, $fechaFin), 'Reporte_Ventas.xlsx');

            case 'ingresos':
                return Excel::download(new IngresosExport($fechaInicio, $fechaFin), 'Reporte_Ingresos.xlsx');

            case 'morosidad':
                return Excel::download(new MorosidadExport(), 'Reporte_Morosidad.xlsx');

            default:
                return back()->with('error', 'Tipo de reporte no válido');
        }
    }
}
