@extends('layouts.admin')

@section('content')
<div class="container-fluid p-4">
    
    <h3 class="fw-bold text-dark mb-4"><i class="fas fa-wallet me-2"></i> Mis Pagos y Documentos</h3>

    @forelse($ventas as $venta)
        
        @php
            $hoy = \Carbon\Carbon::now()->startOfDay();
            
            // 1. Ordenamos cuotas (Ene -> Dic)
            $cuotas = $venta->cuotas->sortBy('fecha_vencimiento');
            
            $cuotaActiva = null; 
            $deudaTotalCalculada = 0; 

            foreach($cuotas as $cuota) {
                $estadoLimpio = strtoupper(trim($cuota->estado_cuota));

                $monto = floatval($cuota->monto_cuota);
                $pagado = floatval($cuota->monto_pagado);
                $saldoReal = $monto - $pagado;

                if (in_array($estadoLimpio, ['PAGADO', 'PAGADA', 'COMPLETADO']) || $saldoReal < 0.50) {
                    continue; 
                }

                $deudaTotalCalculada += $saldoReal;

                if (!$cuotaActiva) {
                    $cuotaActiva = $cuota;
                }
            }

            // DEFINIR MENSAJES DEL SEMÁFORO
            $estadoColor = 'success'; 
            $estadoIcono = 'check-circle';
            $estadoMensaje = "¡Excelente! Estás al día con tus pagos.";

            if ($cuotaActiva) {
                $vence = \Carbon\Carbon::parse($cuotaActiva->fecha_vencimiento)->startOfDay();
                $diff = $hoy->diffInDays($vence, false);
                $desc = $cuotaActiva->concepto ?? $cuotaActiva->descripcion ?? 'Cuota';

                if ($diff < 0) {
                    // ROJO: Atrasado
                    $dias = abs($diff);
                    $estadoColor = 'danger';
                    $estadoIcono = 'times-circle';
                    $estadoMensaje = "Pago de <strong>$desc</strong> retrasado por $dias días";
                } elseif ($diff <= 3) {
                    // AMARILLO: Próximo
                    $estadoColor = 'warning';
                    $estadoIcono = 'exclamation-circle';
                    $estadoMensaje = ($diff == 0) ? "Tu <strong>$desc</strong> vence HOY" : "Atención: Tu <strong>$desc</strong> vence en $diff días";
                } else {
                    // AZUL: Futuro
                    $estadoColor = 'primary';
                    $estadoIcono = 'info-circle';
                    $estadoMensaje = "Próximo vencimiento: <strong>$desc</strong> el " . $vence->format('d/m/Y');
                }
            }
        @endphp

        <div class="card shadow-sm border-0 mb-4">
            
            {{-- CABECERA --}}
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold text-primary mb-1">
                            {{ $venta->grupo->programa->nombre ?? 'Programa Académico' }}
                        </h5>
                        <small class="text-muted">
                            Grupo: {{ $venta->grupo->codigo_grupo ?? '---' }}
                        </small>
                    </div>
                    
                    {{-- BOTÓN CONTRATO --}}
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-4">
                    
                    {{-- LADO IZQUIERDO: CONTADOR DE DEUDA + AVISOS --}}
                    <div class="col-md-7 mb-3">
                        <div class="p-3 rounded border h-100 d-flex flex-column justify-content-center bg-light">
                            
                            {{-- 💰 CONTADOR DE DEUDA (NUEVO) --}}
                            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                                <div>
                                    <h6 class="text-secondary fw-bold text-uppercase mb-0">Deuda Total</h6>
                                    <small class="text-muted">Monto pendiente acumulado</small>
                                </div>
                                <div class="text-end">
                                    @if($deudaTotalCalculada > 0)
                                        <h2 class="fw-bold text-dark mb-0">S/ {{ number_format($deudaTotalCalculada, 2) }}</h2>
                                    @else
                                        <h2 class="fw-bold text-success mb-0">S/ 0.00</h2>
                                    @endif
                                </div>
                            </div>

                            {{-- AVISO DINÁMICO --}}
                            <div class="alert alert-{{ $estadoColor }} d-flex align-items-center mb-0 p-3 shadow-sm border-0" role="alert">
                                <i class="fas fa-{{ $estadoIcono }} fa-2x me-3"></i>
                                <div>
                                    <div class="fs-6 lh-sm">{!! $estadoMensaje !!}</div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- LADO DERECHO: BOTÓN PAGAR --}}
                    <div class="col-md-5 mb-3 d-flex align-items-stretch">
                        <button class="btn btn-success w-100 fw-bold shadow-lg" data-bs-toggle="modal" data-bs-target="#modalPago{{ $venta->id }}">
                            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                <i class="fas fa-qrcode fa-3x mb-2"></i>
                                <span class="fs-4">PAGAR AHORA</span>
                                <small class="fw-light opacity-75">Yape / Plin / Transferencia</small>
                            </div>
                        </button>
                    </div>

                </div>

                {{-- TABLA DE CUOTAS --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Vencimiento</th>
                                <th>Descripción</th>
                                <th>Monto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cuotas as $cuota)
                                @php
                                    $est = strtoupper(trim($cuota->estado_cuota));
                                    
                                    // 1. Verificamos si tiene reporte pendiente usando la relación que creamos
                                    $reportePendiente = $cuota->reportes->where('estado', 'Pendiente')->first();

                                    // 2. Definimos el Estado Visual y Color
                                    if (in_array($est, ['PAGADO', 'PAGADA'])) {
                                        $badgeColor = 'success';
                                        $textoEstado = 'Pagado';
                                    } 
                                    elseif ($reportePendiente) {
                                        // AQUÍ ESTÁ LA MAGIA: Si hay reporte, mostramos esto
                                        $badgeColor = 'info text-dark'; // Azulito claro
                                        $textoEstado = 'Validando...';
                                    }
                                    elseif ($cuota->fecha_vencimiento < now()) {
                                        $badgeColor = 'danger';
                                        $textoEstado = 'Vencido';
                                    } 
                                    else {
                                        $badgeColor = 'secondary';
                                        $textoEstado = 'Pendiente';
                                    }
                                @endphp
                                <tr>
                                    <td class="fw-bold">{{ $loop->iteration }}</td>
                                    <td>{{ \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('d/m/Y') }}</td>
                                    <td>{{ $cuota->descripcion }}</td>
                                    <td>S/ {{ number_format($cuota->monto_cuota, 2) }}</td>
                                    <td><span class="badge bg-{{ $badgeColor }}">{{ $textoEstado }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalPago{{ $venta->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    
                    {{-- CABECERA DEL MODAL CON TÍTULO DINÁMICO --}}
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-money-bill-wave me-2"></i> 
                            Pagar el programa: {{ $venta->grupo->programa->nombre }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            
                            {{-- COLUMNA IZQUIERDA: QRs y CUENTAS --}}
                            <div class="col-md-5 border-end text-center bg-light p-3 rounded">
    
                            @php
                                $billeteras = $metodosPago->where('tipo', 'Billetera Digital');
                            @endphp

                            @if($billeteras->count() > 0)
                                <h6 class="fw-bold text-muted mb-3">Escanea para pagar:</h6>
                                
                                {{-- A. LOS QRs (Uno al lado del otro) --}}
                                <div class="d-flex justify-content-center gap-3 mb-3 flex-wrap">
                                    @foreach($billeteras as $wallet)
                                        <div class="text-center">
                                            <div class="bg-white p-2 border rounded shadow-sm mb-1" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center;">
                                                @if($wallet->qr_imagen)
                                                    <img src="{{ asset('storage/' . $wallet->qr_imagen) }}" 
                                                        style="max-width: 100%; max-height: 100%; object-fit: contain; cursor: pointer;" 
                                                        alt="{{ $wallet->nombre_banco }}"
                                                        onclick="window.open(this.src)">
                                                @else
                                                    <i class="fas fa-qrcode fa-3x text-warning"></i>
                                                @endif
                                            </div>
                                            <span class="badge bg-warning text-dark">{{ $wallet->nombre_banco }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- B. LOS NÚMEROS (Listados uno por uno) --}}
                                <div class="mt-3">
                                    @foreach($billeteras as $wallet)
                                        <div class="mb-2">
                                            <h4 class="fw-bold text-success mb-0">
                                                <i class="fas fa-mobile-alt"></i> {{ $wallet->numero_cuenta }}
                                            </h4>
                                            <small class="text-muted">{{ $wallet->nombre_banco }} - {{ $wallet->titular }}</small>
                                        </div>
                                    @endforeach
                                </div>

                            @endif

                            <hr>
                            
                            @php
                                $bancos = $metodosPago->where('tipo', 'Cuenta Bancaria');
                            @endphp

                            @if($bancos->count() > 0)
                                <div class="text-start small">
                                    <h6 class="fw-bold text-secondary mb-2"><i class="fas fa-university"></i> Transferencias:</h6>
                                    
                                    @foreach($bancos as $banco)
                                        <div class="mb-2 border-bottom pb-2">
                                            <p class="mb-0 text-primary fw-bold">{{ $banco->nombre_banco }}</p>
                                            <p class="mb-0"><strong>Nro:</strong> {{ $banco->numero_cuenta }}</p>
                                            @if($banco->cci)
                                                <p class="mb-0 text-muted"><strong>CCI:</strong> {{ $banco->cci }}</p>
                                            @endif
                                            <p class="mb-0 text-muted fst-italic" style="font-size: 0.85em;">{{ $banco->titular }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted small">No hay cuentas bancarias registradas.</p>
                            @endif

                        </div>

                            {{-- COLUMNA DERECHA: FORMULARIO DE REPORTE --}}
                            <div class="col-md-7">
                                <form action="{{ route('portal.pagos.reportar') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    {{-- Enviamos el ID de la venta oculto --}}
                                    <input type="hidden" name="venta_id" value="{{ $venta->id }}">

                                    {{-- SELECT DE CUOTAS PENDIENTES --}}
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">¿Qué cuota estás pagando?</label>
                                        {{-- 1. Agregamos el ID 'select_cuota' --}}
                                        <select name="cuota_id" id="select_cuota" class="form-select border-success" required>
                                            <option value="" selected disabled>Selecciona una opción...</option>
                                            @foreach($cuotas as $c)
                                                @php 
                                                    $estC = strtoupper(trim($c->estado_cuota));
                                                    // Solo mostramos las que NO están pagadas
                                                    if(!in_array($estC, ['PAGADO', 'PAGADA', 'COMPLETADO'])) {
                                                @endphp
                                                    {{-- 2. Agregamos data-monto con el precio --}}
                                                    <option value="{{ $c->id }}" data-monto="{{ $c->monto_cuota }}">
                                                        {{ $c->descripcion }} (Vence: {{ \Carbon\Carbon::parse($c->fecha_vencimiento)->format('d/m') }}) - S/ {{ number_format($c->monto_cuota, 2) }}
                                                    </option>
                                                @php } @endphp
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label class="form-label small fw-bold">Monto (S/)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white">S/</span>
                                                <input type="number" step="0.01" name="monto_pagado" id="txt_monto" class="form-control" placeholder="00" required>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label class="form-label small fw-bold">Método</label>
                                            <select name="metodo_pago" class="form-select" required>
                                                <option value="Yape">Yape</option>
                                                <option value="Plin">Plin</option>
                                                <option value="BCP">BCP</option>
                                                <option value="BBVA">BBVA</option>
                                                <option value="Agente">Agente</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">N° Operación</label>
                                        <input type="text" name="numero_operacion" class="form-control" placeholder="Ej: 1234567" required>
                                        <div class="form-text x-small">Código que aparece en tu comprobante.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Foto del Comprobante</label>
                                        <input type="file" name="comprobante" class="form-control" accept="image/*" required>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-success fw-bold">
                                            <i class="fas fa-paper-plane me-2"></i> ENVIAR REPORTE
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center py-5">
            <h4>No tienes cursos registrados.</h4>
        </div>
    @endforelse

</div>
{{-- MODAL DE REPORTE DE PAGO --}}


{{-- ALERTAS DE ÉXITO O ERROR (Poner al inicio del content si no las tienes) --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectCuota = document.getElementById('select_cuota');
        const txtMonto = document.getElementById('txt_monto');

        if(selectCuota && txtMonto) {
            selectCuota.addEventListener('change', function() {
                // Obtener la opción seleccionada
                const selectedOption = this.options[this.selectedIndex];
                
                // Leer el atributo data-monto
                const monto = selectedOption.getAttribute('data-monto');

                // Llenar el input
                if(monto) {
                    txtMonto.value = parseFloat(monto).toFixed(2);
                } else {
                    txtMonto.value = '';
                }
            });
        }
    });
</script>
@endsection