<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\ReportePago;
use Carbon\Carbon;
use App\Models\MetodoPago;

class PortalController extends Controller
{
    public function index()
    {
        // 1. Identificar al alumno
        $user = Auth::user();
        $cliente = $user->cliente; 

        if (!$cliente) {
            // Si por error no tiene cliente asociado
            return view('portal.error', ['msg' => 'Usuario sin perfil de estudiante.']);
        }

        // 2. Obtener TODOS sus cursos (Ventas) con las cuotas y programas
        // Asumimos relaciones: venta->grupo->programa y venta->cuotas
        $ventas = $cliente->ventas()->with(['grupo.programa', 'cuotas'])->get();

        // 3. Inicializar variables del Semáforo Global
        $deudaTotal = 0;
        $cuotaMasProxima = null; // Guardará la cuota que vence más pronto (o la ya vencida)
        $estadoSemaforo = 'verde'; // Por defecto, todo bien
        $mensajeSemaforo = 'Estás al día con tus pagos';
        $diasRestantes = 0;

        // 4. Recorrer todas las ventas para calcular deuda y buscar la fecha clave
        foreach ($ventas as $venta) {
            
            // Sumar deuda de cuotas pendientes o parciales
            $cuotasPendientes = $venta->cuotas->whereIn('estado_cuota', ['Pendiente', 'Parcial']);
            $deudaTotal += $cuotasPendientes->sum('saldo_pendiente'); // Asegúrate que tu BD tenga este campo o usa 'monto_cuota'

            // Buscar la fecha más crítica
            foreach ($cuotasPendientes as $cuota) {
                // Si es la primera que encontramos o si esta vence ANTES que la que teníamos guardada
                if (!$cuotaMasProxima || $cuota->fecha_vencimiento < $cuotaMasProxima->fecha_vencimiento) {
                    $cuotaMasProxima = $cuota;
                }
            }
        }

        // 5. Definir el Color del Semáforo (Si hay deuda)
        if ($cuotaMasProxima) {
            $hoy = Carbon::now()->startOfDay();
            $vencimiento = Carbon::parse($cuotaMasProxima->fecha_vencimiento)->startOfDay();
            
            // Calculamos la diferencia en días
            // Si venció ayer: -1. Si vence mañana: +1.
            $diasDiff = $hoy->diffInDays($vencimiento, false); 
            $diasRestantes = abs($diasDiff); // Valor absoluto para mostrar "hace 5 días" o "en 5 días"

            if ($diasDiff < 0) {
                // YA VENCIÓ (Atrasado)
                $estadoSemaforo = 'rojo';
                $mensajeSemaforo = "¡Tienes pagos atrasados por $diasRestantes días!";
            } elseif ($diasDiff <= 3) {
                // POR VENCER (3 días o menos)
                $estadoSemaforo = 'amarillo';
                $mensajeSemaforo = "Tu próximo pago vence pronto (en $diasRestantes días)";
            } else {
                // FUTURO (Falta bastante)
                $estadoSemaforo = 'verde';
                $mensajeSemaforo = "Tu próximo pago vence en $diasRestantes días";
            }
        }

        return view('portal.home', compact('cliente', 'ventas', 'deudaTotal', 'estadoSemaforo', 'mensajeSemaforo', 'diasRestantes', 'cuotaMasProxima'));
    }
    public function pagos()
    {
        $user = auth()->user();
        $cliente = $user->cliente;
        $ventas = $cliente->ventas()
            ->with(['grupo.programa', 'contrato', 'cuotas' => function($query) {
                $query->orderBy('fecha_vencimiento', 'asc');
            }])
            ->get();

        $metodosPago = MetodoPago::where('estado', 'Activo')->get();
        return view('portal.pagos', compact('ventas', 'metodosPago'));
    }

    public function reportarPago(Request $request)
    {
        $request->validate([
            'venta_id' => 'required|exists:ventas,id',
            'cuota_id' => 'required|exists:cuotas,id',
            'monto' => 'required|numeric|min:1',
            'metodo_pago' => 'required|string',
            'numero_operacion' => 'required|string',
            'comprobante' => 'required|image|max:15360', 
        ]);

        $rutaImagen = null;
        if ($request->hasFile('comprobante')) {
            $rutaImagen = $request->file('comprobante')->store('comprobantes', 'public');
        }

        ReportePago::create([
            'venta_id' => $request->venta_id,
            'cuota_id' => $request->cuota_id,
            'monto' => $request->monto,
            'metodo_pago' => $request->metodo_pago,
            'numero_operacion' => $request->numero_operacion,
            'comprobante_imagen' => $rutaImagen,
            'estado' => 'Pendiente'
        ]);
        return back()->with('success', '¡Comprobante enviado! Validaremos tu pago en breve.');
    }
}
