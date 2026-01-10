<?php

namespace App\Exports;

use App\Models\Venta;
use Illuminate\Contracts\View\View; 
use Maatwebsite\Excel\Concerns\FromView; 
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Auth;


class VentasExport implements FromView, ShouldAutoSize
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
        $query = Venta::with(['cliente', 'grupo.programa', 'vendedor'])
            ->whereDate('fecha_venta', '>=', $this->fechaInicio)
            ->whereDate('fecha_venta', '<=', $this->fechaFin)
            ->where('estado', '!=', 'Anulada');

        if (!Auth::user()->hasRole('Admin')) {
            $query->where('vendedor_id', Auth::id());
        }

        $ventas = $query->get();

        return view('exports.ventas', [
            'ventas' => $ventas,
            'fechaInicio' => $this->fechaInicio,
            'fechaFin' => $this->fechaFin
        ]);
    }
}