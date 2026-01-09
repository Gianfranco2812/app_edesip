@extends('layouts.admin')

@section('content')
<div class="container-fluid p-4">

    <div class="row">
        {{-- COLUMNA IZQUIERDA: VISUALIZADOR DEL CONTRATO --}}
        <div class="col-lg-8">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📄 Vista Previa del Contrato</h5>
                    <span class="badge bg-warning text-dark">Pendiente de Firma</span>
                </div>
                <div class="card-body bg-secondary bg-opacity-10">
                    
                    {{-- Papel Virtual --}}
                    <div class="bg-white p-5 mx-auto shadow-sm" style="max-width: 800px; min-height: 800px; overflow-y: auto;">
                        {{-- Cabecera del papel --}}
                        <div class="text-center mb-4 border-bottom pb-3">
                            <h3 class="fw-bold">CONTRATO DE SERVICIOS EDUCATIVOS</h3>
                            <p class="text-muted">EDESIP S.A.C.</p>
                        </div>

                        {{-- CONTENIDO DEL CONTRATO (HTML) --}}
                        <div class="contenido-contrato text-justify">
                            {!! $venta->contrato->contenido_generado !!}
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: ACCIONES --}}
        <div class="col-lg-4">
            <div class="card shadow border-0 mb-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold text-primary mb-3">Acciones de Matrícula</h4>
                    <p class="text-muted small">Cliente: <strong>{{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}</strong></p>
                    
                    <hr>

                    {{-- OPCIÓN 1: COMPARTIR (WHATSAPP / COPIAR) --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">OPCIÓN A: Enviar al Alumno (Remoto)</label>
                        
                        {{-- Botón WhatsApp --}}
                        <a href="{{ $linkWhatsApp }}" target="_blank" class="btn btn-success w-100 mb-2 fw-bold py-2">
                            <i class="fab fa-whatsapp fa-lg me-2"></i> Enviar por WhatsApp
                        </a>

                        {{-- Input Copiar --}}
                        <div class="input-group">
                            <input type="text" id="linkInput" class="form-control bg-light" value="{{ $linkFirma }}" readonly>
                            <button class="btn btn-outline-secondary" onclick="copiarLink()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <hr>

                    {{-- OPCIÓN 2: FIRMAR AHORA (PRESENCIAL) --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">OPCIÓN B: Alumno Presencial</label>
                        <p class="small text-muted">Si el alumno está presente, haz clic abajo para que firme en este dispositivo.</p>
                        
                        {{-- Este botón abre el link público en una pestaña nueva --}}
                        <a href="{{ $linkFirma }}" target="_blank" class="btn btn-primary w-100 fw-bold py-3 shadow">
                            <i class="fas fa-pen-nib me-2"></i> ABRIR PARA FIRMAR
                        </a>
                    </div>

                </div>
            </div>

            {{-- Botón para salir --}}
            <div class="text-center">
                <a href="{{ route('ventas.index') }}" class="text-muted text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Volver al listado de ventas
                </a>
            </div>
        </div>
    </div>

</div>

<script>
    function copiarLink() {
        var copyText = document.getElementById("linkInput");
        copyText.select();
        navigator.clipboard.writeText(copyText.value);
        // Puedes usar una librería de notificaciones aquí
        alert("Link copiado: " + copyText.value);
    }
</script>
@endsection