<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    // Muestra la lista de clientes
    public function index(Request $request)
    {
        // 1. Iniciamos la consulta base con la relación del vendedor
        $query = Cliente::with('vendedor');

        // --- FILTRO DE SEGURIDAD (Rol) ---
        // Si NO es Admin, forzamos a que solo vea sus propios registros
        if (!Auth::user()->hasRole('Admin')) {
            $query->where('creado_por_vendedor_id', Auth::id());
        }

        // --- 2. BUSCADOR DE TEXTO (Nombre, Apellido, DNI, Teléfono) ---
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%$search%")
                    ->orWhere('apellido', 'like', "%$search%")
                    ->orWhere('numero_documento', 'like', "%$search%")
                    ->orWhere('telefono', 'like', "%$search%");
            });
        }

        // --- 3. FILTRO POR ASESOR (Solo para Admin) ---
        // El Admin puede elegir ver los clientes de un vendedor específico
        if (Auth::user()->hasRole('Admin') && $request->filled('vendedor_id')) {
            $query->where('creado_por_vendedor_id', $request->vendedor_id);
        }

        // --- 4. FILTRO POR FECHAS (Registro) ---
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        // --- 5. FILTRO POR ESTADO (Opcional pero útil) ---
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Ejecutar consulta y paginar
        $clientes = $query->latest()->paginate(20);

        // --- DATOS PARA EL FORMULARIO DE FILTROS ---
        // Obtenemos la lista de usuarios que tienen rol 'Asesor' (para el dropdown del Admin)
        // (Asumiendo que usas Spatie, podemos filtrar por rol, o traer todos los users)
        $vendedores = [];
        if (Auth::user()->hasRole('Admin')) {
            $vendedores = User::role('Asesor')->get(); 
        }

        return view('clientes.index', compact('clientes', 'vendedores'));
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
            ]);
            $data['estado'] = 'Prospecto';
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
