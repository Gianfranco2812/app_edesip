<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportePago;
use App\Models\Cuota;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminPagoController extends Controller
{
    public function aprobar($id)
    {
        DB::transaction(function () use ($id) {
            
            // 1. Buscar y validar el Reporte
            $reporte = ReportePago::findOrFail($id);
            
            if($reporte->estado != 'Pendiente') {
                throw new \Exception('Este pago ya fue procesado anteriormente.');
            }

            // 2. Actualizar estado del Reporte (Esto sí se mantiene igual)
            $reporte->estado = 'Aprobado';
            $reporte->observacion_admin = 'Validado el ' . now()->format('d/m/Y H:i');
            $reporte->save();

            // 3. ACTUALIZAR LA CUOTA (Adaptado a tu tabla real)
            $cuota = Cuota::findOrFail($reporte->cuota_id);
            
            // --- MAPEO DE COLUMNAS CORRECTO ---
            $cuota->estado_cuota = 'Pagada'; // Tu enum es 'Pagada', no 'Pagado'
            $cuota->fecha_pago = Carbon::now();
            $cuota->metodo_pago = $reporte->metodo_pago; // Columna 'metodo_pago'
            $cuota->transaccion_id = $reporte->numero_operacion; // Columna 'transaccion_id'
            
            // NOTA: Como no tienes columna 'monto_pagado' ni 'saldo_pendiente',
            // asumimos que al cambiar el estado a 'Pagada', la deuda es cero.
            
            $cuota->save();
        });

        return back()->with('success', 'Pago aprobado y cuota actualizada correctamente.');
    }


    public function rechazar(Request $request, $id)
    {
        $reporte = ReportePago::findOrFail($id);
        
        if($reporte->estado != 'Pendiente') {
            return back()->with('error', 'Este pago ya fue procesado.');
        }

        $reporte->estado = 'Rechazado';
        // Puedes recibir un motivo desde el formulario si quieres ser más específico
        $reporte->observacion_admin = 'Comprobante no válido o ilegible. Por favor contactar a soporte.';
        $reporte->save();

        return back()->with('error', 'El pago ha sido rechazado.');
    }
}
