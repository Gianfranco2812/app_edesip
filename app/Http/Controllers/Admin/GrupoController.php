<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Grupo;
use App\Models\Programa;


class GrupoController extends Controller
{

    public function index()
    {
        $grupos = Grupo::with('programa')->get();
        return view('admin.grupos.index', compact('grupos'));
    }

    public function create()
    {
        $programas = Programa::where('estado', 'Activo')->get();
        return view('admin.grupos.create', compact('programas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'programa_id' => 'required|exists:programas,id',
            'codigo_grupo' => 'required|string|max:255|unique:grupos',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'required|date|after_or_equal:fecha_inicio',
            'modalidad' => 'required|string',
            'horario_texto' => 'required|string|max:255',
            'estado' => 'required|string',
            'costo_total' => 'required|numeric|min:0',
            'costo_matricula' => 'required|numeric|min:0|lte:costo_total', 
            'numero_cuotas' => 'required|integer|min:1',
            'texto_promocional' => 'nullable|string',
        ]);

        Grupo::create($data);

        return redirect()->route('grupos.index')->with('success', 'Grupo creado exitosamente.');
    }

    public function edit(Grupo $grupo)
    {
        $programas = Programa::all();
        return view('admin.grupos.create', compact('grupo', 'programas'));
    }

    public function update(Request $request, Grupo $grupo)
    {
        $data = $request->validate([
            'programa_id' => 'required|exists:programas,id',
            'codigo_grupo' => 'required|string|max:255|unique:grupos,codigo_grupo,' . $grupo->id,
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'required|date|after_or_equal:fecha_inicio',
            'modalidad' => 'required|string',
            'horario_texto' => 'required|string|max:255',
            'estado' => 'required|string',
            'costo_total' => 'required|numeric|min:0',
            'costo_matricula' => 'required|numeric|min:0|lte:costo_total',
            'numero_cuotas' => 'required|integer|min:1',
            'texto_promocional' => 'nullable|string',
        ]);

        $grupo->update($data);

        return redirect()->route('grupos.index')->with('success', 'Grupo actualizado exitosamente.');
    }


    public function destroy(Grupo $grupo)
    {

        $grupo->delete();
        return back()->with('success', 'Grupo eliminado exitosamente.');
    }
}
