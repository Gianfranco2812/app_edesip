<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cuota;
use App\Notifications\AlertaDeudaNotification;
use Carbon\Carbon;

class VerificarVencimientos extends Command
{
    protected $signature = 'cobranza:verificar';

    // La descripción
    protected $description = 'Revisa cuotas por vencer (3 días) y vencidas (hoy) y notifica a los asesores.';

    public function handle()
    {
        $this->info('Iniciando verificación de cobranzas...');

        // FECHAS CLAVE
        $hoy = Carbon::now()->startOfDay(); // Hoy (Para las rojas)
        $enTresDias = Carbon::now()->addDays(3)->startOfDay(); // En 3 días (Para las naranjas)

        // ----------------------------------------------------
        // 1. BUSCAR CUOTAS POR VENCER (NARANJA - 3 DÍAS ANTES)
        // ----------------------------------------------------
        $cuotasPorVencer = Cuota::with('venta.vendedor')
                                ->where('estado_cuota', 'Pendiente')
                                ->whereDate('fecha_vencimiento', $enTresDias) // Exactamente en 3 días
                                ->get();

        foreach ($cuotasPorVencer as $cuota) {
            // Notificamos al vendedor asignado
            if ($cuota->venta->vendedor) {
                $cuota->venta->vendedor->notify(new AlertaDeudaNotification($cuota, 'warning'));
                $this->info("Notificación NARANJA enviada para cuota ID: {$cuota->id}");
            }
        }

        // ----------------------------------------------------
        // 2. BUSCAR CUOTAS RECIÉN VENCIDAS (ROJA - EL DÍA DEL VENCIMIENTO O EL SIGUIENTE)
        // ----------------------------------------------------
        // Usamos 'subDay()' para avisar al día siguiente del vencimiento ("Oye, venció ayer")
        // O usamos 'now()' para avisar el mismo día ("Oye, vence hoy, cobra ya!")
        // Vamos a usar el mismo día (hoy) como "Vencida/Urgente".
        
        $cuotasVencidas = Cuota::with('venta.vendedor')
                                ->where('estado_cuota', 'Pendiente')
                                ->whereDate('fecha_vencimiento', $hoy) // Vence HOY (Urgente)
                                ->get();

        foreach ($cuotasVencidas as $cuota) {
            if ($cuota->venta->vendedor) {
                $cuota->venta->vendedor->notify(new AlertaDeudaNotification($cuota, 'danger'));
                $this->info("Notificación ROJA enviada para cuota ID: {$cuota->id}");
            }
        }

        $this->info('Verificación completada exitosamente.');
    }
}
