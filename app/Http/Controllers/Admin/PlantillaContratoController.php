<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PlantillaContrato;

class PlantillaContratoController extends Controller
{
    public function index()
    {
        $plantillas = PlantillaContrato::all();
        return view('admin.plantillas_contrato.index', compact('plantillas'));
    }

    // Muestra el formulario de creación
    public function create()
    {
        return view('admin.plantillas_contrato.create');
    }

    // Almacena en la BD
    public function store(Request $request)
    {
        $request->validate([
            'nombre_plantilla' => 'required|string|max:255|unique:plantillas_contrato',
            'contenido' => 'required|string',
        ]);

        PlantillaContrato::create($request->all());

        return redirect()->route('plantillas-contrato.index')->with('success', 'Plantilla creada exitosamente.');
    }

    // Muestra el formulario de edición
    public function edit(PlantillaContrato $plantillas_contrato)
    {
        return view('admin.plantillas_contrato.create', ['plantilla' => $plantillas_contrato]);
    }

    // Actualiza en la BD
    public function update(Request $request, PlantillaContrato $plantillas_contrato)
    {
        $request->validate([
            'nombre_plantilla' => 'required|string|max:255|unique:plantillas_contrato,nombre_plantilla,' . $plantillas_contrato->id,
            'contenido' => 'required|string',
        ]);

        $plantillas_contrato->update($request->all());

        return redirect()->route('plantillas-contrato.index')->with('success', 'Plantilla actualizada exitosamente.');
    }

    // Elimina de la BD
    public function destroy(PlantillaContrato $plantillas_contrato)
    {
        try {
            $plantillas_contrato->delete();
            return back()->with('success', 'Plantilla eliminada exitosamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Captura el error si está siendo usada por un 'programa'
            return back()->with('error', 'No se puede eliminar. Esta plantilla está siendo usada por un programa.');
        }
    }
}
