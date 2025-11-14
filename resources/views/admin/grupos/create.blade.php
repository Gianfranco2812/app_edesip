@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        @if(isset($grupo))
        <h3 class="fw-bold mb-3">Editar Grupo</h3>
        @else
        <h3 class="fw-bold mb-3">Crear Nuevo Grupo</h3>
        @endif
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-body">
            
            <form action="{{ isset($grupo) ? route('grupos.update', $grupo->id) : route('grupos.store') }}" method="POST">
                @csrf
                @if(isset($grupo))
                @method('PUT')
                @endif

                <h4 class="fw-bold mb-3">1. Información del Programa</h4>
                <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                    <label for="programa_id" class="form-label">Programa (Base)</label>
                    <select class="form-select @error('programa_id') is-invalid @enderror" id="programa_id" name="programa_id" required>
                        <option value="" disabled selected>Selecciona un programa...</option>
                        @foreach($programas as $programa)
                        <option value="{{ $programa->id }}" {{ (old('programa_id', $grupo->programa_id ?? '') == $programa->id) ? 'selected' : '' }}>
                            {{ $programa->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('programa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                    <label for="codigo_grupo" class="form-label">Código de Grupo (Único)</label>
                    <input type="text" class="form-control @error('codigo_grupo') is-invalid @enderror" id="codigo_grupo" name="codigo_grupo" value="{{ old('codigo_grupo', $grupo->codigo_grupo ?? '') }}" placeholder="Ej: DIP-2026-01" required>
                    @error('codigo_grupo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                </div>

                <hr>
                <h4 class="fw-bold mb-3">2. Fechas y Horarios</h4>
                <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                    <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                    <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', $grupo->fecha_inicio ?? '') }}" required>
                    @error('fecha_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                    <label for="fecha_termino" class="form-label">Fecha de Término</label>
                    <input type="date" class="form-control @error('fecha_termino') is-invalid @enderror" id="fecha_termino" name="fecha_termino" value="{{ old('fecha_termino', $grupo->fecha_termino ?? '') }}" required>
                    @error('fecha_termino') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                    <label for="modalidad" class="form-label">Modalidad</label>
                    <select class="form-select @error('modalidad') is-invalid @enderror" id="modalidad" name="modalidad" required>
                        <option value="" disabled selected>Selecciona...</option>
                        <option value="Virtual" {{ (old('modalidad', $grupo->modalidad ?? '') == 'Virtual') ? 'selected' : '' }}>Virtual</option>
                        <option value="Híbrido" {{ (old('modalidad', $grupo->modalidad ?? '') == 'Híbrido') ? 'selected' : '' }}>Híbrido</option>
                        <option value="Presencial" {{ (old('modalidad', $grupo->modalidad ?? '') == 'Presencial') ? 'selected' : '' }}>Presencial</option>
                    </select>
                    @error('modalidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                    <label for="horario_texto" class="form-label">Horario (Texto)</label>
                    <input type="text" class="form-control @error('horario_texto') is-invalid @enderror" id="horario_texto" name="horario_texto" value="{{ old('horario_texto', $grupo->horario_texto ?? '') }}" placeholder="Ej: Sábados 9am-1pm" required>
                    @error('horario_texto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                </div>

                <hr>
                <h4 class="fw-bold mb-3">3. Información Financiera y Venta</h4>
                <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                    <label for="costo_total" class="form-label">Costo Total (S/)</label>
                    <input type="number" step="0.01" class="form-control @error('costo_total') is-invalid @enderror" id="costo_total" name="costo_total" value="{{ old('costo_total', $grupo->costo_total ?? '') }}" required>
                    @error('costo_total') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                    <label for="costo_matricula" class="form-label">Costo Matrícula (S/)</label>
                    <input type="number" step="0.01" class="form-control @error('costo_matricula') is-invalid @enderror" id="costo_matricula" name="costo_matricula" value="{{ old('costo_matricula', $grupo->costo_matricula ?? '0.00') }}" required>
                    @error('costo_matricula') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                    <label for="numero_cuotas" class="form-label">N° de Cuotas (Total)</label>
                    <input type="number" class="form-control @error('numero_cuotas') is-invalid @enderror" id="numero_cuotas" name="numero_cuotas" value="{{ old('numero_cuotas', $grupo->numero_cuotas ?? '1') }}" required>
                    @error('numero_cuotas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                    <label for="estado" class="form-label">Estado (para la venta)</label>
                    <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                        <option value="Próximo" {{ (old('estado', $grupo->estado ?? 'Próximo') == 'Próximo') ? 'selected' : '' }}>Próximo (Visible para vender)</option>
                        <option value="En Curso" {{ (old('estado', $grupo->estado ?? '') == 'En Curso') ? 'selected' : '' }}>En Curso</option>
                        <option value="Finalizado" {{ (old('estado', $grupo->estado ?? '') == 'Finalizado') ? 'selected' : '' }}>Finalizado</toFinalizado>
                        <option value="Cancelado" {{ (old('estado', $grupo->estado ?? '') == 'Cancelado') ? 'selected' : '' }}>Cancelado</option>
                    </select>
                    @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                </div>
                <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                    <label for="texto_promocional" class="form-label">Texto Promocional (Opcional)</label>
                    <input type="text" class="form-control @error('texto_promocional') is-invalid @enderror" id="texto_promocional" name="texto_promocional" value="{{ old('texto_promocional', $grupo->texto_promocional ?? '') }}" placeholder="Ej: 20% dscto. pago al contado">
                    @error('texto_promocional') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                </div>
                
                <div class="text-end mt-4">
                <a href="{{ route('grupos.index') }}" class="btn btn-danger">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($grupo) ? 'Actualizar Grupo' : 'Guardar Grupo' }}
                </button>
                </div>
            </form>

            </div>
        </div>
        </div>
    </div>
</div>
@endsection