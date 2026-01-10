@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Monitor de Cobranzas</h3>
    </div>
    
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            
            <div class="card-header">
                <h4 class="card-title mb-3">Filtros de Morosidad</h4>
                
                <form action="{{ route('cobranzas.index') }}" method="GET" class="p-3 bg-light rounded border">
                    <div class="row g-3">
                        
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Buscar</label>
                            <input type="text" name="search" class="form-control" 
                                placeholder="Alumno, DNI..." value="{{ request('search') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Grupo</label>
                            <select name="grupo_id" class="form-select">
                                <option value="">Todos los grupos</option>
                                @foreach($grupos as $grupo)
                                    <option value="{{ $grupo->id }}" {{ request('grupo_id') == $grupo->id ? 'selected' : '' }}>
                                        {{ $grupo->codigo_grupo }} - {{ Str::limit($grupo->programa->nombre, 20) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Estado Deuda</label>
                            <select name="estado_deuda" class="form-select">
                                <option value="">Todos</option>
                                <option value="vencido" {{ request('estado_deuda') == 'vencido' ? 'selected' : '' }} class="text-danger fw-bold">
                                    🔴 Retrasados
                                </option>
                                <option value="por_vencer" {{ request('estado_deuda') == 'por_vencer' ? 'selected' : '' }} class="text-warning fw-bold">
                                    🟠 Vence Pronto
                                </option>
                                <option value="al_dia" {{ request('estado_deuda') == 'al_dia' ? 'selected' : '' }} class="text-success fw-bold">
                                    🟢 Al día
                                </option>
                            </select>
                        </div>

                        @role('Admin')
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Asesor</label>
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

                        <div class="col-md-{{ Auth::user()->hasRole('Admin') ? '2' : '4' }} d-flex align-items-end">
                            <div class="d-grid gap-2 d-md-block w-100 text-end">
                                <a href="{{ route('cobranzas.index') }}" class="btn btn-outline-danger me-1" title="Limpiar">
                                    <i class="fas fa-times"></i>
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                <thead>
                    <tr>
                    <th>Semáforo</th>
                    <th>Alumno</th>
                    <th>Grupo</th>
                    <th>Próximo Vencimiento</th>
                    <th>Deuda Total</th>
                    <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ventas as $venta)
                        @php
                            $proximaCuota = $venta->cuotas->first();
                            $color = 'success'; $texto = 'Al día'; $icono = 'fa-check-circle';

                            if ($proximaCuota) {
                                $fechaVenc = \Carbon\Carbon::parse($proximaCuota->fecha_vencimiento);
                                $hoy = now()->startOfDay();
                                $diasRestantes = $hoy->diffInDays($fechaVenc, false); 

                                if ($diasRestantes < 0) {
                                    $color = 'danger'; $texto = 'Retrasado (' . abs(intval($diasRestantes)) . 'd)'; $icono = 'fa-exclamation-circle';
                                } elseif ($diasRestantes <= 3) {
                                    $color = 'warning'; $texto = 'Vence en ' . intval($diasRestantes) . 'd'; $icono = 'fa-clock';
                                }
                            } else {
                                $color = 'primary'; $texto = 'Finalizado'; $icono = 'fa-trophy';
                            }
                        @endphp

                    <tr>
                    <td><span class="badge bg-{{ $color }} w-100 py-2"><i class="fas {{ $icono }} me-1"></i> {{ $texto }}</span></td>
                    <td>
                        <div class="fw-bold">{{ $venta->cliente->nombre_completo }}</div>
                        <small class="text-muted">{{ $venta->cliente->numero_documento }}</small>
                    </td>
                    <td>
                        <span class="badge badge-black">{{ $venta->grupo->codigo_grupo }}</span>
                        <br><small class="text-muted">{{ Str::limit($venta->grupo->programa->nombre, 20) }}</small>
                    </td>
                    <td>
                        @if($proximaCuota)
                            <span class="{{ $color == 'danger' ? 'text-danger fw-bold' : '' }}">
                                {{ \Carbon\Carbon::parse($proximaCuota->fecha_vencimiento)->format('d/m/Y') }}
                            </span>
                            <br><small class="text-muted">{{ $proximaCuota->descripcion }}</small>
                        @else - @endif
                    </td>
                    <td class="fw-bold">S/ {{ number_format($venta->cuotas->sum('monto_cuota'), 2) }}</td>
                    <td class="text-end">
                            @if($venta->vouchers_por_validar > 0)
                                <a href="{{ route('cobranzas.show', $venta->id) }}" 
                                class="btn btn-sm btn-danger position-relative me-2"
                                data-bs-toggle="tooltip" 
                                title="¡Hay {{ $venta->vouchers_por_validar }} pago(s) por validar!">
                                    
                                    <i class="fas fa-bell fa-beat"></i>
                                    
                                    {{-- Opcional: El numerito en la esquina --}}
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">
                                        {{ $venta->vouchers_por_validar }}
                                    </span>
                                </a>
                            @endif

                            {{-- Botón Normal de Ver/Gestionar --}}
                            <a href="{{ route('cobranzas.show', $venta->id) }}" class="btn btn-sm btn-outline-primary" title="Ver Cobranza">
                                <i class="fas fa-eye"></i>
                            </a>

                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center p-4">No se encontraron registros.</td></tr>
                    @endforelse
                </tbody>
                </table>
                <div class="mt-3">{{ $ventas->appends(request()->query())->links() }}</div>
            </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection