<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use App\Models\Cliente; 
use App\Models\Grupo;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Str; 
use Carbon\Carbon;
use App\Models\Contrato;



class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Usamos with() para cargar las relaciones y evitar N+1 queries
        // Cargamos el cliente, el grupo (y el programa dentro del grupo) y el vendedor
        $query = Venta::with(['cliente', 'grupo.programa', 'vendedor']);

        // El Admin ve TODAS las ventas
        if (Auth::user()->hasRole('Admin')) {
            $ventas = $query->latest()->get(); // latest() ordena por fecha de creación desc
        } else {
            // El Asesor ve SOLO sus ventas
            $ventas = $query->where('vendedor_id', Auth::id())
                            ->latest()
                            ->get();
        }

        return view('ventas.index', compact('ventas'));
    }

    // ... (Aquí dejaremos los otros métodos vacíos por ahora)
    public function create(Request $request)
    {
        $cliente_id = $request->query('cliente_id');
        
        if (!$cliente_id) {
            return redirect()->route('clientes.index')->with('error', 'Debe seleccionar un cliente primero.');
        }

        $cliente = Cliente::findOrFail($cliente_id);

        // --- NUEVA VALIDACIÓN (La Guardia) ---
        // Verificamos si faltan datos CRÍTICOS para el contrato
        if (empty($cliente->numero_documento) || empty($cliente->direccion)) {
            
            // Si faltan datos, lo mandamos a editar, pero le pasamos una bandera 'next=matricula'
            return redirect()
                ->route('clientes.edit', ['cliente' => $cliente->id, 'next' => 'matricula'])
                ->with('warning', 'Para generar el contrato, primero necesitamos completar el DNI y la Dirección del cliente.');
        }
        // -------------------------------------

        // (El resto de tu código sigue igual...)
        $gruposDisponibles = Grupo::where('estado', 'Próximo')
                                    ->with('programa')
                                    ->get();

        return view('ventas.create', compact('cliente', 'gruposDisponibles'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'grupo_id' => 'required|exists:grupos,id',
        ]);
        // 1. Obtenemos los datos PRIMERO (para poder usar los nombres)
        $cliente = Cliente::findOrFail($data['cliente_id']);
        // Cargamos también el programa para dar más detalles
        $grupo = Grupo::with('programa')->findOrFail($data['grupo_id']);

        // 2. 🛡️ VALIDACIÓN ANTI-DUPLICADOS PERSONALIZADA
        $existeMatricula = Venta::where('cliente_id', $cliente->id)
                                ->where('grupo_id', $grupo->id)
                                ->where('estado', '!=', 'Anulada')
                                ->exists();

        if ($existeMatricula) {
            // Construimos el mensaje personalizado que pediste
            $mensajeError = "{$cliente->nombre_completo} ya se encuentra matriculado en el grupo {$grupo->codigo_grupo} ({$grupo->programa->nombre}).";
            
            return back()->with('error', $mensajeError);
        }

        // 1. Iniciar la Transacción
        // Si algo falla, se revierte todo (no quedan registros 'huérfanos')
        try {
            DB::beginTransaction();

            // 2. Obtener los modelos clave
            $cliente = Cliente::findOrFail($data['cliente_id']);
            $grupo = Grupo::with('programa.plantillaContrato')->findOrFail($data['grupo_id']);
            $vendedor = Auth::user();
            
            // 3. Crear la VENTA (El "Snapshot" financiero)
            $venta = Venta::create([
                'cliente_id' => $cliente->id,
                'grupo_id' => $grupo->id,
                'vendedor_id' => $vendedor->id,
                'fecha_venta' => now(), // ¡Venta creada hoy!
                'estado' => 'En Proceso', // <-- Estado inicial
                
                // "Snapshot" de los datos financieros del grupo
                'costo_total_venta' => $grupo->costo_total,
                'costo_matricula_venta' => $grupo->costo_matricula,
                'nro_cuotas_venta' => $grupo->numero_cuotas,
                'texto_promocional_venta' => $grupo->texto_promocional,
            ]);

            // 4. Crear el CONTRATO (Pendiente)
            
            // 4a. Obtener la plantilla y reemplazar placeholders
            $plantilla = $grupo->programa->plantillaContrato;
            $contenido = $this->reemplazarPlaceholders($plantilla->contenido_html, $cliente, $grupo, $venta);

            // 4b. Crear el registro
            $contrato = Contrato::create([
                'venta_id' => $venta->id,
                'plantilla_contrato_id' => $plantilla->id,
                'token_acceso' => Str::random(40), // Token único para el link
                'contenido_generado' => $contenido, // El HTML renderizado
                'estado' => 'Pendiente',
            ]);

            // 5. Actualizar el estado del Cliente
            $cliente->update(['estado' => 'En Proceso']);
            
            // 6. Si todo salió bien, confirma la transacción
            DB::commit();

            // 7. Redirigir a la página de "Mostrar Contrato en Tablet"
            // (Esta ruta la crearemos a continuación)
            return redirect()->route('contratos.mostrar', $contrato->token_acceso)
                        ->with('success', '¡Venta y Contrato generados! Pendiente de confirmación del cliente.');

        } catch (\Exception $e) {
            // 6b. Si algo falló, revierte todo
            DB::rollBack();
            
            // Muestra el error
            return back()->with('error', 'Error al generar la matrícula: ' . $e->getMessage());
        }
    }

    /**
     * Función privada para reemplazar los placeholders del contrato.
     */
    private function reemplazarPlaceholders($contenido, $cliente, $grupo, $venta)
    {
        $reemplazos = [
            '[CLIENTE_NOMBRE]' => $cliente->nombre_completo,
            '[CLIENTE_DNI]' => $cliente->numero_documento ?? 'N/A',
            '[CLIENTE_DIRECCION]' => $cliente->direccion ?? 'N/A',
            '[PROGRAMA_NOMBRE]' => $grupo->programa->nombre,
            '[CODIGO_GRUPO]' => $grupo->codigo_grupo,
            '[GRUPO_MODALIDAD]' => $grupo->modalidad,
            '[GRUPO_FECHA_INICIO]' => $grupo->fecha_inicio->format('d/m/Y'),
            '[GRUPO_FECHA_TERMINO]' => $grupo->fecha_termino->format('d/m/Y'),
            '[GRUPO_HORARIO_TEXTO]' => $grupo->horario_texto,
            '[GRUPO_COSTO_TOTAL]' => number_format($venta->costo_total_venta, 2),
            '[COSTO_MATRICULA]' => number_format($venta->costo_matricula_venta, 2),
            '[NUMERO_CUOTAS]' => $venta->nro_cuotas_venta,
            '[COSTO_TOTAL_LETRAS]' => '(PENDIENTE CONVERSOR A LETRAS)', // (Esto es más complejo)
            '[FECHA_ACEPTACION_DIGITAL]' => '...', // (Se llenará al firmar)
        ];

        return str_replace(array_keys($reemplazos), array_values($reemplazos), $contenido);
    }
    /**
     * Anula una venta (Retiro del alumno).
     */
    public function anular(Request $request, Venta $venta)
    {
        // 1. Validar que no esté ya anulada
        if ($venta->estado == 'Anulada') {
            return back()->with('error', 'Esta venta ya está anulada.');
        }

        try {
            DB::beginTransaction();

            // 2. Actualizar estado de la VENTA
            $venta->update(['estado' => 'Anulada']);

            // 3. Gestionar las CUOTAS
            // Opción A: Borramos SOLO las cuotas que NO han sido pagadas (la deuda futura)
            $venta->cuotas()->where('estado_cuota', '!=', 'Pagada')->delete();
            
            // (Si quisieras devolver el dinero de las pagadas, sería otro proceso, 
            // pero por ahora asumimos que lo pagado, pagado está).

            // 4. Actualizar estado del CONTRATO (si existe)
            if ($venta->contrato) {
                // Podrías tener un estado 'Anulado' en contratos también, 
                // o simplemente dejarlo como constancia histórica.
                // $venta->contrato->update(['estado' => 'Anulado']); 
            }

            // 5. Liberar al CLIENTE
            // Lo regresamos a 'Prospecto' o le ponemos un estado 'Retirado'
            // Para tu sistema actual, 'Finalizado' o regresar a 'Prospecto' funciona.
            $venta->cliente->update(['estado' => 'Finalizado']); 

            DB::commit();

            return back()->with('success', 'Venta anulada correctamente. La deuda pendiente ha sido eliminada.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al anular: ' . $e->getMessage());
        }
    }
    public function show(Venta $venta) {}
    public function edit(Venta $venta) {}
    public function update(Request $request, Venta $venta) {}
    public function destroy(Venta $venta) {}
}
