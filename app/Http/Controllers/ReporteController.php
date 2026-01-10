<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Cuota;
use App\Models\User;
use App\Exports\VentasExport;
use App\Exports\IngresosExport;
use App\Exports\MorosidadExport;
use App\Exports\VentasPorAsesorExport;
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
        $rankingAsesores = []; // Array vacío por defecto para que no de error en la vista
        
        if (Auth::user()->hasRole('Admin')) {
            $rankingAsesores = User::role(['Asesor', 'Admin'])
                ->withCount(['ventas' => function($q) use ($fechaInicio, $fechaFin) {
                    $q->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
                        ->where('estado', '!=', 'Anulada');
                }])
                ->orderBy('ventas_count', 'desc')
                ->take(5)
                ->get();
        }

        $totalDeudaVencida = $deudaQuery->sum('monto_cuota');
        $cantidadDeudores = $deudaQuery->distinct('venta_id')->count('venta_id');

        return view('reportes.index', compact(
            'fechaInicio', 'fechaFin', 
            'totalVentas', 'montoVendido',
            'totalIngresos',
            'totalDeudaVencida', 'cantidadDeudores'
            ,'rankingAsesores'
        ));
    }
    public function exportar(Request $request, $tipo)
    {
        // 1. Obtener las fechas
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin    = $request->input('fecha_fin', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // 2. CREAR EL TEXTO DE LA FECHA PARA EL ARCHIVO
        // Convertimos "2026-01-09" a "09-01-2026" (sin barras "/" para no romper el archivo)
        $textoFecha = Carbon::parse($fechaInicio)->format('d-m-Y');

        switch ($tipo) {
            case 'ventas':
                // Resultado: "Reporte_Ventas_desde_09-01-2026.xlsx"
                $nombreArchivo = 'Reporte_Ventas_desde_' . $textoFecha . '.xlsx';
                return Excel::download(new VentasExport($fechaInicio, $fechaFin), $nombreArchivo);

            case 'ingresos':
                // Resultado: "Reporte_Ingresos_desde_09-01-2026.xlsx"
                $nombreArchivo = 'Reporte_Ingresos_desde_' . $textoFecha . '.xlsx';
                return Excel::download(new IngresosExport($fechaInicio, $fechaFin), $nombreArchivo);

            case 'morosidad':
                // Para morosidad, usualmente es una foto "al día de hoy", así que usamos la fecha actual
                $hoy = Carbon::now()->format('d-m-Y');
                return Excel::download(new MorosidadExport(), 'Reporte_Morosidad_al_' . $hoy . '.xlsx');
            
            case 'asesores':
                return Excel::download(
                    new VentasPorAsesorExport($fechaInicio, $fechaFin), 
                    'Reporte_Rendimiento_Asesores.xlsx'
                );

            default:
                return back()->with('error', 'Tipo de reporte no válido');
        }
    }
}
