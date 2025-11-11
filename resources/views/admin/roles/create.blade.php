@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        {{-- Determina si estamos creando o editando --}}
        @if(isset($role))
        <h3 class="fw-bold mb-3">Editar Rol: {{ $role->name }}</h3>
        @else
        <h3 class="fw-bold mb-3">Crear Nuevo Rol</h3>
        @endif
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-body">
            
            {{-- El formulario apunta a 'store' (crear) o 'update' (actualizar) --}}
            <form action="{{ isset($role) ? route('roles.update', $role->id) : route('roles.store') }}" method="POST">
                @csrf
                @if(isset($role))
                @method('PUT') {{-- Método para actualizar --}}
                @endif

                <div class="mb-3">
                <label for="name" class="form-label">Nombre del Rol</label>
                <input type="text" 
                        class="form-control @error('name') is-invalid @enderror" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $role->name ?? '') }}"
                        {{-- Si es el rol Admin, no dejamos editar el nombre --}}
                        {{ (isset($role) && $role->name == 'Admin') ? 'readonly' : '' }}>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                </div>

                <hr>

                <h4 class="fw-bold mb-3">Asignar Permisos</h4>
                
                @error('permissions') {{-- Error si no se marca ninguno --}}
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <div class="row">
                @foreach($permissions as $permission)
                    <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" 
                            type="checkbox" 
                            value="{{ $permission->name }}" 
                            id="perm_{{ $permission->id }}" 
                            name="permissions[]"
                            
                            {{-- Marca el checkbox si el rol YA tiene ese permiso --}}
                            {{ (isset($rolePermissions) && in_array($permission->name, $rolePermissions)) ? 'checked' : '' }}
                            
                            {{-- El rol Admin siempre tiene todo marcado y deshabilitado --}}
                            {{ (isset($role) && $role->name == 'Admin') ? 'checked disabled' : '' }}>
                            
                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                        {{ $permission->name }}
                        </label>
                    </div>
                    </div>
                @endforeach
                </div>
                
                <div class="text-end mt-4">
                <a href="{{ route('roles.index') }}" class="btn btn-danger">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($role) ? 'Actualizar Rol' : 'Guardar Rol' }}
                </button>
                </div>
            </form>

            </div>
        </div>
        </div>
    </div>
    </div>
@endsection