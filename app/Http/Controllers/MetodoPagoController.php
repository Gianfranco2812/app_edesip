<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MetodoPago;


class MetodoPagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $metodos = MetodoPago::orderBy('estado', 'asc')->paginate(10);
        return view('metodos_pago.index', compact('metodos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('metodos_pago.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_banco'  => 'required|string',
            'tipo'          => 'required|in:Cuenta Bancaria,Billetera Digital',
            'numero_cuenta' => 'required|string',
            'titular'       => 'required|string',
            'qr_imagen'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validación de imagen
            'estado'        => 'required'
        ]);

        // Lógica de Subida de Imagen
        if ($request->hasFile('qr_imagen')) {
            // Guardamos en storage/app/public/qrs
            $ruta = $request->file('qr_imagen')->store('qrs', 'public');
            $data['qr_imagen'] = $ruta;
        }

        MetodoPago::create($data);

        return redirect()->route('metodos_pago.index')->with('success', 'Método de pago agregado.');
    }

    // Método para cambiar estado rápido (Activar/Desactivar)
    public function toggleEstado($id)
    {
        $metodo = MetodoPago::findOrFail($id);
        $metodo->estado = ($metodo->estado == 'Activo') ? 'Inactivo' : 'Activo';
        $metodo->save();
        
        return back()->with('success', 'Estado actualizado correctamente.');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
