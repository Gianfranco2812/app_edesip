<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
public function run(): void
    {
        // 1. Resetea la caché de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Definir Permisos
        // Permisos del Dashboard
        Permission::create(['name' => 'ver-dashboard-global']);
        Permission::create(['name' => 'ver-dashboard-personal']);

        // Permisos de Administración (Sistema)
        Permission::create(['name' => 'gestionar-roles']); // CRUD de Roles
        Permission::create(['name' => 'gestionar-usuarios']); // CRUD de Usuarios
        Permission::create(['name' => 'gestionar-cursos']); // CRUD de Cursos

        // Permisos de Clientes
        Permission::create(['name' => 'ver-clientes']);
        Permission::create(['name' => 'crear-clientes']);
        Permission::create(['name' => 'editar-clientes']);
        Permission::create(['name' => 'eliminar-clientes']);
        Permission::create(['name' => 'ver-historial-cliente']);
        Permission::create(['name' => 'gestionar-documentos-cliente']);

        // Permisos de Ventas
        Permission::create(['name' => 'ver-ventas']);
        Permission::create(['name' => 'crear-ventas']);
        Permission::create(['name' => 'editar-ventas']);

        // Permisos de Cobranza
        Permission::create(['name' => 'ver-cobranzas-globales']); // Ver todas
        Permission::create(['name' => 'ver-cobranzas-personales']); // Ver solo las suyas

        // Permisos de Reportes
        Permission::create(['name' => 'ver-reportes-globales']);
        Permission::create(['name' => 'ver-reportes-personales']);
        
        // Permisos del Portal de Cliente
        Permission::create(['name' => 'ver-portal-pagos']);
        Permission::create(['name' => 'realizar-pagos']);
        Permission::create(['name' => 'ver-estado-cuenta']);


        // 3. Crear Roles
        $roleAdmin = Role::create(['name' => 'Admin']);
        $roleAsesor = Role::create(['name' => 'Asesor']);
        $roleCliente = Role::create(['name' => 'Cliente']);

        // 4. Asignar Permisos a Roles (¡AQUÍ LA MAGIA!)

        // El 'Admin' no necesita permisos gracias al Gate::before(),
        // pero es buena práctica dárselos por si acaso
        // $roleAdmin->givePermissionTo(Permission::all());
        
        // Asignamos permisos al 'Asesor'
        $roleAsesor->givePermissionTo([
            'ver-dashboard-personal',
            'ver-clientes',
            'crear-clientes',
            'editar-clientes',
            'ver-historial-cliente',
            'gestionar-documentos-cliente',
            'ver-ventas',
            'crear-ventas',
            'editar-ventas',
            'ver-cobranzas-personales',
            'ver-reportes-personales',
        ]);

        // Asignamos permisos al 'Cliente'
        $roleCliente->givePermissionTo([
            'ver-portal-pagos',
            'realizar-pagos',
            'ver-estado-cuenta',
        ]);
    }
}
