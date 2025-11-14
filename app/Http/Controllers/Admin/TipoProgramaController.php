<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TipoPrograma;

class TipoProgramaController extends Controller
{
    
    public function index()
    {
        $tipos = TipoPrograma::all();
        return view('admin.tipos_programa.index', compact('tipos'));
    }

    // Muestra el formulario de creación
    public function create()
    {
        return view('admin.tipos_programa.create');
    }

    // Almacena en la BD
    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:255|unique:tipos_programa']);

        TipoPrograma::create($request->all());

        return redirect()->route('tipos-programa.index')->with('success', 'Tipo de programa creado exitosamente.');
    }

    // Muestra el formulario de edición
    public function edit(TipoPrograma $tipos_programa) // Laravel inyecta el modelo
    {
        return view('admin.tipos_programa.create', ['tipo' => $tipos_programa]);
    }

    // Actualiza en la BD
    public function update(Request $request, TipoPrograma $tipos_programa)
    {
        $request->validate(['nombre' => 'required|string|max:255|unique:tipos_programa,nombre,' . $tipos_programa->id]);

        $tipos_programa->update($request->all());

        return redirect()->route('tipos-programa.index')->with('success', 'Tipo de programa actualizado exitosamente.');
    }

    // Elimina de la BD
    public function destroy(TipoPrograma $tipos_programa)
    {
        try {
            $tipos_programa->delete();
            return back()->with('success', 'Tipo de programa eliminado exitosamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Captura el error si está siendo usado por un 'programa'
            return back()->with('error', 'No se puede eliminar. Este tipo está siendo usado por un programa.');
        }
    }
}
