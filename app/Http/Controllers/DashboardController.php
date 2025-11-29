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
        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();

        // Variables iniciales
        $totalIngresos = 0;
        $totalDeuda = 0;
        $ventasMes = 0;
        $totalAlumnos = 0;
        $ultimasVentas = [];

        // --- LÓGICA PARA ADMIN (Ve todo) ---
        if ($user->hasRole('Admin')) {
            // 1. Ingresos (Cuotas pagadas este mes)
            // Asumimos que la fecha_pago es cuando entró el dinero
            $totalIngresos = Cuota::where('estado_cuota', 'Pagada')
                                    ->whereDate('fecha_pago', '>=', $inicioMes)
                                    ->sum('monto_cuota');

            // 2. Deuda Total (Dinero en la calle - Vencido o Pendiente)
            $totalDeuda = Cuota::where('estado_cuota', '!=', 'Pagada')->sum('monto_cuota');

            // 3. Ventas del Mes
            $ventasMes = Venta::where('estado', '!=', 'Anulada')
                                ->whereDate('fecha_venta', '>=', $inicioMes)
                                ->count();

            // 4. Total Alumnos (Clientes confirmados/activos)
            $totalAlumnos = Cliente::whereIn('estado', ['Confirmado', 'Alumno Activo'])->count();

            // 5. Últimas 5 Ventas
            $ultimasVentas = Venta::with(['cliente', 'grupo.programa'])
                                    ->latest('fecha_venta')
                                    ->take(5)
                                    ->get();
        } 
        
        // --- LÓGICA PARA ASESOR (Ve solo lo suyo) ---
        else {
            // 1. Mis Ingresos (Cuotas pagadas de mis ventas)
            $totalIngresos = Cuota::whereHas('venta', function($q) use ($user) {
                                        $q->where('vendedor_id', $user->id);
                                    })
                                    ->where('estado_cuota', 'Pagada')
                                    ->whereDate('fecha_pago', '>=', $inicioMes)
                                    ->sum('monto_cuota');

            // 2. Mi Cartera Vencida (Deuda de mis alumnos)
            $totalDeuda = Cuota::whereHas('venta', function($q) use ($user) {
                                    $q->where('vendedor_id', $user->id);
                                })
                                ->where('estado_cuota', '!=', 'Pagada')
                                ->sum('monto_cuota');

            // 3. Mis Ventas del Mes
            $ventasMes = Venta::where('vendedor_id', $user->id)
                                ->where('estado', '!=', 'Anulada')
                                ->whereDate('fecha_venta', '>=', $inicioMes)
                                ->count();

            // 4. Mis Alumnos Activos
            $totalAlumnos = Cliente::where('creado_por_vendedor_id', $user->id)
                                    ->whereIn('estado', ['Confirmado', 'Alumno Activo'])
                                    ->count();

            // 5. Mis Últimas Ventas
            $ultimasVentas = Venta::with(['cliente', 'grupo.programa'])
                                    ->where('vendedor_id', $user->id)
                                    ->latest('fecha_venta')
                                    ->take(5)
                                    ->get();
        }

        return view('dashboard', compact('totalIngresos', 'totalDeuda', 'ventasMes', 'totalAlumnos', 'ultimasVentas'));
    }
}
