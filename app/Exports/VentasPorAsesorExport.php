<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Para que las columnas se ajusten solas
use Maatwebsite\Excel\Concerns\WithTitle;

class VentasPorAsesorExport implements FromView, ShouldAutoSize, WithTitle
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
        // Consulta: Ranking de Asesores
        $asesores = User::role(['Asesor', 'Admin'])
            ->withCount(['ventas' => function ($query) {
                $query->whereBetween('fecha_venta', [$this->fechaInicio, $this->fechaFin])
                      ->where('estado', '!=', 'Anulada');
            }])
            ->having('ventas_count', '>', 0) // Solo los que vendieron algo
            ->orderBy('ventas_count', 'desc')
            ->get();

        return view('exports.ventas_asesor', [
            'asesores' => $asesores,
            'fechaInicio' => $this->fechaInicio,
            'fechaFin' => $this->fechaFin
        ]);
    }

    public function title(): string
    {
        return 'Ranking Asesores';
    }
}