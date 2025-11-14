@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        @if(isset($tipo))
        <h3 class="fw-bold mb-3">Editar Tipo de Programa</h3>
        @else
        <h3 class="fw-bold mb-3">Crear Nuevo Tipo de Programa</h3>
        @endif
    </div>
    <div class="row">
        <div class="col-md-6"> {{-- Lo hacemos más pequeño (col-md-6) --}}
        <div class="card">
            <div class="card-body">
            
            <form action="{{ isset($tipo) ? route('tipos-programa.update', $tipo->id) : route('tipos-programa.store') }}" method="POST">
                @csrf
                @if(isset($tipo))
                @method('PUT')
                @endif

                <div class="mb-3">
                <label for="name" class="form-label">Nombre del Tipo</label>
                <input type="text" 
                        class="form-control @error('nombre') is-invalid @enderror" 
                        id="name" 
                        name="nombre" 
                        value="{{ old('nombre', $tipo->nombre ?? '') }}"
                        placeholder="Ej: Diplomado"
                        required>
                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                </div>
                
                <div class="text-end mt-3">
                <a href="{{ route('tipos-programa.index') }}" class="btn btn-danger">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($tipo) ? 'Actualizar' : 'Guardar' }}
                </button>
                </div>
            </form>

            </div>
        </div>
        </div>
    </div>
</div>
@endsection