<?php

namespace App\Exports;

use App\Models\Cuota;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Auth;

class IngresosExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function query()
    {
        $query = Cuota::with(['venta.cliente', 'venta.grupo'])
                      ->where('estado_cuota', 'Pagada')
                      ->whereDate('fecha_pago', '>=', $this->fechaInicio)
                      ->whereDate('fecha_pago', '<=', $this->fechaFin);

        if (!Auth::user()->hasRole('Admin')) {
            $query->whereHas('venta', function($q) {
                $q->where('vendedor_id', Auth::id());
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Fecha Pago',
            'Cliente',
            'Concepto',
            'Programa',
            'Monto Pagado',
            'Método Pago',
            'N° Operación',
        ];
    }

    public function map($cuota): array
    {
        return [
            $cuota->fecha_pago ? $cuota->fecha_pago->format('d/m/Y H:i') : 'N/A',
            $cuota->venta->cliente->nombre_completo,
            $cuota->descripcion,
            $cuota->venta->grupo->programa->nombre,
            $cuota->monto_cuota,
            $cuota->metodo_pago,
            $cuota->transaccion_id,
        ];
    }
}