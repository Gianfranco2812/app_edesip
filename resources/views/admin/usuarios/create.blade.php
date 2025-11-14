@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        @if(isset($user))
        <h3 class="fw-bold mb-3">Editar Usuario: {{ $user->name }}</h3>
        @else
        <h3 class="fw-bold mb-3">Crear Nuevo Usuario</h3>
        @endif
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-body">
            
            <form action="{{ isset($user) ? route('usuarios.update', $user->id) : route('usuarios.store') }}" method="POST">
                @csrf
                @if(isset($user))
                @method('PUT')
                @endif

                <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                    <label for="name" class="form-label">Nombre Completo</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                </div>

                <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" {{ isset($user) ? '' : 'required' }}>
                    @if(isset($user))
                        <small class="form-text text-muted">Dejar en blanco para no cambiar la contraseña.</small>
                    @endif
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" {{ isset($user) ? '' : 'required' }}>
                    </div>
                </div>
                </div>

                <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                    <label for="role" class="form-label">Rol</label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="" disabled {{ !isset($userRole) ? 'selected' : '' }}>Selecciona un rol</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" 
                            {{ (isset($userRole) && $userRole == $role->name) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                </div>
                
                <div class="text-end mt-4">
                <a href="{{ route('usuarios.index') }}" class="btn btn-danger">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($user) ? 'Actualizar Usuario' : 'Guardar Usuario' }}
                </button>
                </div>
            </form>

            </div>
        </div>
        </div>
    </div>
</div>
@endsection