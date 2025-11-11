<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;  
use Spatie\Permission\Models\Permission;
use IlluminateD\B;

class RoleController extends Controller
{
    // Muestra la lista de roles
    public function index()
    {
        $roles = Role::all();
        // Carga la vista y pasa los roles
        return view('admin.roles.index', compact('roles'));
    }

    // Muestra el formulario para crear un nuevo rol
    public function create()
    {
        $permissions = Permission::all(); // Obtiene todos los permisos
        return view('admin.roles.create', compact('permissions'));
    }

    // Almacena el nuevo rol en la BD
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array', // Asegura que al menos un permiso sea enviado
        ]);

        // Crea el rol
        $role = Role::create(['name' => $request->name]);

        // Asigna los permisos seleccionados (los que vinieron del checkbox)
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('success', 'Rol creado exitosamente.');
    }

    // Muestra el formulario para editar un rol (¡Este es el más importante!)
    public function edit(Role $role)
    {
        $permissions = Permission::all(); // Todos los permisos para el checklist
        $rolePermissions = $role->permissions->pluck('name')->toArray(); // Permisos que este rol YA tiene

        return view('admin.roles.create', compact('role', 'permissions', 'rolePermissions'));
    }

    // Actualiza el rol en la BD
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'required|array',
        ]);

        // Actualiza el nombre del rol
        $role->update(['name' => $request->name]);

        // Sincroniza los permisos (quita los viejos y pone los nuevos)
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('success', 'Rol actualizado exitosamente.');
    }

    // Elimina un rol
    public function destroy(Role $role)
    {
        // No dejamos que borren el rol de Admin
        if ($role->name == 'Admin') {
            return back()->with('error', 'No se puede eliminar el rol de Administrador.');
        }

        $role->delete();
        return back()->with('success', 'Rol eliminado exitosamente.');
    }
}