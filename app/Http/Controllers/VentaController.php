<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Grupo;
use App\Models\Programa;
use App\Models\User;
use App\Models\Contrato;
use App\Models\Cuota; // Importante: Agregado para el cronograma
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::with(['cliente', 'grupo.programa', 'vendedor', 'contrato']);

        if (!Auth::user()->hasRole('Admin')) {
            $query->where('vendedor_id', Auth::id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($qMain) use ($search) {
                $qMain->whereHas('cliente', function($q) use ($search) {
                    $q->where('nombre', 'like', "%$search%")
                        ->orWhere('apellido', 'like', "%$search%")
                        ->orWhere('numero_documento', 'like', "%$search%");
                })
                ->orWhereHas('grupo', function($q) use ($search) {
                    $q->where('codigo_grupo', 'like', "%$search%");
                });
            });
        }

        if ($request->filled('programa_id')) {
            $query->whereHas('grupo', function($q) use ($request) {
                $q->where('programa_id', $request->programa_id);
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if (Auth::user()->hasRole('Admin') && $request->filled('vendedor_id')) {
            $query->where('vendedor_id', $request->vendedor_id);
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_venta', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_venta', '<=', $request->fecha_fin);
        }

        $ventas = $query->latest('fecha_venta')->paginate(20);

        $programas = Programa::where('estado', 'Activo')->get();
        
        $vendedores = Auth::user()->hasRole('Admin') 
            ? User::role(['Asesor', 'Admin'])->get() 
            : [];

        return view('ventas.index', compact('ventas', 'programas', 'vendedores'));
    }

    public function create(Request $request)
    {
        $cliente_id = $request->query('cliente_id');
        
        if (!$cliente_id) {
            return redirect()->route('clientes.index')->with('error', 'Debe seleccionar un cliente primero.');
        }

        $cliente = Cliente::findOrFail($cliente_id);

        if (empty($cliente->numero_documento) || empty($cliente->direccion)) {
            return redirect()
                ->route('clientes.edit', ['cliente' => $cliente->id, 'next' => 'matricula'])
                ->with('warning', 'Para generar el contrato, primero necesitamos completar el DNI y la Dirección del cliente.');
        }

        $gruposDisponibles = Grupo::where('estado', 'Próximo')
                                    ->with('programa')
                                    ->get();

        return view('ventas.create', compact('cliente', 'gruposDisponibles'));
    }

    public function store(Request $request)
    {
        // 1. Validaciones
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'grupo_id'   => 'required|exists:grupos,id',
        ]);

        $cliente = Cliente::findOrFail($data['cliente_id']);
        $grupo = Grupo::with('programa.plantillaContrato')->findOrFail($data['grupo_id']);

        // 2. Anti-duplicados
        $existe = Venta::where('cliente_id', $cliente->id)
                        ->where('grupo_id', $grupo->id)
                        ->where('estado', '!=', 'Anulada')
                        ->exists();

        if ($existe) {
            return back()->with('error', "El cliente {$cliente->nombre} {$cliente->apellido} ya está registrado en este grupo.");
        }

        try {
            DB::beginTransaction();

            // A. Crear la Venta (Cabecera)
            $venta = Venta::create([
                'cliente_id'  => $cliente->id,
                'grupo_id'    => $grupo->id,
                'vendedor_id' => Auth::id(),
                'fecha_venta' => now(),
                'estado'      => 'En Proceso',
                'costo_total_venta'       => $grupo->costo_total ?? 0,
                'costo_matricula_venta'   => $grupo->costo_matricula ?? 0,
                'nro_cuotas_venta'        => $grupo->numero_cuotas ?? 1,
                'texto_promocional_venta' => $grupo->texto_promocional,
            ]);

            // B. Delegar creación de Cuotas
            $this->generarCuotas($venta, $grupo);

            // C. Delegar creación del Contrato
            $this->generarContrato($venta, $cliente, $grupo);

            // D. Actualizar Cliente
            if ($cliente->estado === 'Prospecto') {
                $cliente->update(['estado' => 'En Proceso']);
            }

            DB::commit();

            return redirect()->route('ventas.previsualizar', $venta->id)
                ->with('success', 'Matrícula generada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error crítico: ' . $e->getMessage());
        }
    }


    private function generarCuotas(Venta $venta, Grupo $grupo)
    {
        // Definir Fecha Base (Inicio de Clases o Hoy)
        $fechaBase = $grupo->fecha_inicio ? Carbon::parse($grupo->fecha_inicio) : now();

        // 1. Crear Matrícula (Vence el mismo día que inicia el curso)
        if ($venta->costo_matricula_venta > 0) {
            Cuota::create([
                'venta_id'          => $venta->id,
                'descripcion'       => 'Matrícula',
                'monto_cuota'       => $venta->costo_matricula_venta,
                'fecha_vencimiento' => $fechaBase, 
                'estado_cuota'      => 'Pendiente',
            ]);
        }

        // 2. Crear Mensualidades
        if ($venta->nro_cuotas_venta > 0 && $venta->costo_total_venta > 0) {
            $montoMensual = $venta->costo_total_venta / $venta->nro_cuotas_venta;

            for ($i = 1; $i <= $venta->nro_cuotas_venta; $i++) {
                Cuota::create([
                    'venta_id'          => $venta->id,
                    'descripcion'       => "Cuota $i de {$venta->nro_cuotas_venta}",
                    'monto_cuota'       => $montoMensual,
                    // Cuota 1 = Fecha Base, Cuota 2 = Fecha Base + 1 mes...
                    'fecha_vencimiento' => $fechaBase->copy()->addMonths($i - 1),
                    'estado_cuota'      => 'Pendiente',
                ]);
            }
        }
    }

    private function generarContrato(Venta $venta, Cliente $cliente, Grupo $grupo)
    {
        // Obtener plantilla base
        $plantilla = $grupo->programa->plantillaContrato;
        $contenidoBase = $plantilla ? $plantilla->contenido : '<p>Error: No hay plantilla asignada.</p>';
        $plantillaId = $plantilla ? $plantilla->id : null;

        // Reemplazar variables (Usa el helper existente)
        $htmlFinal = $this->reemplazarPlaceholders($contenidoBase, $cliente, $grupo, $venta);

        // Crear registro
        Contrato::create([
            'venta_id'              => $venta->id,
            'plantilla_contrato_id' => $plantillaId,
            'token_acceso'          => Str::random(40),
            'contenido_generado'    => $htmlFinal,
            'estado'                => 'Pendiente',
        ]);
    }

    private function reemplazarPlaceholders($html, $cliente, $grupo, $venta)
    {
        // 1. Duración en meses
        if ($grupo->fecha_inicio && $grupo->fecha_termino) {
            $inicio = Carbon::parse($grupo->fecha_inicio);
            $fin = Carbon::parse($grupo->fecha_termino);
            $meses = $inicio->diffInMonths($fin);
            $textoDuracion = ($meses < 1) ? 'Menos de 1 mes' : $meses . ' MESES';
        } else {
            $textoDuracion = '6 MESES';
        }

        // 2. Cálculo cuota
        $costoTotal = $venta->costo_total_venta ?? 0;
        $nroCuotas = $venta->nro_cuotas_venta > 0 ? $venta->nro_cuotas_venta : 1;
        $montoCuota = $costoTotal / $nroCuotas;

        // 3. Reemplazo
        $variables = [
            '{{nombre_programa}}' => strtoupper($grupo->programa->nombre),
            '{{nombre_alumno}}'   => strtoupper($cliente->nombre . ' ' . $cliente->apellido),
            '{{dni_alumno}}'      => $cliente->numero_documento,
            '{{modalidad}}'       => strtoupper($grupo->modalidad ?? 'Presencial'),
            '{{duracion}}'        => $textoDuracion,
            '{{horario}}'         => $grupo->horario_texto ?? 'Por definir',
            '{{monto_cuota}}'     => number_format($montoCuota, 2),
        ];

        foreach ($variables as $key => $value) {
            $html = str_replace($key, $value, $html);
        }

        return $html;
    }

    public function previsualizar($id)
    {
        $venta = Venta::with(['cliente', 'contrato', 'grupo.programa'])->findOrFail($id);
        
        $linkFirma = route('contratos.publico', $venta->contrato->token_acceso);
        
        $nombre = $venta->cliente->nombre;
        $mensaje = "Hola $nombre, aquí tienes tu contrato de matrícula. Por favor fírmalo para finalizar tu inscripción: $linkFirma";
        $linkWhatsApp = "https://wa.me/51" . $venta->cliente->telefono . "?text=" . urlencode($mensaje);

        return view('ventas.previsualizar', compact('venta', 'linkFirma', 'linkWhatsApp'));
    }

    public function anular(Request $request, Venta $venta)
    {
        if ($venta->estado == 'Anulada') {
            return back()->with('error', 'Esta venta ya está anulada.');
        }

        try {
            DB::beginTransaction();

            $venta->update(['estado' => 'Anulada']);

            // Eliminar cuotas pendientes (mantener pagadas si las hubiera)
            $venta->cuotas()->where('estado_cuota', '!=', 'Pagada')->delete();
            
            $venta->cliente->update(['estado' => 'Finalizado']); 

            DB::commit();

            return back()->with('success', 'Venta anulada correctamente. La deuda pendiente ha sido eliminada.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al anular: ' . $e->getMessage());
        }
    }
}