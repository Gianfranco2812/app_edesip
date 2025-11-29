<div class="sidebar" data-background-color="white">
<div class="main-header-logo">
    <div class="logo-header" data-background-color="white">
        
        <a href="{{ route('dashboard') }}" class="logo">
            
            <img
                src="{{ asset('img/logo.png') }}" {{-- Asegúrate de subir tu imagen aquí --}}
                alt="Logo Edesip"
                class="navbar-brand"
                height="70" {{-- Ajusta la altura según necesites (ej. 30 o 40) --}}
            />
        
        </a>
        
        <div class="nav-toggle">
            <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
            </button>
            <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
            </button>
        </div>
        <button class="topbar-toggler more">
            <i class="gg-more-vertical-alt"></i>
        </button>
    </div>
    </div>    
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
                <li class="nav-item {{ (request()->is('cobranzas*')) ? 'active' : '' }}">
                    <a href="{{ route('cobranzas.index') }}">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <p>Cobranza</p>
                    </a>
                </li>

                @endrole
            @endcanany
            @role('Cliente')
                <li class="nav-section">
                <span class="sidebar-mini-icon">
                    <i class="fa fa-ellipsis-h"></i>
                </span>
                <h4 class="text-section">MI CUENTA</h4>
                </li>

                <li class="nav-item {{ (request()->is('mi-portal*')) ? 'active' : '' }}">
                <a href="{{ route('portal.index') }}">
                    <i class="fas fa-laptop-house"></i>
                    <p>Mi Portal</p>
                </a>
                </li>
                
                <li class="nav-item {{ (request()->is('perfil*')) ? 'active' : '' }}">
                <a href="{{ route('profile.edit') }}">
                    <i class="fas fa-user-cog"></i>
                    <p>Mis Datos</p>
                </a>
                </li>
                @endrole
            </ul>
        </div>
    </div>
</div>