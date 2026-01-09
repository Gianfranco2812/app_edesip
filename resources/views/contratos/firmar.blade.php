<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firmar Contrato | EDESIP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow border-0">
                    <div class="card-header bg-dark text-white text-center py-4">
                        <h4 class="mb-0">Contrato de Servicios Educativos</h4>
                    </div>
                    <div class="card-body p-4">
                        
                        {{-- 1. Resumen Alumno --}}
                        <div class="alert alert-secondary">
                            <strong>Alumno:</strong> {{ $contrato->venta->cliente->nombre }} {{ $contrato->venta->cliente->apellido }}<br>
                            <strong>DNI:</strong> {{ $contrato->venta->cliente->numero_documento }}<br>
                            <strong>Programa:</strong> {{ $contrato->venta->grupo->programa->nombre }}
                        </div>

                        {{-- 2. El Contrato (Viene de tu módulo de plantillas) --}}
                        <div class="border p-4 mb-4 bg-white" style="height: 400px; overflow-y: auto;">
                            {!! $contrato->contenido_generado !!}
                        </div>

                        {{-- 3. Formulario --}}
                        <form action="{{ route('contratos.procesar', $contrato->token_acceso) }}" method="POST">
                            @csrf
                            <div class="form-check mb-4 p-3 bg-warning-subtle border border-warning rounded">
                                <input class="form-check-input" type="checkbox" id="acepto" required>
                                <label class="form-check-label fw-bold" for="acepto">
                                    He leído y acepto los términos y condiciones.
                                </label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg fw-bold">
                                    FIRMAR Y CREAR ACCESO
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>