<?php

namespace App\Exports;

use App\Models\Cuota;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Auth;

class MorosidadExport implements FromView, ShouldAutoSize
{
    public function view(): View
    {
        $query = Cuota::with(['venta.cliente', 'venta.grupo.programa', 'venta.vendedor'])
                        ->where('estado_cuota', '!=', 'Pagada') 
                        ->whereDate('fecha_vencimiento', '<', now()); 

        if (!Auth::user()->hasRole('Admin')) {
            $query->whereHas('venta', function($q) {
                $q->where('vendedor_id', Auth::id());
            });
        }

        $cuotas = $query->orderBy('fecha_vencimiento', 'asc')->get();

        return view('exports.morosidad', [
            'cuotas' => $cuotas
        ]);
    }
}