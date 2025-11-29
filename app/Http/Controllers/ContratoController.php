<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Venta;
use App\Models\Cuota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


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
            $this->crearUsuarioAlumno($venta->cliente);
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
        
        // La fecha base es el inicio del curso
        // Usamos copy() para no alterar la fecha original si la necesitamos después
        $fechaBase = Carbon::parse($venta->grupo->fecha_inicio); 

        // 1. Crear la cuota de Matrícula (si aplica)
        if ($matricula > 0) {
            Cuota::create([
                'venta_id' => $venta->id,
                'descripcion' => 'Matrícula',
                'monto_cuota' => $matricula,
                'fecha_vencimiento' => $fechaBase, // Vence el día de inicio
                'estado_cuota' => 'Pendiente',
            ]);
        }

        // 2. Calcular las cuotas del saldo
        if ($nroCuotas > 0 && $montoFinanciado > 0) {
            
            // Asumimos que 'nro_cuotas_venta' es el TOTAL de pagos
            // Si hay matrícula, las cuotas restantes son (Total - 1)
            $nroCuotasReales = $nroCuotas;
            
            if ($nroCuotasReales > 0) {
                $montoPorCuota = $montoFinanciado / $nroCuotasReales;
                
                // --- CORRECCIÓN AQUÍ ---
                // Antes hacíamos $fechaBase->addMonth() aquí. LO QUITAMOS.
                // Ahora usamos una variable temporal para iterar.
                
                $fechaCuota = $fechaBase->copy(); // Empezamos en la fecha de inicio

                for ($i = 1; $i <= $nroCuotasReales; $i++) {
                    Cuota::create([
                        'venta_id' => $venta->id,
                        'descripcion' => "Cuota $i de $nroCuotasReales",
                        'monto_cuota' => $montoPorCuota,
                        'fecha_vencimiento' => $fechaCuota, // Cuota 1 = Fecha Inicio
                        'estado_cuota' => 'Pendiente',
                    ]);
                    
                    // DESPUÉS de crear la cuota, sumamos un mes para la siguiente
                    $fechaCuota->addMonth();
                }
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

    private function crearUsuarioAlumno($cliente)
    {
        if ($cliente->user_id) return;

        // Usamos el DNI como usuario. Si no tiene DNI, usamos el email como fallback.
        $usuarioLogin = $cliente->numero_documento ?? $cliente->email;
        
        // Buscamos por username O por email para no duplicar
        $user = User::where('username', $usuarioLogin)
                    ->orWhere('email', $cliente->email)
                    ->first();

        if (!$user) {
            // Contraseña = DNI
            $password = $cliente->numero_documento ?? '12345678'; 
            
            $user = User::create([
                'name' => $cliente->nombre_completo,
                'email' => $cliente->email, // Seguimos guardando el email para notificaciones
                'username' => $usuarioLogin, // <-- AQUÍ GUARDAMOS EL DNI
                'password' => Hash::make($password),
            ]);
            
            $user->assignRole('Cliente');
        }

        $cliente->update(['user_id' => $user->id]);
    }
}
