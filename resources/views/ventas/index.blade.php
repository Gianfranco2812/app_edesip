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
                    <div class="row g-3">
                        
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Buscar</label>
                            <input type="text" name="search" class="form-control" 
                                placeholder="Alumno, DNI o Grupo..." 
                                value="{{ request('search') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Programa</label>
                            <select name="programa_id" class="form-select">
                                <option value="">Todos</option>
                                @foreach($programas as $programa)
                                    <option value="{{ $programa->id }}" {{ request('programa_id') == $programa->id ? 'selected' : '' }}>
                                        {{ $programa->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="">Todos</option>
                                <option value="En Proceso" {{ request('estado') == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                                <option value="Cerrada" {{ request('estado') == 'Cerrada' ? 'selected' : '' }}>Cerrada (Ok)</option>
                                <option value="Anulada" {{ request('estado') == 'Anulada' ? 'selected' : '' }}>Anulada</option>
                            </select>
                        </div>

                        @role('Admin')
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Vendedor</label>
                            <select name="vendedor_id" class="form-select">
                                <option value="">Todos</option>
                                @foreach($vendedores as $vendedor)
                                    <option value="{{ $vendedor->id }}" {{ request('vendedor_id') == $vendedor->id ? 'selected' : '' }}>
                                        {{ $vendedor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endrole
                        
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Fecha Venta</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                        </div>

                        <div class="col-md-12 text-end mt-3">
                            <a href="{{ route('ventas.index') }}" class="btn btn-outline-danger btn-sm me-2">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Programa / Grupo</th>
                    <th>Total</th>
                    <th>Estado</th>
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
                        @if($venta->estado == 'Cerrada')
                        <span class="badge bg-success">Cerrada</span>
                        @elseif($venta->estado == 'En Proceso')
                        <span class="badge bg-warning text-dark">En Proceso</span>
                        @else
                        <span class="badge bg-danger">Anulada</span>
                        @endif
                    </td>
                    <td>
                        @if($venta->contrato && $venta->contrato->estado == 'Confirmado')
                        <i class="fas fa-check-circle text-success" title="Firmado"></i> Confirmado
                        @else
                        <i class="fas fa-clock text-warning" title="Pendiente"></i> Pendiente
                        @endif
                    </td>
                    @role('Admin')
                        <td><small>{{ $venta->vendedor->name ?? 'N/A' }}</small></td>
                    @endrole
                    <td>
                        <div class="form-button-action">
                        
                        {{-- FIRMAR (Si pendiente) --}}
                        @if($venta->contrato && $venta->contrato->estado == 'Pendiente' && $venta->estado != 'Anulada')
                            <a href="{{ route('contratos.mostrar', $venta->contrato->token_acceso) }}" 
                            target="_blank"
                            data-bs-toggle="tooltip" 
                            title="Firmar Contrato" 
                            class="btn btn-link btn-secondary btn-lg">
                            <i class="fas fa-file-signature"></i>
                            </a>
                        @endif

                        {{-- ANULAR (Si no anulada) --}}
                        @if($venta->estado != 'Anulada')
                            <form action="{{ route('ventas.anular', $venta->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" data-bs-toggle="tooltip" title="Anular" class="btn btn-link btn-warning btn-lg" onclick="return confirm('¿Retirar alumno?');">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </form>
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