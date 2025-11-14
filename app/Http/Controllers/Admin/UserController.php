<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;



class UserController extends Controller
{

    public function index()
    {
        $users = User::with('roles')->get();
        return view('admin.usuarios.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all(); 
        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', 
            'role' => 'required|string', 
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
        ]);

        $user->assignRole($request->role); 

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }


    public function edit(User $usuario) 
    {
        $roles = Role::all();
        $userRole = $usuario->roles->pluck('name')->first(); 

        return view('admin.usuarios.create', [
            'user' => $usuario, 
            'roles' => $roles,
            'userRole' => $userRole
        ]);
    }


    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id, 
            'password' => 'nullable|string|min:8|confirmed', 
            'role' => 'required|string',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        
        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data); 

        $usuario->syncRoles($request->role); 

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }


    public function destroy(User $usuario)
    {
        if ($usuario->id == 1 || $usuario->id == auth()->id()) {
            return back()->with('error', 'No puedes eliminar a este usuario.');
        }

        $usuario->delete();
        return back()->with('success', 'Usuario eliminado exitosamente.');
    }
}
