<?php

namespace App\Exports;

use App\Models\Venta;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Auth;

class VentasExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $fechaInicio;
    protected $fechaFin;

    // Recibimos los filtros al crear la clase
    public function __construct($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function query()
    {
        $query = Venta::with(['cliente', 'grupo.programa', 'vendedor'])
                      ->whereDate('fecha_venta', '>=', $this->fechaInicio)
                      ->whereDate('fecha_venta', '<=', $this->fechaFin)
                      ->where('estado', '!=', 'Anulada'); // Opcional: ¿Quieres ver las anuladas?

        if (!Auth::user()->hasRole('Admin')) {
            $query->where('vendedor_id', Auth::id());
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID Venta',
            'Fecha Venta',
            'Cliente',
            'DNI/Doc',
            'Programa',
            'Grupo',
            'Costo Total',
            'Estado',
            'Vendedor',
        ];
    }

    public function map($venta): array
    {
        return [
            $venta->id,
            $venta->fecha_venta->format('d/m/Y'),
            $venta->cliente->nombre_completo,
            $venta->cliente->numero_documento,
            $venta->grupo->programa->nombre,
            $venta->grupo->codigo_grupo,
            $venta->costo_total_venta,
            $venta->estado,
            $venta->vendedor->name ?? 'Sistema',
        ];
    }
}