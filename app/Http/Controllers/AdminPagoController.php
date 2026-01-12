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
            
            $reporte = ReportePago::findOrFail($id);
            
            if($reporte->estado != 'Pendiente') {
                throw new \Exception('Este pago ya fue procesado anteriormente.');
            }
            $reporte->estado = 'Aprobado';
            $reporte->observacion_admin = 'Validado el ' . now()->format('d/m/Y H:i');
            $reporte->save();

            $cuota = Cuota::findOrFail($reporte->cuota_id);
            
            $cuota->estado_cuota = 'Pagada'; 
            $cuota->fecha_pago = Carbon::now();
            $cuota->metodo_pago = $reporte->metodo_pago; 
            $cuota->transaccion_id = $reporte->numero_operacion; 
            
            
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
        $reporte->observacion_admin = 'Comprobante no válido o ilegible. Por favor contactar a soporte.';
        $reporte->save();

        return back()->with('error', 'El pago ha sido rechazado.');
    }
}
