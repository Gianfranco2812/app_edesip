<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cliente;
use App\Models\Venta;

class PortalController extends Controller
{
    /**
     * Muestra el Dashboard del Alumno.
     */
    public function index()
    {
        $user = Auth::user();

        // Buscamos el registro de Cliente asociado a este Usuario
        $cliente = Cliente::where('user_id', $user->id)->first();

        if (!$cliente) {
            // Caso raro: Es un usuario con rol Cliente pero sin ficha de cliente enlazada
            return view('portal.sin_datos');
        }

        // Traemos sus matrículas (Ventas) activas o finalizadas
        $misMatriculas = Venta::with(['grupo.programa', 'cuotas', 'contrato'])
                                ->where('cliente_id', $cliente->id)
                                ->where('estado', '!=', 'Anulada')
                                ->latest()
                                ->get();

        return view('portal.index', compact('cliente', 'misMatriculas'));
    }
}
