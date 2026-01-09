@extends('layouts.admin')

@section('content')
<style>
    /* Estilo para el Círculo del Semáforo */
    .status-dot {
        height: 15px;
        width: 15px;
        background-color: #bbb;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 5px rgba(0,0,0,0.2);
    }
    .dot-verde { background-color: #28a745; box-shadow: 0 0 8px #28a745; }
    .dot-amarillo { background-color: #ffc107; box-shadow: 0 0 8px #ffc107; }
    .dot-rojo { background-color: #dc3545; box-shadow: 0 0 8px #dc3545; }
</style>

<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-3">
        <div>
            <h2 class="fw-bold text-dark mb-1">Hola, {{ explode(' ', $cliente->nombre_completo)[0] }} 👋</h2>
            <p class="text-muted mb-0">Bienvenido a tu  Portal de Pagos</p>
        </div>
        
        <div class="d-flex align-items-center">
            <span class="me-2 fw-bold text-secondary" style="font-size: 1.1rem;">
                {{ $mensajeSemaforo }}
            </span>
            
            @php
                $dotClass = 'dot-verde';
                if($estadoSemaforo == 'amarillo') $dotClass = 'dot-amarillo';
                if($estadoSemaforo == 'rojo') $dotClass = 'dot-rojo';
            @endphp
            
            <span class="status-dot {{ $dotClass }}" title="Estado de tu cuenta"></span>
        </div>
    </div>

    <div class="row">
        @forelse($ventas as $venta)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border-0 hover-effect">
                    
                    {{-- CABECERA: NOMBRE DEL PROGRAMA --}}
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h4 class="card-title fw-bold text-primary mb-0">
                            {{ $venta->grupo->programa->nombre }}
                        </h4>
                    </div>

                    <div class="card-body">
                        {{-- Detalles del Grupo --}}
                        <div class="mb-3">
                            <p class="text-muted mb-1">
                                <i class="fas fa-layer-group me-2"></i> 
                                Grupo: <strong>{{ $venta->grupo->codigo_grupo }}</strong>
                            </p>
                            <p class="text-muted mb-1">
                                <i class="fas fa-laptop me-2"></i> 
                                Modalidad: {{ $venta->grupo->modalidad }}
                            </p>
                            <p class="text-muted">
                                <i class="fas fa-calendar-alt me-2"></i> 
                                Inicio: {{ $venta->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top-0 pb-4">
                        <div class="d-grid">
                            <a href="{{ route('portal.pagos') }}" class="btn btn-outline-primary rounded-pill">
                                Ver Pagos y Contrato <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border text-center">
                    No tienes cursos activos actualmente.
                </div>
            </div>
        @endforelse
    </div>

</div>

<style>
    .hover-effect { transition: transform 0.2s; }
    .hover-effect:hover { transform: translateY(-3px); }
</style>
{{-- BOTÓN FLOTANTE DE WHATSAPP --}}
<a href="https://wa.me/51924828177?text=Hola,%20necesito%20ayuda%20con%20mi%20portal%20de%20alumno" 
    target="_blank" 
    class="btn-whatsapp shadow-lg">
    <i class="fab fa-whatsapp"></i> ¿Ayuda?
</a>

<style>
    .btn-whatsapp {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background-color: #25d366;
        color: white;
        border-radius: 50px;
        padding: 12px 24px;
        font-weight: bold;
        font-size: 16px;
        text-decoration: none;
        z-index: 1000;
        transition: transform 0.3s;
    }
    .btn-whatsapp:hover {
        background-color: #1ebe57;
        color: white;
        transform: scale(1.05);
    }
</style>
@endsection