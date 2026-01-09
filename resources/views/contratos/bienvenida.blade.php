<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido a EDESIP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-success-subtle">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="card shadow border-0 p-5">
                    
                    {{-- Icono Check --}}
                    <div class="mb-4 text-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                    </div>

                    <h2 class="fw-bold text-success mb-3">¡Contrato Firmado!</h2>
                    <p class="text-muted mb-4">Hemos generado tu acceso al Portal del Estudiante.</p>

                    <div class="alert alert-primary text-start">
                        <h5 class="fw-bold border-bottom pb-2 mb-3">Tus Credenciales de Acceso:</h5>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Usuario:</span>
                            {{-- Mostramos el username (que es el DNI) --}}
                            <strong class="fs-5">{{ $usuario->username }}</strong>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Contraseña:</span>
                            @if($esNuevo)
                                <strong class="fs-5">{{ $password }}</strong>
                            @else
                                <span class="text-muted fst-italic">(Tu contraseña actual)</span>
                            @endif
                        </div>
                        
                        <small class="text-primary d-block mt-3 text-center border-top pt-2">
                            <i class="fas fa-info-circle"></i> Usa tu número de documento como usuario.
                        </small>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg fw-bold">IR AL PORTAL</a>
                        <a href="{{ asset('storage/' . $ruta_pdf) }}" download class="btn btn-outline-secondary">Descargar Contrato (PDF)</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>