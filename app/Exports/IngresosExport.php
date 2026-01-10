<?php

namespace App\Exports;

use App\Models\Cuota;
use Illuminate\Contracts\View\View; // Importante
use Maatwebsite\Excel\Concerns\FromView; // Usamos FromView
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Auth;

class IngresosExport implements FromView, ShouldAutoSize
{
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function view(): View
    {
        // 1. Construimos la consulta
        // Agregué 'venta.grupo.programa' para acceder al nombre del programa optimizado
        $query = Cuota::with(['venta.cliente', 'venta.grupo.programa']) 
                        ->where('estado_cuota', 'Pagada')
                        ->whereDate('fecha_pago', '>=', $this->fechaInicio)
                        ->whereDate('fecha_pago', '<=', $this->fechaFin);

        // 2. Filtro de seguridad (Si no es Admin, solo ve ingresos de SUS ventas)
        if (!Auth::user()->hasRole('Admin')) {
            $query->whereHas('venta', function($q) {
                $q->where('vendedor_id', Auth::id());
            });
        }

        // 3. Obtenemos los datos
        $cuotas = $query->get();

        // 4. Retornamos la vista con los datos
        return view('exports.ingresos', [
            'cuotas' => $cuotas,
            'fechaInicio' => $this->fechaInicio,
            'fechaFin' => $this->fechaFin
        ]);
    }
}