@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Reportes Estratégicos</h3>
    </div>

    {{-- FILTRO GENERAL DE FECHAS --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('reportes.index') }}" method="GET" class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Desde</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Hasta</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ $fechaFin }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Actualizar Datos</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="reportesTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="ventas-tab" data-bs-toggle="tab" href="#ventas" role="tab">📊 Reporte de Ventas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="finanzas-tab" data-bs-toggle="tab" href="#finanzas" role="tab">💰 Reporte Financiero (Caja)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" id="morosidad-tab" data-bs-toggle="tab" href="#morosidad" role="tab">🚨 Reporte de Morosidad</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="reportesTabContent">
                        
                        {{-- PESTAÑA 1: VENTAS --}}
                        <div class="tab-pane fade show active" id="ventas" role="tabpanel">
                            <div class="row text-center mb-4">
                                <div class="col-md-6 border-end">
                                    <h6 class="text-muted">Total Ventas (Cantidad)</h6>
                                    <h2 class="fw-bold text-primary">{{ $totalVentas }}</h2>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Monto Total Vendido</h6>
                                    <h2 class="fw-bold text-success">S/ {{ number_format($montoVendido, 2) }}</h2>
                                </div>
                            </div>
                            <div class="text-center">
                                <a href="{{ route('reportes.exportar', ['tipo' => 'ventas'] + request()->all()) }}" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Descargar Excel Detallado de Ventas
                                </a>
                            </div>
                        </div>

                        {{-- PESTAÑA 2: FINANZAS --}}
                        <div class="tab-pane fade" id="finanzas" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Este reporte muestra el dinero que realmente ingresó (cuotas pagadas) en el rango de fechas seleccionado.
                            </div>
                            <div class="text-center mb-4">
                                <h6 class="text-muted">Ingresos Totales (Caja)</h6>
                                <h1 class="fw-bold text-success display-4">S/ {{ number_format($totalIngresos, 2) }}</h1>
                            </div>
                            <div class="text-center">
                                <a href="{{ route('reportes.exportar', ['tipo' => 'ingresos'] + request()->all()) }}" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Descargar Reporte de Caja
                                </a>
                            </div>
                        </div>

                        {{-- PESTAÑA 3: MOROSIDAD --}}
                        <div class="tab-pane fade" id="morosidad" role="tabpanel">
                            <div class="row text-center mb-4">
                                <div class="col-md-6 border-end">
                                    <h6 class="text-muted text-danger fw-bold">Deuda Vencida Total</h6>
                                    <h2 class="fw-bold text-danger">S/ {{ number_format($totalDeudaVencida, 2) }}</h2>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Cantidad de Alumnos Morosos</h6>
                                    <h2 class="fw-bold">{{ $cantidadDeudores }}</h2>
                                </div>
                            </div>
                            <div class="text-center">
                                <a href="{{ route('reportes.exportar', ['tipo' => 'morosidad']) }}" class="btn btn-danger">
                                    <i class="fas fa-file-excel"></i> Descargar Lista Negra (Morosos)
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection