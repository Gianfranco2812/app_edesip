<div class="sidebar" data-background-color="dark">
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
        
        {{-- (Aquí suele ir el logo y el perfil de usuario) --}}
        
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

            {{-- Menú desplegable para Administración --}}
            <li class="nav-item {{ (request()->is('admin/*')) ? 'active submenu' : '' }}">
            <a data-bs-toggle="collapse" href="#adminMenu">
                <i class="fas fa-cogs"></i>
                <p>Sistema</p>
                <span class="caret"></span>
            </a>
            <div class="collapse {{ (request()->is('admin/*')) ? 'show' : '' }}" id="adminMenu">
                <ul class="nav nav-collapse">
                <li>
                    <a href="{{ route('roles.index') }}">
                    <span class="sub-item">Roles y Permisos</span>
                    </a>
                </li>
                
                </ul>
            </div>
            </li>
            @endrole
        </ul>
        </div>
    </div>
</div>