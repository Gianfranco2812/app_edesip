@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Gestión de Usuarios</h3>
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            
            <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="card-title">Listado de Usuarios</h4>
                <a href="{{ route('usuarios.create') }}" class="btn btn-primary btn-round">
                <i class="fa fa-plus"></i> Crear Nuevo Usuario
                </a>
            </div>

            <div class="mt-3">
                <form action="{{ route('usuarios.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                                placeholder="Buscar por Nombre, DNI o Email..." 
                                value="{{ request('search') }}">
                        
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        
                        @if(request('search'))
                            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-danger" title="Limpiar búsqueda">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
            </div>

            <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                    <th>ID</th>
                    <th>Usuario</th> <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th style="width: 10%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr>
                    <td>{{ $user->id }}</td>
                    <td>
                        <span >{{ $user->username ?? 'Sin usuario' }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xs me-2">
                                <img src="{{ $user->profile_photo_path ? asset('storage/'.$user->profile_photo_path) : asset('assets/img/profile.jpg') }}" class="avatar-img rounded-circle">
                            </div>
                            {{ $user->name }}
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        {{-- Mostramos el primer rol con un badge --}}
                        @forelse($user->getRoleNames() as $role)
                            @php
                                $color = match($role) {
                                    'Admin' => 'danger',
                                    'Asesor' => 'success',
                                    'Cliente' => 'info',
                                    default => 'secondary'
                                };
                            @endphp
                        <span class="badge bg-{{ $color }}">{{ $role }}</span>
                        @empty
                        <span class="badge bg-secondary">Sin Rol</span>
                        @endforelse
                    </td>
                    <td>
                        <div class="form-button-action">
                        <a href="{{ route('usuarios.edit', $user->id) }}" class="btn btn-link btn-primary btn-lg" data-bs-toggle="tooltip" title="Editar">
                            <i class="fa fa-edit"></i>
                        </a>
                        
                        {{-- Evitar que te borres a ti mismo --}}
                        @if(Auth::id() != $user->id)
                            <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Eliminar" onclick="return confirm('¿Estás seguro?');">
                                <i class="fa fa-times"></i>
                                </button>
                            </form>
                        @endif
                        </div>
                    </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center p-4 text-muted">
                            No se encontraron usuarios que coincidan con "{{ request('search') }}".
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                </table>

                {{-- PAGINACIÓN --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $users->appends(request()->query())->links() }}
                </div>

            </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection