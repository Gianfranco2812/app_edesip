<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{

    public function index(Request $request)
    {

        $query = Cliente::with('vendedor');


        if (!Auth::user()->hasRole('Admin')) {
            $query->where('creado_por_vendedor_id', Auth::id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%$search%")
                    ->orWhere('apellido', 'like', "%$search%")
                    ->orWhere('numero_documento', 'like', "%$search%")
                    ->orWhere('telefono', 'like', "%$search%");
            });
        }

        if (Auth::user()->hasRole('Admin') && $request->filled('vendedor_id')) {
            $query->where('creado_por_vendedor_id', $request->vendedor_id);
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $clientes = $query->latest()->paginate(20);


        $vendedores = [];
        if (Auth::user()->hasRole('Admin')) {
            $vendedores = User::role('Asesor')->get(); 
        }

        return view('clientes.index', compact('clientes', 'vendedores'));
    }

    public function create()
    {
        return view('clientes.create');
    }

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

            $cliente = Cliente::create($data);

            
            if ($request->input('action') == 'matriculate') {
                return redirect()->route('ventas.create', ['cliente_id' => $cliente->id])
                        ->with('success', 'Cliente registrado. Ahora, selecciona el grupo para la matrícula.');
            }

            return redirect()->route('clientes.index')->with('success', 'Prospecto registrado exitosamente.');
    }

    /**
     * Display the specified resource.
     * (Aún no lo usamos, pero lo tendremos listo)
     */
    public function show(Cliente $cliente)
    {

    }


    public function edit(Cliente $cliente)
    {
        if (Auth::user()->hasRole('Asesor') && $cliente->creado_por_vendedor_id != Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }

        return view('clientes.create', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
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
        
        if (!empty($cliente->numero_documento) && !empty($cliente->direccion)) {
            return redirect()
                ->route('ventas.create', ['cliente_id' => $cliente->id])
                ->with('success', 'Datos actualizados. Ahora sí, proceda con la matrícula.');
        } else {
            return back()
                ->withInput() 
                ->with('error', 'Aún falta el Documento o la Dirección. Son obligatorios para el contrato.');
        }
    }
    // -----------------------------------

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(Cliente $cliente)
    {
        if (Auth::user()->hasRole('Asesor') && $cliente->creado_por_vendedor_id != Auth::id()) {
            abort(403, 'Acción no autorizada.');
        }


        
        $cliente->delete();
        return back()->with('success', 'Cliente eliminado exitosamente.');
    }
}
