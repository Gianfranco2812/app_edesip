@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header d-flex justify-content-between">
        <h3 class="fw-bold mb-3">Estado de Cuenta</h3>
        <a href="{{ route('cobranzas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Monitor
        </a>
    </div>
    
    {{-- TARJETA DE RESUMEN DEL ALUMNO --}}
    <div class="card mb-4 border-left-primary">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="fw-bold text-primary mb-1">{{ $venta->cliente->nombre_completo }}</h4>
                    <p class="mb-0">Grupo: <strong>{{ $venta->grupo->codigo_grupo }}</strong> ({{ $venta->grupo->programa->nombre }})</p>
                </div>
                <div class="col-md-6 text-end">
                    <h2 class="fw-bold {{ $venta->cuotas->where('estado_cuota', '!=', 'Pagada')->count() > 0 ? 'text-danger' : 'text-success' }}">
                        Deuda: S/ {{ number_format($venta->cuotas->where('estado_cuota', '!=', 'Pagada')->sum('monto_cuota'), 2) }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Cronograma de Pagos</h4>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                <thead>
                    <tr>
                    <th>#</th>
                    <th>Concepto</th>
                    <th>Vencimiento</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($venta->cuotas->sortBy('fecha_vencimiento') as $index => $cuota)
                    <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $cuota->descripcion }}</td>
                    <td>
                        {{ $cuota->fecha_vencimiento->format('d/m/Y') }}
                        @if($cuota->estado_cuota == 'Pendiente' && $cuota->fecha_vencimiento < now())
                            <span class="text-danger fw-bold">(Vencido)</span>
                        @endif
                    </td>
                    <td>S/ {{ number_format($cuota->monto_cuota, 2) }}</td>
                    <td>
                        @if($cuota->estado_cuota == 'Pagada')
                            <span class="badge bg-success">Pagada <i class="fas fa-check"></i></span>
                            <div class="small text-muted mt-1">
                                {{ $cuota->fecha_pago ? $cuota->fecha_pago->format('d/m/Y') : '' }}
                            </div>
                        @elseif($cuota->fecha_vencimiento < now())
                            <span class="badge bg-danger">Vencida</span>
                        @else
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        @endif
                    </td>
                    <td>
                        {{-- 1. PRIMERO: VERIFICAMOS SI YA ESTÁ PAGADA --}}
                        @if($cuota->estado_cuota == 'Pagada')
                            
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle"></i> PAGADA
                            </span>

                        @else
                            {{-- SI NO ESTÁ PAGADA, APLICAMOS TU LÓGICA DE VALIDACIÓN O PAGO --}}
                            
                            @php
                                // Buscamos si hay un reporte pendiente
                                $reporte = $cuota->reportes->where('estado', 'Pendiente')->first();
                            @endphp

                            @if($reporte)
                                {{-- CASO A: EL ALUMNO ENVIÓ VOUCHER (Validar) --}}
                                <button type="button" 
                                        class="btn btn-warning btn-sm fw-bold" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalValidar{{ $reporte->id }}">
                                    <i class="fas fa-search-dollar"></i> Validar Voucher
                                </button>

                                {{-- MODAL DE VALIDACIÓN --}}
                                <div class="modal fade" id="modalValidar{{ $reporte->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title text-dark">Validar Pago - Cuota {{ $cuota->numero_cuota }}</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <p><strong>Alumno:</strong> {{ $cliente->nombres ?? 'Cliente' }}</p>
                                                <p><strong>Monto Reportado:</strong> S/ {{ $reporte->monto }}</p>
                                                <p><strong>Operación:</strong> {{ $reporte->numero_operacion }} ({{ $reporte->metodo_pago }})</p>
                                                
                                                <div class="mb-3 border p-2">
                                                    <img src="{{ asset('storage/' . $reporte->comprobante_imagen) }}" class="img-fluid" alt="Comprobante">
                                                </div>

                                                <div class="d-flex justify-content-between gap-2">
                                                    {{-- RECHAZAR --}}
                                                    <form action="{{ route('pagos.rechazar', $reporte->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger">Rechazar</button>
                                                    </form>

                                                    {{-- APROBAR --}}
                                                    <form action="{{ route('pagos.aprobar', $reporte->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fas fa-check"></i> Aprobar y Marcar Pagado
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @else
                                {{-- CASO B: DEUDA PENDIENTE SIN VOUCHER (Pagar Manual) --}}
                                {{-- Este botón SOLO aparece si no está pagada y no hay reporte --}}
                                <a href="{{ route('cobranzas.edit', $cuota->id) }}" class="btn btn-success btn-sm fw-bold">
                                Pagar
                                </a>
                            @endif

                        @endif
                    </td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection