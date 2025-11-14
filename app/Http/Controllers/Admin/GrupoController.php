<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Grupo;
use App\Models\Programa;


class GrupoController extends Controller
{
// Muestra la lista de grupos
    public function index()
    {
        // Cargamos la relación 'programa' para mostrar el nombre
        $grupos = Grupo::with('programa')->get();
        return view('admin.grupos.index', compact('grupos'));
    }

    // Muestra el formulario de creación
    public function create()
    {
        // Solo mostramos programas 'Activos' para crear grupos nuevos
        $programas = Programa::where('estado', 'Activo')->get();
        return view('admin.grupos.create', compact('programas'));
    }

    // Almacena el nuevo grupo
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
            'costo_matricula' => 'required|numeric|min:0|lte:costo_total', // lte = menor o igual que costo_total
            'numero_cuotas' => 'required|integer|min:1',
            'texto_promocional' => 'nullable|string',
        ]);

        Grupo::create($data);

        return redirect()->route('grupos.index')->with('success', 'Grupo creado exitosamente.');
    }

    // Muestra el formulario de edición
    public function edit(Grupo $grupo)
    {
        // Obtenemos todos los programas (incluidos archivados) por si acaso
        $programas = Programa::all();
        return view('admin.grupos.create', compact('grupo', 'programas'));
    }

    // Actualiza el grupo
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

    // Elimina el grupo
    public function destroy(Grupo $grupo)
    {
        // (Aquí deberías añadir una validación para no borrar grupos con alumnos inscritos)
        // if ($grupo->ventas()->count() > 0) {
        //     return back()->with('error', 'No se puede eliminar. Este grupo tiene ventas/alumnos asociados.');
        // }

        $grupo->delete();
        return back()->with('success', 'Grupo eliminado exitosamente.');
    }
}
