<?php

namespace App\Exports;

use App\Models\Cuota;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Auth;

class MorosidadExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query()
    {
        $query = Cuota::with(['venta.cliente', 'venta.grupo', 'venta.vendedor'])
                        ->where('estado_cuota', '!=', 'Pagada')
                        ->whereDate('fecha_vencimiento', '<', now());

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
            'Fecha Vencimiento',
            'Días de Retraso',
            'Cliente',
            'Teléfono',
            'Programa',
            'Concepto Deuda',
            'Monto Deuda',
            'Vendedor Responsable',
        ];
    }

    public function map($cuota): array
    {
        return [
            $cuota->fecha_vencimiento->format('d/m/Y'),
            now()->diffInDays($cuota->fecha_vencimiento) . ' días',
            $cuota->venta->cliente->nombre_completo,
            $cuota->venta->cliente->telefono,
            $cuota->venta->grupo->programa->nombre,
            $cuota->descripcion,
            $cuota->monto_cuota,
            $cuota->venta->vendedor->name ?? 'N/A',
        ];
    }
}