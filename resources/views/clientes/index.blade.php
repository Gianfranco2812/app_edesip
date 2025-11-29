@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Directorio de Clientes</h3>
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="card-title">Listado de Clientes</h4>
                    <a href="{{ route('clientes.create') }}" class="btn btn-primary btn-round">
                        <i class="fa fa-plus"></i> Registrar Contacto
                    </a>
                </div>

                <form action="{{ route('clientes.index') }}" method="GET" class="p-3 bg-light rounded border">
                    <div class="row g-3">
                        
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Buscar</label>
                            <input type="text" name="search" class="form-control" 
                                placeholder="Nombre, DNI o Teléfono..." 
                                value="{{ request('search') }}">
                        </div>

                        @role('Admin')
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Filtrar por Asesor</label>
                            <select name="vendedor_id" class="form-select">
                                <option value="">Todos los asesores</option>
                                @foreach($vendedores as $vendedor)
                                    <option value="{{ $vendedor->id }}" {{ request('vendedor_id') == $vendedor->id ? 'selected' : '' }}>
                                        {{ $vendedor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endrole

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Registrado Desde</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Hasta</label>
                            <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                        </div>
                        
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="d-grid gap-2 d-md-block">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i>
                                </button>
                                <a href="{{ route('clientes.index') }}" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Limpiar Filtros">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                    <th>Nombre Completo</th>
                    <th>Documento</th>
                    <th>Contacto</th>
                    <th>Estado</th>
                    @role('Admin')
                        <th>Asesor</th>
                    @endrole
                    <th>Fecha Registro</th>
                    <th style="width: 10%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clientes as $cliente)
                    <tr>
                    <td>
                        <span class="fw-bold">{{ $cliente->nombre }} {{ $cliente->apellido }}</span>
                    </td>
                    <td>
                        @if($cliente->numero_documento)
                            <span >{{ $cliente->tipo_documento }}: {{ $cliente->numero_documento }}</span>
                        @else
                            <span class="text-muted small">Sin registrar</span>
                        @endif
                    </td>
                    <td>
                        <div class="small"><i class="fas fa-envelope me-1"></i> {{ $cliente->email }}</div>
                        <div class="small"><i class="fas fa-phone me-1"></i> {{ $cliente->telefono }}</div>
                    </td>
                    <td>
                        @php
                            $badgeColor = match($cliente->estado) {
                                'Prospecto' => 'secondary',
                                'En Proceso' => 'warning',
                                'Confirmado' => 'info',
                                'Alumno Activo' => 'success',
                                'Finalizado' => 'dark',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-{{ $badgeColor }}">{{ $cliente->estado }}</span>
                    </td>
                    @role('Admin')
                        <td>
                            <small>{{ $cliente->vendedor->name ?? 'Sistema' }}</small>
                        </td>
                    @endrole
                    <td>
                        <small class="text-muted">{{ $cliente->created_at->format('d/m/Y') }}</small>
                    </td>
                    <td>
                        <div class="form-button-action">
                        {{-- Botón Vender (Bolsa) --}}
                        <a href="{{ route('ventas.create', ['cliente_id' => $cliente->id]) }}" 
                            data-bs-toggle="tooltip" 
                            title="Matricular" 
                            class="btn btn-link btn-success btn-lg">
                            <i class="fas fa-cart-plus"></i>
                        </a>

                        {{-- Botón Editar --}}
                        <a href="{{ route('clientes.edit', $cliente->id) }}" 
                            data-bs-toggle="tooltip" 
                            title="Editar" 
                            class="btn btn-link btn-primary btn-lg">
                            <i class="fa fa-edit"></i>
                        </a>
                        </div>
                    </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ Auth::user()->hasRole('Admin') ? '7' : '6' }}" class="text-center p-4">
                            <div class="text-muted">
                                <i class="fas fa-search fa-2x mb-3"></i><br>
                                No se encontraron clientes con esos criterios de búsqueda.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                </table>
                
                {{-- Paginación que recuerda los filtros --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $clientes->appends(request()->query())->links() }}
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection