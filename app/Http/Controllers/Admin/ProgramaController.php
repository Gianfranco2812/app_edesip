<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Programa;
use App\Models\TipoPrograma;
use App\Models\PlantillaContrato;
use Illuminate\Support\Facades\Storage;

class ProgramaController extends Controller
{
    public function index()
    {
        $programas = Programa::with(['tipoPrograma', 'plantillaContrato'])->get();
        return view('admin.programas.index', compact('programas'));
    }

    public function create()
    {
        $tipos = TipoPrograma::all();
        $plantillas = PlantillaContrato::all();
        return view('admin.programas.create', compact('tipos', 'plantillas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:programas',
            'tipo_programa_id' => 'required|exists:tipos_programa,id',
            'plantilla_contrato_id' => 'required|exists:plantillas_contrato,id',
            'descripcion_detallada' => 'nullable|string',
            'horas_totales' => 'nullable|integer',
            'estado' => 'required|string',
            'imagen_promocional' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', 
            'brochure_pdf' => 'nullable|file|mimes:pdf|max:5120', 
        ]);

        if ($request->hasFile('imagen_promocional')) {
            $path = $request->file('imagen_promocional')->store('programas/imagenes', 'public');
            $data['imagen_promocional_url'] = $path;
        }

        if ($request->hasFile('brochure_pdf')) {
            $path = $request->file('brochure_pdf')->store('programas/brochures', 'public');
            $data['brochure_pdf_url'] = $path;
        }

        Programa::create($data);

        return redirect()->route('programas.index')->with('success', 'Programa creado exitosamente.');
    }

    public function edit(Programa $programa)
    {
        $tipos = TipoPrograma::all();
        $plantillas = PlantillaContrato::all();
        return view('admin.programas.create', compact('programa', 'tipos', 'plantillas'));
    }

    public function update(Request $request, Programa $programa)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:programas,nombre,' . $programa->id,
            'tipo_programa_id' => 'required|exists:tipos_programa,id',
            'plantilla_contrato_id' => 'required|exists:plantillas_contrato,id',
            'descripcion_detallada' => 'nullable|string',
            'horas_totales' => 'nullable|integer',
            'estado' => 'required|string',
            'imagen_promocional' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'brochure_pdf' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        if ($request->hasFile('imagen_promocional')) {
            if ($programa->imagen_promocional_url) {
                Storage::disk('public')->delete($programa->imagen_promocional_url);
            }
            $path = $request->file('imagen_promocional')->store('programas/imagenes', 'public');
            $data['imagen_promocional_url'] = $path;
        }

        if ($request->hasFile('brochure_pdf')) {
            if ($programa->brochure_pdf_url) {
                Storage::disk('public')->delete($programa->brochure_pdf_url);
            }
            $path = $request->file('brochure_pdf')->store('programas/brochures', 'public');
            $data['brochure_pdf_url'] = $path;
        }

        $programa->update($data);

        return redirect()->route('programas.index')->with('success', 'Programa actualizado exitosamente.');
    }


    public function destroy(Programa $programa)
    {
        if ($programa->grupos()->count() > 0) {
            return back()->with('error', 'No se puede eliminar. Este programa tiene grupos asociados.');
        }

        if ($programa->imagen_promocional_url) {
            Storage::disk('public')->delete($programa->imagen_promocional_url);
        }
        if ($programa->brochure_pdf_url) {
            Storage::disk('public')->delete($programa->brochure_pdf_url);
        }

        $programa->delete();
        return back()->with('success', 'Programa eliminado exitosamente.');
    }
}
