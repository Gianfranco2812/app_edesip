@extends('layouts.admin')

@section('content')
    <div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Gestión de Cobranzas</h3>
    </div>
    
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            
            <div class="card-header">
                <h4 class="card-title mb-3">Filtros de Búsqueda</h4>
                <form action="{{ route('cobranzas.index') }}" method="GET">
                    <div class="row g-3">
                        
                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" name="search" class="form-control" 
                                placeholder="Nombre, DNI o Grupo..." 
                                value="{{ request('search') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Programa</label>
                            <select name="programa_id" class="form-select">
                                <option value="">Todos los programas</option>
                                @foreach($programas as $programa)
                                    <option value="{{ $programa->id }}" {{ request('programa_id') == $programa->id ? 'selected' : '' }}>
                                        {{ $programa->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select">
                                {{-- Opción por defecto: Busca todo lo pendiente --}}
                                <option value="">Todo lo que se debe</option>
                                
                                {{-- Opción 1: Historial de Pagos --}}
                                <option value="Pagada" {{ request('estado') == 'Pagada' ? 'selected' : '' }}>
                                    ✅ Solo Pagadas
                                </option>
                                
                                {{-- Opción 2: Lo crítico (Vencido) --}}
                                <option value="Vencida" {{ request('estado') == 'Vencida' ? 'selected' : '' }}>
                                    🚨 Solo Vencidas
                                </option>
                                
                                {{-- Opción 3: Lo que viene (Futuro) --}}
                                <option value="PorVencer" {{ request('estado') == 'PorVencer' ? 'selected' : '' }}>
                                    📅 Próximas a vencer
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Vence Desde</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Hasta</label>
                            <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 text-end">
                            <a href="{{ route('cobranzas.index') }}" class="btn btn-outline-danger btn-sm me-2">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i> Filtrar Resultados
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover"> <thead>
                    <tr>
                    <th>Vencimiento</th>
                    <th>Cliente</th>
                    <th>Concepto</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th style="width: 10%">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cuotas as $cuota)
                    <tr>
                    <td>
                        {{ $cuota->fecha_vencimiento->format('d/m/Y') }}
                        @if($cuota->estado_cuota == 'Vencida' || ($cuota->estado_cuota == 'Pendiente' && $cuota->fecha_vencimiento < now()))
                            <br><small class="text-danger fw-bold">¡Vencida!</small>
                        @endif
                    </td>
                    <td>
                        <span class="fw-bold">{{ $cuota->venta->cliente->nombre_completo }}</span>
                        <br>
                        <small class="text-muted d-block text-truncate" style="max-width: 200px;">
                            {{ $cuota->venta->grupo->programa->nombre }}
                        </small>
                        <small class="badge badge-black">{{ $cuota->venta->grupo->codigo_grupo }}</small>
                    </td>
                    <td>{{ $cuota->descripcion }}</td>
                    <td class="fw-bold">S/ {{ number_format($cuota->monto_cuota, 2) }}</td>
                    <td>
                        @if($cuota->estado_cuota == 'Pagada')
                        <span class="badge bg-success">Pagada</span>
                        @elseif($cuota->fecha_vencimiento < now())
                        <span class="badge bg-danger">Vencida</span>
                        @else
                        <span class="badge bg-warning text-dark">Pendiente</span>
                        @endif
                    </td>
                    <td>
                        @if($cuota->estado_cuota != 'Pagada')
                            <a href="{{ route('cobranzas.edit', $cuota->id) }}" 
                            class="btn btn-success btn-sm fw-bold">
                            <i class="fas fa-money-bill-wave"></i> Pagar
                            </a>
                        @else
                        <span class="text-success"><i class="fas fa-check"></i> {{ $cuota->fecha_pago ? $cuota->fecha_pago->format('d/m') : '' }}</span>
                        @endif
                    </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center p-4">
                            <i class="fas fa-search mb-2" style="font-size: 2rem; color: #ccc;"></i><br>
                            No se encontraron cuotas con los filtros seleccionados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                </table>
                
                {{-- Paginación (Si usas paginate en el controlador) --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $cuotas->appends(request()->query())->links() }}
                </div>

            </div>
            </div>
        </div>
        </div>
    </div>
    </div>
@endsection