<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Cuota;
use App\Models\Grupo; 
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Programa;

class CobranzaController extends Controller
{
    public function index(Request $request)
    {
        // Consulta base: Ventas activas
        $query = Venta::with(['cliente', 'grupo.programa', 'vendedor', 'cuotas' => function($q) {
            $q->where('estado_cuota', '!=', 'Pagada')
                ->orderBy('fecha_vencimiento', 'asc');
        }])
        ->withCount(['cuotas as vouchers_por_validar' => function ($q) {
            // Contamos cuotas que tengan reportes 'Pendiente'
            $q->whereHas('reportes', function ($r) {
                $r->where('estado', 'Pendiente');
            });
        }])
        ->where('estado', '!=', 'Anulada');

        // --- 1. SEGURIDAD (Rol) ---
        if (!Auth::user()->hasRole('Admin')) {
            $query->where('vendedor_id', Auth::id());
        }

        // --- 2. BUSCADOR (Texto) ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($qMain) use ($search) {
                $qMain->whereHas('cliente', function($q) use ($search) {
                    $q->where('nombre', 'like', "%$search%")
                        ->orWhere('apellido', 'like', "%$search%")
                        ->orWhere('numero_documento', 'like', "%$search%");
                })->orWhereHas('grupo', function($q) use ($search) {
                    $q->where('codigo_grupo', 'like', "%$search%");
                });
            });
        }

        // --- 3. FILTRO POR ASESOR (Solo Admin) ---
        if (Auth::user()->hasRole('Admin') && $request->filled('vendedor_id')) {
            $query->where('vendedor_id', $request->vendedor_id);
        }

        // --- 4. FILTRO POR GRUPO ---
        if ($request->filled('grupo_id')) {
            $query->where('grupo_id', $request->grupo_id);
        }

        // --- 5. FILTRO POR ESTADO DE DEUDA (SEMÁFORO) ---
        // Aquí ocurre la magia para filtrar R/N/V en la base de datos
        if ($request->filled('estado_deuda')) {
            $hoy = now()->startOfDay();
            $limitePronto = now()->addDays(3)->endOfDay(); // Definimos "Pronto" como 3 días

            switch ($request->estado_deuda) {
                case 'vencido': // ROJO
                    // Ventas que tienen AL MENOS UNA cuota vencida pendiente
                    $query->whereHas('cuotas', function($q) use ($hoy) {
                        $q->where('estado_cuota', '!=', 'Pagada')
                            ->whereDate('fecha_vencimiento', '<', $hoy);
                    });
                    break;

                case 'por_vencer': // NARANJA
                    // Ventas con cuotas próximas... Y SIN cuotas vencidas (si no, sería rojo)
                    $query->whereHas('cuotas', function($q) use ($hoy, $limitePronto) {
                        $q->where('estado_cuota', '!=', 'Pagada')
                            ->whereBetween('fecha_vencimiento', [$hoy, $limitePronto]);
                    })->whereDoesntHave('cuotas', function($q) use ($hoy) {
                        $q->where('estado_cuota', '!=', 'Pagada')
                            ->whereDate('fecha_vencimiento', '<', $hoy);
                    });
                    break;

                case 'al_dia': // VERDE
                    // Ventas que NO tienen nada vencido ni próximo a vencer
                    $query->whereDoesntHave('cuotas', function($q) use ($limitePronto) {
                        $q->where('estado_cuota', '!=', 'Pagada')
                            ->whereDate('fecha_vencimiento', '<=', $limitePronto);
                    });
                    break;
            }
        }

        // Ejecutar
        $ventas = $query->latest()->paginate(20);

        // --- DATOS PARA DROPDOWNS ---
        // Grupos activos o próximos
        $grupos = Grupo::with('programa')->where('estado', '!=', 'Cancelado')->get();
        
        // Vendedores
        $vendedores = [];
        if (Auth::user()->hasRole('Admin')) {
            $vendedores = User::role(['Asesor', 'Admin'])->get();
        }

        return view('cobranzas.index', compact('ventas', 'grupos', 'vendedores'));
    }


    public function show($id)
    {
        $venta = Venta::with(['cliente', 'grupo', 'cuotas'])->findOrFail($id);

        // Seguridad: Asesor solo ve sus ventas
        if (!Auth::user()->hasRole('Admin') && $venta->vendedor_id != Auth::id()) {
            abort(403);
        }

        return view('cobranzas.show', compact('venta'));
    }

    /**
     * Muestra el formulario para registrar un pago.
     */
    public function edit(Cuota $cuota)
    {
        // Seguridad: Verificar si el asesor puede ver esta cuota
        if (!Auth::user()->hasRole('Admin') && $cuota->venta->vendedor_id != Auth::id()) {
            abort(403, 'No tienes permiso para gestionar esta cobranza.');
        }

        return view('cobranzas.edit', compact('cuota'));
    }

    /**
     * Procesa el pago.
     */
    public function update(Request $request, Cuota $cuota)
    {
        $request->validate([
            'fecha_pago' => 'required|date',
            'monto_pagado' => 'required|numeric|min:' . $cuota->monto_cuota, // Debe pagar al menos el monto exacto
            'metodo_pago' => 'required|string',
            'transaccion_id' => 'nullable|string',
        ]);

        // Actualizamos la cuota
        $cuota->update([
            'estado_cuota' => 'Pagada',
            'fecha_pago' => $request->fecha_pago,
            'metodo_pago' => $request->metodo_pago,
            'transaccion_id' => $request->transaccion_id,
            // Nota: Podrías guardar el 'monto_pagado' si permites pagos parciales, 
            // pero por ahora asumimos pago completo.
        ]);

        return redirect()->route('cobranzas.show', $cuota->venta_id)
                            ->with('success', 'Pago registrado correctamente.');
    }
}
