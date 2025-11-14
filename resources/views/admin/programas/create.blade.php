@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        @if(isset($programa))
        <h3 class="fw-bold mb-3">Editar Programa</h3>
        @else
        <h3 class="fw-bold mb-3">Crear Nuevo Programa</h3>
        @endif
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-body">
            
            {{-- ¡¡IMPORTANTE: enctype para subir archivos!! --}}
            <form action="{{ isset($programa) ? route('programas.update', $programa->id) : route('programas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($programa))
                @method('PUT')
                @endif

                <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Programa</label>
                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $programa->nombre ?? '') }}" required>
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                    <label for="descripcion_detallada" class="form-label">Descripción Detallada (temario, objetivos, etc.)</label>
                    <textarea class="form-control @error('descripcion_detallada') is-invalid @enderror" id="descripcion_detallada" name="descripcion_detallada" rows="10">{{ old('descripcion_detallada', $programa->descripcion_detallada ?? '') }}</textarea>
                    @error('descripcion_detallada') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                    <label for="tipo_programa_id" class="form-label">Tipo de Programa</label>
                    <select class="form-select @error('tipo_programa_id') is-invalid @enderror" id="tipo_programa_id" name="tipo_programa_id" required>
                        <option value="" disabled selected>Selecciona un tipo...</option>
                        @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id }}" {{ (old('tipo_programa_id', $programa->tipo_programa_id ?? '') == $tipo->id) ? 'selected' : '' }}>
                            {{ $tipo->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('tipo_programa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                    <label for="plantilla_contrato_id" class="form-label">Plantilla de Contrato</label>
                    <select class="form-select @error('plantilla_contrato_id') is-invalid @enderror" id="plantilla_contrato_id" name="plantilla_contrato_id" required>
                        <option value="" disabled selected>Selecciona una plantilla...</option>
                        @foreach($plantillas as $plantilla)
                        <option value="{{ $plantilla->id }}" {{ (old('plantilla_contrato_id', $programa->plantilla_contrato_id ?? '') == $plantilla->id) ? 'selected' : '' }}>
                            {{ $plantilla->nombre_plantilla }}
                        </option>
                        @endforeach
                    </select>
                    @error('plantilla_contrato_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                    <label for="horas_totales" class="form-label">Horas Totales</label>
                    <input type="number" class="form-control @error('horas_totales') is-invalid @enderror" id="horas_totales" name="horas_totales" value="{{ old('horas_totales', $programa->horas_totales ?? '') }}">
                    @error('horas_totales') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                        <option value="Activo" {{ (old('estado', $programa->estado ?? '') == 'Activo') ? 'selected' : '' }}>Activo</option>
                        <option value="Archivado" {{ (old('estado', $programa->estado ?? '') == 'Archivado') ? 'selected' : '' }}>Archivado</option>
                    </select>
                    @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                </div>

                <hr>
                
                <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                    <label for="imagen_promocional" class="form-label">Imagen Promocional (Opcional)</label>
                    <input type="file" class="form-control @error('imagen_promocional') is-invalid @enderror" id="imagen_promocional" name="imagen_promocional" accept="image/*">
                    @error('imagen_promocional') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if(isset($programa) && $programa->imagen_promocional_url)
                        <small class="form-text text-muted mt-2">
                        Archivo actual: <a href="{{ Storage::url($programa->imagen_promocional_url) }}" target="_blank">Ver Imagen</a>
                        </small>
                    @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                    <label for="brochure_pdf" class="form-label">Brochure PDF (Opcional)</label>
                    <input type="file" class="form-control @error('brochure_pdf') is-invalid @enderror" id="brochure_pdf" name="brochure_pdf" accept="application/pdf">
                    @error('brochure_pdf') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    @if(isset($programa) && $programa->brochure_pdf_url)
                        <small class="form-text text-muted mt-2">
                        Archivo actual: <a href="{{ Storage::url($programa->brochure_pdf_url) }}" target="_blank">Ver PDF</a>
                        </small>
                    @endif
                    </div>
                </div>
                </div>

                <div class="text-end mt-4">
                <a href="{{ route('programas.index') }}" class="btn btn-danger">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($programa) ? 'Actualizar Programa' : 'Guardar Programa' }}
                </button>
                </div>
            </form>

            </div>
        </div>
        </div>
    </div>
</div>
@endsection