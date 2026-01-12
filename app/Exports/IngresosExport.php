<?php

namespace App\Exports;

use App\Models\Cuota;
use Illuminate\Contracts\View\View; 
use Maatwebsite\Excel\Concerns\FromView; 
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
    
        $query = Cuota::with(['venta.cliente', 'venta.grupo.programa']) 
                        ->where('estado_cuota', 'Pagada')
                        ->whereDate('fecha_pago', '>=', $this->fechaInicio)
                        ->whereDate('fecha_pago', '<=', $this->fechaFin);

        if (!Auth::user()->hasRole('Admin')) {
            $query->whereHas('venta', function($q) {
                $q->where('vendedor_id', Auth::id());
            });
        }

        $cuotas = $query->get();

        return view('exports.ingresos', [
            'cuotas' => $cuotas,
            'fechaInicio' => $this->fechaInicio,
            'fechaFin' => $this->fechaFin
        ]);
    }
}