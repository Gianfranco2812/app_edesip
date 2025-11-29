@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">👋 Hola, {{ $cliente->nombre }}</h3>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            @forelse($misMatriculas as $venta)
            <div class="card mb-3">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold">{{ $venta->grupo->programa->nombre }}</h4>
                    <span class="badge bg-light text-primary fw-bold">{{ $venta->grupo->codigo_grupo }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><i class="fas fa-calendar-alt me-2"></i> Inicio: <strong>{{ $venta->grupo->fecha_inicio->format('d/m/Y') }}</strong></p>
                            <p class="mb-1"><i class="fas fa-clock me-2"></i> Horario: {{ $venta->grupo->horario_texto }}</p>
                            <p class="mb-1"><i class="fas fa-laptop me-2"></i> Modalidad: {{ $venta->grupo->modalidad }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            {{-- Botón Descargar Contrato --}}
                            @if($venta->contrato && $venta->contrato->estado == 'Confirmado')
                                <a href="{{ route('contratos.mostrar', $venta->contrato->token_acceso) }}" target="_blank" class="btn btn-outline-secondary btn-sm mb-2">
                                    <i class="fas fa-file-contract"></i> Ver Contrato
                                </a>
                            @endif
                            
                            {{-- Estado General --}}
                            @if($venta->estado == 'Cerrada')
                                <div class="alert alert-success py-1 px-2 mb-0 d-inline-block">
                                    <small>Matrícula Activa</small>
                                </div>
                            @else
                                <div class="alert alert-warning py-1 px-2 mb-0 d-inline-block">
                                    <small>En Proceso</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr>

                    {{-- TABLA DE PAGOS --}}
                    <h5 class="fw-bold text-muted mb-3">Mis Pagos</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Concepto</th>
                                    <th>Vence</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venta->cuotas as $cuota)
                                <tr>
                                    <td>{{ $cuota->descripcion }}</td>
                                    <td>{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</td>
                                    <td>S/ {{ number_format($cuota->monto_cuota, 2) }}</td>
                                    <td>
                                        @if($cuota->estado_cuota == 'Pagada')
                                            <span class="badge bg-success">Pagado</span>
                                        @elseif($cuota->fecha_vencimiento < now())
                                            <span class="badge bg-danger">Vencido</span>
                                        @else
                                            <span class="badge bg-secondary">Pendiente</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @empty
            <div class="card">
                <div class="card-body text-center p-5">
                    <h3>😕 No tienes cursos activos.</h3>
                    <p>Comunícate con un asesor para matricularte.</p>
                </div>
            </div>
            @endforelse
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="fw-bold">Mis Datos</h5>
                    <p class="mb-1"><strong>DNI:</strong> {{ $cliente->numero_documento }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $cliente->email }}</p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-link px-0">Editar contraseña / perfil</a>
                </div>
            </div>

            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="fw-bold"><i class="fas fa-headset"></i> ¿Necesitas Ayuda?</h5>
                    <p>Si tienes problemas con tus pagos o acceso al aula virtual, contáctanos.</p>
                    <p class="mb-0 fw-bold"><i class="fab fa-whatsapp"></i> +51 999 999 999</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection