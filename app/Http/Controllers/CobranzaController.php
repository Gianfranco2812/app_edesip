<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuota;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Programa;

class CobranzaController extends Controller
{
    public function index(Request $request)
{
    // 1. Iniciamos la consulta base
    $query = Cuota::with(['venta.cliente', 'venta.grupo.programa']);

    // --- FILTRO DE SEGURIDAD (Admin vs Asesor) ---
    if (!Auth::user()->hasRole('Admin')) {
        $query->whereHas('venta', function($q) {
            $q->where('vendedor_id', Auth::id());
        });
    }

    // --- 2. BUSCADOR GENERAL (Nombre, Apellido, DNI, Código Grupo) ---
    if ($request->filled('search')) {
        $search = $request->search;
        $query->whereHas('venta', function($qVenta) use ($search) {
            // Buscamos en Cliente
            $qVenta->whereHas('cliente', function($qCliente) use ($search) {
                $qCliente->where('nombre', 'like', "%$search%")
                            ->orWhere('apellido', 'like', "%$search%")
                            ->orWhere('numero_documento', 'like', "%$search%");
            })
            // O buscamos por Código de Grupo
            ->orWhereHas('grupo', function($qGrupo) use ($search) {
                $qGrupo->where('codigo_grupo', 'like', "%$search%");
            });
        });
    }

    // --- 3. FILTRO POR PROGRAMA ---
    if ($request->filled('programa_id')) {
        $query->whereHas('venta.grupo', function($q) use ($request) {
            $q->where('programa_id', $request->programa_id);
        });
    }

    // --- 4. FILTRO POR ESTADO ---
    if ($request->filled('estado')) {
            switch ($request->estado) {
                case 'Pagada':
                    $query->where('estado_cuota', 'Pagada');
                    break;
                
                case 'Vencida':
                    // Busca cuotas que siguen "Pendiente" PERO cuya fecha ya pasó
                    $query->where('estado_cuota', 'Pendiente')
                        ->whereDate('fecha_vencimiento', '<', now());
                    break;
                
                case 'PorVencer':
                    // Busca cuotas "Pendiente" cuya fecha es HOY o Futura
                    $query->where('estado_cuota', 'Pendiente')
                        ->whereDate('fecha_vencimiento', '>=', now());
                    break;

                case 'TodasPendientes':
                    // Muestra todo lo que se debe (Vencido y Por Vencer)
                    $query->where('estado_cuota', 'Pendiente');
                    break;
            }
        } else {
            // --- COMPORTAMIENTO POR DEFECTO ---
            
            // Si no hay búsqueda de texto, mostramos solo lo que se debe (Pendiente + Vencido)
            // Si el usuario busca "Juan", mostramos TODO (incluyendo sus pagadas)
            if (!$request->filled('search')) {
                $query->where('estado_cuota', 'Pendiente');
            }
    }

    // --- 5. FILTRO POR FECHAS (Vencimiento) ---
    if ($request->filled('fecha_inicio')) {
        $query->whereDate('fecha_vencimiento', '>=', $request->fecha_inicio);
    }
    if ($request->filled('fecha_fin')) {
        $query->whereDate('fecha_vencimiento', '<=', $request->fecha_fin);
    }

    // Ordenar y ejecutar
    $cuotas = $query->orderBy('fecha_vencimiento', 'asc')->paginate(20); // Usamos paginación mejor

    // Obtenemos los programas para el dropdown del filtro
    $programas = Programa::where('estado', 'Activo')->get();

    return view('cobranzas.index', compact('cuotas', 'programas'));
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

        return redirect()->route('cobranzas.index')
                            ->with('success', 'Pago registrado correctamente. La cuota ha sido saldada.');
    }
}
