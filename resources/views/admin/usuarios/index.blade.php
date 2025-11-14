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
            <div class="d-flex align-items-center">
                <h4 class="card-title">Listado de Usuarios</h4>
                <a href="{{ route('usuarios.create') }}" class="btn btn-primary btn-round ms-auto">
                <i class="fa fa->plus"></i>
                Crear Nuevo Usuario
                </a>
            </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="display table table-striped table-hover">
                <thead>
                    <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th style="width: 10%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        {{-- Muestra el primer rol que tenga el usuario --}}
                        @forelse($user->getRoleNames() as $role)
                        <span class="badge bg-primary">{{ $role }}</span>
                        @empty
                        <span class="badge bg-secondary">Sin Rol</span>
                        @endforelse
                    </td>
                    <td>
                        <div class="form-button-action">
                        <a href="{{ route('usuarios.edit', $user->id) }}" data-bs-toggle="tooltip" title="Editar" class="btn btn-link btn-primary btn-lg">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" data-bs-toggle="tooltip" title="Eliminar" class="btn btn-link btn-danger" onclick="return confirm('¿Estás seguro?');">
                            <i class="fa fa-times"></i>
                            </button>
                        </form>
                        </div>
                    </td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection