@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Gestión de Ventas y Matrículas</h3>
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="card-title">Historial de Ventas</h4>
                    {{-- (El botón de crear venta se hace desde Clientes, así que aquí no hace falta, o puedes poner un link a clientes) --}}
                    <a href="{{ route('clientes.index') }}" class="btn btn-primary btn-round">
                        <i class="fa fa-plus"></i> Nueva Matrícula
                    </a>
                </div>

                <form action="{{ route('ventas.index') }}" method="GET" class="p-3 bg-light rounded border">
                    <div class="row g-2 align-items-end">
                        
                        {{-- 1. BUSCADOR (Enter para enviar) --}}
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Buscar</label>
                            <input type="text" name="search" class="form-control form-control-sm" 
                                placeholder="Alumno, DNI o Grupo..." 
                                value="{{ request('search') }}">
                        </div>

                        {{-- 2. PROGRAMA (Automático) --}}
                        <div class="col-md-2">
                            <label class="form-label fw-bold small">Programa</label>
                            <select name="programa_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach($programas as $programa)
                                    <option value="{{ $programa->id }}" {{ request('programa_id') == $programa->id ? 'selected' : '' }}>
                                        {{ $programa->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 3. ESTADO (Automático) --}}
                        <div class="col-md-2">
                            <label class="form-label fw-bold small">Estado Contrato</label>
                            <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                
                                {{-- Opción 1: Contrato Pendiente (o venta sin contrato aún) --}}
                                <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>
                                    Pendiente
                                </option>
                                
                                {{-- Opción 2: Contrato Firmado (Venta Cerrada) --}}
                                <option value="Firmado" {{ request('estado') == 'Firmado' ? 'selected' : '' }}>
                                    Firmado
                                </option>
                                
                                {{-- Opción 3: Anulada (Sigue siendo un estado de la venta) --}}
                                <option value="Anulada" {{ request('estado') == 'Anulada' ? 'selected' : '' }}>
                                    Anulada
                                </option>
                            </select>
                        </div>

                        {{-- 4. VENDEDOR (Solo Admin - Automático) --}}
                        @role('Admin')
                        <div class="col-md-2">
                            <label class="form-label fw-bold small">Vendedor</label>
                            <select name="vendedor_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach($vendedores as $vendedor)
                                    <option value="{{ $vendedor->id }}" {{ request('vendedor_id') == $vendedor->id ? 'selected' : '' }}>
                                        {{ $vendedor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endrole
                        
                        {{-- 5. FECHA (Automático) --}}
                        <div class="col-md-2">
                            <label class="form-label fw-bold small">Fecha Venta</label>
                            <input type="date" name="fecha_inicio" class="form-control form-control-sm" 
                                value="{{ request('fecha_inicio') }}" onchange="this.form.submit()">
                        </div>

                        {{-- 6. BOTÓN LIMPIAR (Solo si hay filtros activos) --}}
                        <div class="col-md-1">
                            @if(request()->anyFilled(['search', 'programa_id', 'estado', 'vendedor_id', 'fecha_inicio']))
                                <a href="{{ route('ventas.index') }}" class="btn btn-outline-danger btn-sm w-100" data-bs-toggle="tooltip" title="Quitar Filtros">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Botón invisible para permitir "Enter" en el buscador --}}
                    <button type="submit" style="display: none;"></button>
                </form>
            </div>

            <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Programa - Grupo</th>
                    <th>Total</th>
                    <th>Contrato</th>
                    @role('Admin')
                        <th>Asesor</th>
                    @endrole
                    <th style="width: 10%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ventas as $venta)
                    <tr>
                    <td>
                        {{ $venta->fecha_venta->format('d/m/Y') }}
                        <br><small class="text-muted">{{ $venta->fecha_venta->format('H:i') }}</small>
                    </td>
                    <td>
                        <span class="fw-bold">{{ $venta->cliente->nombre_completo ?? 'N/A' }}</span>
                        <br>
                        <small class="text-muted">{{ $venta->cliente->numero_documento }}</small>
                    </td>
                    <td>
                        {{ $venta->grupo->programa->nombre ?? 'N/A' }}
                        <br>
                        <span class="badge badge-black">{{ $venta->grupo->codigo_grupo ?? 'N/A' }}</span>
                    </td>
                    <td class="fw-bold">S/ {{ number_format($venta->costo_total_venta, 2) }}</td>
                    <td>
                        @if($venta->contrato && $venta->contrato->estado == 'Firmado')
                        <i class="fas fa-check-circle text-success" title="Firmado"></i> Firmado
                        @else
                        <i class="fas fa-clock text-warning" title="Pendiente"></i> Pendiente
                        @endif
                    </td>
                    @role('Admin')
                        <td><small>{{ $venta->vendedor->name ?? 'N/A' }}</small></td>
                    @endrole
                    <td>
                        <div class="form-button-action">
                            
                            @if($venta->contrato && $venta->contrato->estado == 'Pendiente' && $venta->estado != 'Anulada')
                                <a href="{{ route('ventas.previsualizar', $venta->id) }}" 
                                class="btn btn-warning btn-sm fw-bold me-1"
                                data-bs-toggle="tooltip" 
                                title="Gestionar Firma / Enviar Link">
                                    <i class="fas fa-file-contract text-dark"></i>
                                </a>
                            @endif

                            @if($venta->contrato && $venta->contrato->ruta_pdf)
                                <a href="{{ route('contratos.ver_pdf', $venta->contrato->id) }}" 
                                target="_blank"
                                class="btn btn-danger btn-sm me-1" 
                                data-bs-toggle="tooltip"
                                title="Ver Contrato Firmado (PDF)">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            @endif
                        
                        {{-- ELIMINAR (Solo Admin) --}}
                        @role('Admin')
                            <form action="{{ route('ventas.destroy', $venta->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" data-bs-toggle="tooltip" title="Borrar Definitivamente" class="btn btn-link btn-danger" onclick="return confirm('¿Borrar registro?');">
                                    <i class="fa fa-times"></i>
                                </button>
                            </form>
                        @endrole
                        </div>
                    </td>
                    </tr>
                    @empty
                    <tr>
                    <td colspan="{{ Auth::user()->hasRole('Admin') ? '8' : '7' }}" class="text-center p-4">
                        No se encontraron ventas con estos filtros.
                    </td>
                    </tr>
                    @endforelse
                </tbody>
                </table>
                
                <div class="d-flex justify-content-center mt-3">
                    {{ $ventas->appends(request()->query())->links() }}
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection