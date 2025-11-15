<div class="sidebar" data-background-color="dark">
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
        
        <ul class="nav nav-secondary">

            <li class="nav-item {{ (request()->is('dashboard')) ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="nav-link">
                <i class="fas fa-home"></i>
                <p>Home (Dashboard)</p>
            </a>
            </li>

            @role('Admin') {{-- El usuario DEBE tener el rol 'Admin' --}}
            
            <li class="nav-section">
            <span class="sidebar-mini-icon">
                <i class="fa fa-ellipsis-h"></i>
            </span>
            <h4 class="text-section">ADMINISTRACIÓN</h4>
            </li>

            <li class="nav-item {{ (request()->is('admin/roles*')) ? 'active' : '' }}">
            <a href="{{ route('roles.index') }}">
                <i class="fas fa-user-shield"></i>
                <p>Roles y Permisos</p>
            </a>
            </li>

            <li class="nav-item {{ (request()->is('admin/usuarios*')) ? 'active' : '' }}">
            <a href="{{ route('usuarios.index') }}">
                <i class="fas fa-users-cog"></i>
                <p>Usuarios</p>
            </a>
            </li>

            <li class="nav-item {{ (request()->is('admin/programas*') || request()->is('admin/grupos*') || request()->is('admin/tipos-programa*') || request()->is('admin/plantillas-contrato*')) ? 'active submenu' : '' }}">
            <a data-bs-toggle="collapse" href="#programasMenu">
                <i class="fas fa-graduation-cap"></i>
                <p>Gestión de Programas</p>
                <span class="caret"></span>
            </a>
            <div class="collapse {{ (request()->is('admin/programas*') || request()->is('admin/grupos*') || request()->is('admin/tipos-programa*') || request()->is('admin/plantillas-contrato*')) ? 'show' : '' }}" id="programasMenu">
                <ul class="nav nav-collapse">
                <li class="{{ (request()->is('admin/programas*')) ? 'active' : '' }}">
                    <a href="{{ route('programas.index') }}">
                    <span class="sub-item">Programas</span>
                    </a>
                </li>
                <li class="{{ (request()->is('admin/grupos*')) ? 'active' : '' }}">
                    <a href="{{ route('grupos.index') }}">
                    <span class="sub-item">Grupos</span>
                    </a>
                </li>
                <li class="{{ (request()->is('admin/tipos-programa*')) ? 'active' : '' }}">
                    <a href="{{ route('tipos-programa.index') }}">
                    <span class="sub-item">Tipos de Programa</span>
                    </a>
                </li>
                <li class="{{ (request()->is('admin/plantillas-contrato*')) ? 'active' : '' }}">
                    <a href="{{ route('plantillas-contrato.index') }}">
                    <span class="sub-item">Plantillas de Contrato</span>
                    </a>
                </li>
                </ul>
            </div>
            </li>
            @endrole


            @canany(['ver-clientes', 'ver-ventas', 'ver-cobranzas-personales'])
                @role('Admin|Asesor') 
                <li class="nav-section">
                <span class="sidebar-mini-icon">
                    <i class="fa fa-ellipsis-h"></i>
                </span>
                <h4 class="text-section">OPERACIONES</h4>
                </li>

                <li class="nav-item {{ (request()->is('clientes*')) ? 'active' : '' }}">
                <a href="{{ route('clientes.index') }}">
                    <i class="fas fa-users"></i>
                    <p>Clientes</p>
                </a>
                </li>
                <li class="nav-item {{ (request()->is('ventas*')) ? 'active' : '' }}">
                <a href="{{ route('ventas.index') }}">
                    <i class="fas fa-chart-line"></i>
                    <p>Ventas</p>
                </a>
                </li>

                @endrole
            @endcanany

            </ul>
        </div>
    </div>
</div>