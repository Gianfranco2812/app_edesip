@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        @if(isset($cliente))
        <h3 class="fw-bold mb-3">Editar Cliente</h3>
        @else
        <h3 class="fw-bold mb-3">Registrar Nuevo Contacto (Prospecto)</h3>
        @endif
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-body">
            
            <form action="{{ isset($cliente) ? route('clientes.update', $cliente->id) : route('clientes.store') }}" method="POST">
                @csrf
                @if(isset($cliente))
                    @method('PUT')
                @endif
                
                @if(request('next') == 'matricula')
                    <input type="hidden" name="next_step" value="matricula">
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Faltan datos:</strong> Por favor completa el <b>Tipo/N° Documento</b> y la <b>Dirección</b> para poder generar el contrato.
                    </div>
                @endif
                <h4 class="fw-bold mb-3">1. Datos de Contacto (Obligatorios)</h4>
                <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $cliente->nombre ?? '') }}" required>
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                    <label for="apellido" class="form-label">Apellido</label>
                    <input type="text" class="form-control @error('apellido') is-invalid @enderror" id="apellido" name="apellido" value="{{ old('apellido', $cliente->apellido ?? '') }}" required>
                    @error('apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                </div>
                <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $cliente->email ?? '') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono', $cliente->telefono ?? '') }}" required>
                    @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                </div>

                <hr>

                <h4 class="fw-bold mb-3">2. Datos Personales (Opcionales al inicio)</h4>
                <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                    <label for="tipo_documento" class="form-label">Tipo Documento</label>
                    <select class="form-select @error('tipo_documento') is-invalid @enderror" id="tipo_documento" name="tipo_documento">
                        <option value="" disabled selected>Selecciona...</option>
                        <option value="DNI" {{ (old('tipo_documento', $cliente->tipo_documento ?? '') == 'DNI') ? 'selected' : '' }}>DNI</option>
                        <option value="Carnet Ext." {{ (old('tipo_documento', $cliente->tipo_documento ?? '') == 'Carnet Ext.') ? 'selected' : '' }}>Carnet Ext.</option>
                        <option value="Pasaporte" {{ (old('tipo_documento', $cliente->tipo_documento ?? '') == 'Pasaporte') ? 'selected' : '' }}>Pasaporte</option>
                    </select>
                    @error('tipo_documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                    <label for="numero_documento" class="form-label">N° de Documento</label>
                    <input type="text" class="form-control @error('numero_documento') is-invalid @enderror" id="numero_documento" name="numero_documento" value="{{ old('numero_documento', $cliente->numero_documento ?? '') }}">
                    @error('numero_documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                    <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                    <input type="date" class="form-control @error('fecha_nacimiento') is-invalid @enderror" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $cliente->fecha_nacimiento ?? '') }}">
                    @error('fecha_nacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                </div>
                <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{ old('direccion', $cliente->direccion ?? '') }}">
                    @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                        <option value="Prospecto" {{ (old('estado', $cliente->estado ?? 'Prospecto') == 'Prospecto') ? 'selected' : '' }}>Prospecto</option>
                        <option value="En Proceso" {{ (old('estado', $cliente->estado ?? '') == 'En Proceso') ? 'selected' : '' }}>En Proceso</option>
                        <option value="Confirmado" {{ (old('estado', $cliente->estado ?? '') == 'Confirmado') ? 'selected' : '' }}>Confirmado</option>
                        <option value="Alumno Activo" {{ (old('estado', $cliente->estado ?? '') == 'Alumno Activo') ? 'selected' : '' }}>Alumno Activo</option>
                        <option value="Finalizado" {{ (old('estado', $cliente->estado ?? '') == 'Finalizado') ? 'selected' : '' }}>Finalizado</option>
                    </select>
                    @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                </div>
                
                <div class="text-end mt-4">
                        <a href="{{ route('clientes.index') }}" class="btn btn-danger">Cancelar</a>

                        @if(isset($cliente))
                            {{-- BOTONES DE EDICIÓN --}}
                            
                            @if(request('next') == 'matricula')
                                {{-- Si venimos rebotados de la matrícula, mostramos este botón especial --}}
                                <button type="submit" class="btn btn-warning fw-bold">
                                    <i class="fas fa-save"></i> Guardar Datos y Continuar a Matrícula
                                </button>
                            @else
                                {{-- Edición normal --}}
                                <a href="{{ route('ventas.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-success me-2">
                                    <i class="fas fa-cart-plus"></i> Matricular
                                </a>
                                <button type="submit" class="btn btn-primary">Actualizar Cliente</button>
                            @endif

                        @else
                            {{-- BOTONES DE CREACIÓN (Prospecto nuevo) --}}
                            <button type="submit" name="action" value="save_prospect" class="btn btn-info">Guardar Solo Prospecto</button>
                            <button type="submit" name="action" value="matriculate" class="btn btn-primary">Matricular Ahora</button>
                        @endif
                    </div>
            </form>

            </div>
        </div>
        </div>
    </div>
</div>
@endsection