<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    // Muestra la lista de clientes
    public function index()
    {
        // El Admin ve TODOS los clientes
        if (Auth::user()->hasRole('Admin')) {
            $clientes = Cliente::with('vendedor')->get();
        } else {
            // El Asesor ve SOLO los clientes que él creó
            $clientes = Cliente::where('creado_por_vendedor_id', Auth::id())
                                ->with('vendedor')
                                ->get();
        }
        
        return view('clientes.index', compact('clientes'));
    }

    // Muestra el formulario de creación
    public function create()
    {
        // Esta es la vista para "Registrar Contacto"
        return view('clientes.create');
    }

    // Almacena el nuevo cliente
    public function store(Request $request)
    {
            $data = $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:clientes',
                'telefono' => 'required|string|max:20',
                'tipo_documento' => 'nullable|string',
                'numero_documento' => 'nullable|string|unique:clientes,numero_documento',
                'direccion' => 'nullable|string',
                'fecha_nacimiento' => 'nullable|date',
                'estado' => 'required|string', 
            ]);

            $data['creado_por_vendedor_id'] = Auth::id();

            // Creamos el cliente
            $cliente = Cliente::create($data);

            // --- ¡AQUÍ LA LÓGICA DEL BOTÓN! ---
            
            // Si el botón fue "Matricular Ahora"
            if ($request->input('action') == 'matriculate') {
                // Redirigimos al formulario de 'ventas.create'
                // y le pasamos el ID del cliente que acabamos de crear.
                return redirect()->route('ventas.create', ['cliente_id' => $cliente->id])
                        ->with('success', 'Cliente registrado. Ahora, selecciona el grupo para la matrícula.');
            }

            // Si no (fue "Guardar Prospecto"), solo volvemos al listado.
            return redirect()->route('clientes.index')->with('success', 'Prospecto registrado exitosamente.');
    }

    /**
     * Display the specified resource.
     * (Aún no lo usamos, pero lo tendremos listo)
     */
    public function show(Cliente $cliente)
    {
        // (Aquí iría la vista de detalle del cliente: historial, documentos, etc.)
        // return view('clientes.show', compact('cliente'));
    }

    // Muestra el formulario de edición
    public function edit(Cliente $cliente)
    {
        // Verificación de seguridad: El Asesor solo puede editar sus propios clientes
        if (Auth::user()->hasRole('Asesor') && $cliente->creado_por_vendedor_id != Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }

        return view('clientes.create', compact('cliente'));
    }

    // Actualiza el cliente
    public function update(Request $request, Cliente $cliente)
    {
        // Verificación de seguridad
        if (Auth::user()->hasRole('Asesor') && $cliente->creado_por_vendedor_id != Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clientes,email,' . $cliente->id,
            'telefono' => 'required|string|max:20',
            
            'tipo_documento' => 'nullable|string',
            'numero_documento' => 'nullable|string|unique:clientes,numero_documento,' . $cliente->id,
            'direccion' => 'nullable|string',
            'fecha_nacimiento' => 'nullable|date',
            'estado' => 'required|string',
        ]);

        $cliente->update($data);

        if ($request->input('next_step') == 'matricula') {
        
        // Verificamos de nuevo si ya llenó los datos (Doble check)
        if (!empty($cliente->numero_documento) && !empty($cliente->direccion)) {
            return redirect()
                ->route('ventas.create', ['cliente_id' => $cliente->id])
                ->with('success', 'Datos actualizados. Ahora sí, proceda con la matrícula.');
        } else {
            // Si guardó pero SIGUE sin llenar los datos, lo devolvemos
            return back()
                ->withInput() // Mantiene lo que escribió
                ->with('error', 'Aún falta el Documento o la Dirección. Son obligatorios para el contrato.');
        }
    }
    // -----------------------------------

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado exitosamente.');
    }

    // Elimina el cliente
    public function destroy(Cliente $cliente)
    {
        // Verificación de seguridad
        if (Auth::user()->hasRole('Asesor') && $cliente->creado_por_vendedor_id != Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }

        // (Validación: No borrar clientes con ventas)
        // if ($cliente->ventas()->count() > 0) {
        //     return back()->with('error', 'No se puede eliminar. El cliente tiene ventas asociadas.');
        // }
        
        $cliente->delete();
        return back()->with('success', 'Cliente eliminado exitosamente.');
    }
}
