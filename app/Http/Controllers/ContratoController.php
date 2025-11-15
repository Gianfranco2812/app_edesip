<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Venta;
use App\Models\Cuota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ContratoController extends Controller
{
    public function mostrar($token_acceso)
    {
        $contrato = Contrato::where('token_acceso', $token_acceso)->firstOrFail();

        // Seguridad: Si el contrato ya está 'Confirmado', no se puede volver a firmar.
        if ($contrato->estado == 'Confirmado') {
            return view('contratos.confirmado', compact('contrato'))
                    ->with('warning', 'Este contrato ya fue confirmado anteriormente.');
        }

        // Mostramos la vista de confirmación
        return view('contratos.mostrar', compact('contrato'));
    }

    /**
     * PASO 4: Procesa la confirmación ("magia")
     * (Se ejecuta cuando el cliente presiona "Aceptar").
     */
    public function confirmar(Request $request, $token_acceso)
    {
        $contrato = Contrato::where('token_acceso', $token_acceso)->firstOrFail();

        // Doble chequeo por si acaso
        if ($contrato->estado == 'Confirmado') {
            return redirect()->route('contratos.mostrar', $contrato->token_acceso)
                        ->with('warning', 'Este contrato ya fue confirmado anteriormente.');
        }

        // 1. Iniciar la Transacción
        try {
            DB::beginTransaction();

            // 2. Confirmar CONTRATO
            $contrato->update([
                'estado' => 'Confirmado',
                'fecha_confirmacion' => now(),
                'ip_confirmacion' => $request->ip()
            ]);

            // 3. Cerrar VENTA
            $venta = $contrato->venta; // Obtenemos la venta desde la relación
            $venta->update(['estado' => 'Cerrada']);

            // 4. Confirmar CLIENTE
            $venta->cliente->update(['estado' => 'Confirmado']);

            // 5. Generar COBRANZA (Plan de Pagos)
            $this->generarPlanDePagos($venta);

            // 6. Si todo salió bien, confirma la transacción
            DB::commit();

            // (Aquí iría la lógica de enviar el email con el PDF)
            // Mail::to($venta->cliente->email)->send(new ContratoConfirmado($contrato));

            // 7. Redirigir a una vista de éxito
            return redirect()->route('contratos.exito');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error crítico al confirmar el contrato: ' . $e->getMessage());
        }
    }

    /**
     * Función privada para generar las cuotas (Paso 4c).
     */
    private function generarPlanDePagos(Venta $venta)
    {
        $costoTotal = $venta->costo_total_venta;
        $matricula = $venta->costo_matricula_venta;
        $nroCuotas = $venta->nro_cuotas_venta;
        
        $montoFinanciado = $costoTotal - $matricula;
        $fechaVencimiento = Carbon::parse($venta->grupo->fecha_inicio); // Empezamos a cobrar desde el inicio

        // 1. Crear la cuota de Matrícula (si aplica)
        if ($matricula > 0) {
            Cuota::create([
                'venta_id' => $venta->id,
                'descripcion' => 'Matrícula',
                'monto_cuota' => $matricula,
                'fecha_vencimiento' => $fechaVencimiento, // La matrícula se paga al inicio
                'estado_cuota' => 'Pendiente',
            ]);
        }

        // 2. Calcular las cuotas del saldo (si nroCuotas > 0)
        // Si la matrícula fue el costo total (pago único), nroCuotas del saldo es 0
        if ($nroCuotas > 0 && $montoFinanciado > 0) {
            
            // Si la matrícula fue 0, el nroCuotas se aplica al total
            // Si la matrícula fue > 0, (nroCuotas - 1) se aplica al saldo? 
            // Asumiremos que 'nro_cuotas_venta' es el TOTAL de pagos (incluyendo matrícula si es > 0)
            
            $nroCuotasReales = ($matricula > 0) ? $nroCuotas - 1 : $nroCuotas;
            if ($nroCuotasReales <= 0) { // Caso pago total = matrícula
                return;
            }

            $montoPorCuota = $montoFinanciado / $nroCuotasReales;
            
            // Avanzamos el vencimiento al siguiente mes para la Cuota 1
            $fechaVencimiento->addMonth(); 

            for ($i = 1; $i <= $nroCuotasReales; $i++) {
                Cuota::create([
                    'venta_id' => $venta->id,
                    'descripcion' => "Cuota $i de $nroCuotasReales",
                    'monto_cuota' => $montoPorCuota,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'estado_cuota' => 'Pendiente',
                ]);
                // Siguiente vencimiento en 1 mes
                $fechaVencimiento->addMonth();
            }
        }
    }

    /**
     * Muestra la página de "Éxito".
     */
    public function exito()
    {
        return view('contratos.exito'); // Crearemos esta vista
    }
}
