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

    public function create()
    {
        return view('admin.tipos_programa.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:255|unique:tipos_programa']);

        TipoPrograma::create($request->all());

        return redirect()->route('tipos-programa.index')->with('success', 'Tipo de programa creado exitosamente.');
    }

    public function edit(TipoPrograma $tipos_programa) 
    {
        return view('admin.tipos_programa.create', ['tipo' => $tipos_programa]);
    }


    public function update(Request $request, TipoPrograma $tipos_programa)
    {
        $request->validate(['nombre' => 'required|string|max:255|unique:tipos_programa,nombre,' . $tipos_programa->id]);

        $tipos_programa->update($request->all());

        return redirect()->route('tipos-programa.index')->with('success', 'Tipo de programa actualizado exitosamente.');
    }


    public function destroy(TipoPrograma $tipos_programa)
    {
        try {
            $tipos_programa->delete();
            return back()->with('success', 'Tipo de programa eliminado exitosamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'No se puede eliminar. Este tipo está siendo usado por un programa.');
        }
    }
}
