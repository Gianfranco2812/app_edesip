@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        @if(isset($plantilla))
        <h3 class="fw-bold mb-3">Editar Plantilla de Contrato</h3>
        @else
        <h3 class="fw-bold mb-3">Crear Nueva Plantilla de Contrato</h3>
        @endif
    </div>
    <div classs="row">
        <div class="col-md-12"> {{-- Ancho completo para el editor --}}
        <div class="card">
            <div class="card-body">
            
            <form action="{{ isset($plantilla) ? route('plantillas-contrato.update', $plantilla->id) : route('plantillas-contrato.store') }}" method="POST">
                @csrf
                @if(isset($plantilla))
                @method('PUT')
                @endif

                <div class="mb-3">
                <label for="nombre_plantilla" class="form-label">Nombre de la Plantilla</label>
                <input type="text" 
                        class="form-control @error('nombre_plantilla') is-invalid @enderror" 
                        id="nombre_plantilla" 
                        name="nombre_plantilla" 
                        value="{{ old('nombre_plantilla', $plantilla->nombre_plantilla ?? '') }}"
                        placeholder="Ej: Contrato General de Servicios Educativos"
                        required>
                @error('nombre_plantilla')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                </div>

                <div class="mb-3">
                <label for="contenido" class="form-label">Contenido del Contrato</label>
                <small class="form-text text-muted d-block mb-2">
                    Usa placeholders como: [CLIENTE_NOMBRE], [CLIENTE_DNI], [PROGRAMA_NOMBRE], [GRUPO_COSTO_TOTAL], [GRUPO_FECHA_INICIO]
                </small>
                <textarea 
                    class="form-control @error('contenido') is-invalid @enderror" 
                    id="contenido" 
                    name="contenido" 
                    rows="20" 
                    required>{{ old('contenido', $plantilla->contenido ?? '') }}</textarea>
                @error('contenido')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                </div>
                
                <div class="text-end mt-3">
                <a href="{{ route('plantillas-contrato.index') }}" class="btn btn-danger">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($plantilla) ? 'Actualizar Plantilla' : 'Guardar Plantilla' }}
                </button>
                </div>
            </form>

            </div>
        </div>
        </div>
    </div>
</div>
@endsection