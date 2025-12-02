<div class="main-header">
    <div class="main-header-logo">
    <div class="logo-header" data-background-color="white">
        
        <a href="{{ route('dashboard') }}" class="logo">
            
            <img
                src="{{ asset('img/logo.png') }}" {{-- Asegúrate de subir tu imagen aquí --}}
                alt="Logo Edesip"
                class="navbar-brand"
                height="20" {{-- Ajusta la altura según necesites (ej. 30 o 40) --}}
            />
            
            {{-- OPCIONAL: Si quieres texto al lado del logo --}}
            <span class="text-white fw-bold ms-2">EDESIP</span>
        
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

    <nav
        class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom"
    >
        <div class="container-fluid">
        
        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
            
            <li class="nav-item topbar-icon dropdown hidden-caret">
            <a
                class="nav-link dropdown-toggle"
                href="#"
                id="notifDropdown"
                role="button"
                data-bs-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
            >
                <i class="fa fa-bell"></i>
                </a>
            <ul
                class="dropdown-menu notif-box animated fadeIn"
                aria-labelledby="notifDropdown"
            >
                <li>
                <div class="dropdown-title">No tienes notificaciones nuevas</div>
                </li>
                <li>
                <div class="notif-scroll scrollbar-outer">
                    <div class="notif-center">
                    <p class="text-center p-3 text-muted">No hay nada que mostrar.</p>
                    </div>
                </div>
                </li>
            </ul>
            </li>

            <li class="nav-item topbar-user dropdown hidden-caret">
            <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                <div class="avatar-sm">
                    {{-- Lógica para mostrar la foto real o la default --}}
                    @if(Auth::user()->profile_photo_path)
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="..." class="avatar-img rounded-circle" />
                    @else
                        <img src="{{ asset('img/edesip.jpg') }}" alt="..." class="avatar-img rounded-circle" />
                    @endif
                </div>
                <span class="profile-username">
                <span class="op-7">Hola,</span>
                <span class_name="fw-bold">{{ Auth::user()->name ?? 'Usuario' }}</span>
                {{-- Añadido el ROL como solicitaste --}}
                <span class="op-7 d-block" style="font-size: 11px; margin-top: -3px;">
                    {{-- (Obtiene el primer rol del usuario, si no, muestra 'Rol') --}}
                    {{ Auth::user()->roles->pluck('name')->first() ?? 'Rol' }}
                </span>
                </span>
            </a>

            <ul class="dropdown-menu dropdown-user animated fadeIn">
                <div class="dropdown-user-scroll scrollbar-outer">
                    <li>
                    <div class="dropdown-divider"></div>
                    
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">Mi Perfil</a>
                    
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">Configuración</a>
                    
                    <div class="dropdown-divider"></div>
                    
                    <a class="dropdown-item" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Salir
                    </a>
                    </li>
                </div>
            </ul>
            </li>
        </ul>
        </div>
    </nav>
</div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>