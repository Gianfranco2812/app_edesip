@extends('layouts.admin')

@section('content')
<div class="page-inner">
    {{-- Cabecera con saludo --}}
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
        <h3 class="fw-bold mb-3">Panel de Control</h3>
        <h6 class="op-7 mb-2">Bienvenido, {{ Auth::user()->name }} ({{ Auth::user()->roles->first()->name ?? 'Usuario' }})</h6>
        </div>
        <div class="ms-md-auto py-2 py-md-0">
        <a href="{{ route('ventas.index') }}" class="btn btn-primary btn-round">Ver Ventas</a>
        <a href="{{ route('clientes.create') }}" class="btn btn-secondary btn-round">Nuevo Prospecto</a>
        </div>
    </div>

    {{-- FILA DE TARJETAS (KPIs) --}}
    <div class="row">
        
        <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                <div class="icon-big text-center icon-success bubble-shadow-small">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                </div>
                <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                    <p class="card-category">Ingresos (Mes)</p>
                    <h4 class="card-title">S/ {{ number_format($totalIngresos, 2) }}</h4>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>

        <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                <div class="icon-big text-center icon-info bubble-shadow-small">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                </div>
                <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                    <p class="card-category">Ventas (Mes)</p>
                    <h4 class="card-title">{{ $ventasMes }}</h4>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>

        <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                <div class="icon-big text-center icon-danger bubble-shadow-small">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                </div>
                <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                    <p class="card-category">Por Cobrar (Total)</p>
                    <h4 class="card-title">S/ {{ number_format($totalDeuda, 2) }}</h4>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>

        <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
            <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                <div class="icon-big text-center icon-secondary bubble-shadow-small">
                    <i class="fas fa-user-graduate"></i>
                </div>
                </div>
                <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                    <p class="card-category">Alumnos Activos</p>
                    <h4 class="card-title">{{ $totalAlumnos }}</h4>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>

    {{-- SEGUNDA FILA: TABLA RÁPIDA --}}
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header">
            <div class="card-title">Últimas Matrículas Registradas</div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Alumno</th>
                                <th>Curso</th>
                                <th>Estado</th>
                                <th class="text-end">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimasVentas as $venta)
                            <tr>
                                <td>{{ $venta->fecha_venta->format('d/m/Y') }}</td>
                                <td class="fw-bold">{{ $venta->cliente->nombre_completo }}</td>
                                <td>{{ Str::limit($venta->grupo->programa->nombre, 30) }}</td>
                                <td>
                                    @if($venta->estado == 'Cerrada')
                                        <span class="badge bg-success">Activa</span>
                                    @elseif($venta->estado == 'En Proceso')
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    @else
                                        <span class="badge bg-danger">Anulada</span>
                                    @endif
                                </td>
                                <td class="text-end">S/ {{ number_format($venta->costo_total_venta, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Aún no hay ventas registradas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>

</div>
@endsection