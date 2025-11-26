@extends('layouts.admin')

@section('content')
    <div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Registrar Pago</h3>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0 fw-bold"><i class="fas fa-cash-register"></i> Detalle de la Cuota</h4>
            </div>
            <div class="card-body">
                
                {{-- Resumen de lo que se va a pagar --}}
                <div class="alert alert-secondary">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Cliente:</strong> {{ $cuota->venta->cliente->nombre_completo }}<br>
                            <strong>Programa:</strong> {{ $cuota->venta->grupo->programa->nombre }}
                        </div>
                        <div class="col-md-6 text-end">
                            <h3 class="text-success fw-bold mb-0">S/ {{ number_format($cuota->monto_cuota, 2) }}</h3>
                            <small>Vence: {{ $cuota->fecha_vencimiento->format('d/m/Y') }}</small>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center fw-bold">
                        Concepto: {{ $cuota->descripcion }}
                    </div>
                </div>

                <form action="{{ route('cobranzas.update', $cuota->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h4 class="fw-bold mt-4">Datos del Pago</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha de Pago</label>
                                <input type="date" class="form-control" name="fecha_pago" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Monto Recibido (S/)</label>
                                <input type="number" step="0.01" class="form-control" name="monto_pagado" value="{{ $cuota->monto_cuota }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Método de Pago</label>
                                <select class="form-select" name="metodo_pago" required>
                                    <option value="" disabled selected>Selecciona...</option>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Transferencia BCP">Transferencia BCP</option>
                                    <option value="Transferencia BBVA">Transferencia BBVA</option>
                                    <option value="Yape">Yape</option>
                                    <option value="Plin">Plin</option>
                                    <option value="Tarjeta">Tarjeta de Crédito/Débito</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">N° Operación / Voucher (Opcional)</label>
                                <input type="text" class="form-control" name="transaccion_id" placeholder="Ej: 123456">
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <a href="{{ route('cobranzas.index') }}" class="btn btn-danger">Cancelar</a>
                        <button type="submit" class="btn btn-success fw-bold">
                            <i class="fas fa-check-circle"></i> Confirmar Pago
                        </button>
                    </div>
                </form>

            </div>
        </div>
        </div>
    </div>
    </div>
@endsection