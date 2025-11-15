<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Matrícula Exitosa!</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style> body { background-color: #f4f5f8; } .card { margin-top: 50px; } .icon-success { font-size: 5rem; color: #28a745; } </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card text-center p-4">
                    <div class="card-body">
                        <i class="fas fa-check-circle icon-success mb-3"></i>
                        <h2 class="fw-bold mb-3">¡Matrícula Exitosa!</h2>
                        <p class="card-text fs-5">
                            El contrato ha sido confirmado y el plan de pagos ha sido generado.
                        </p>
                        <p class="text-muted">
                            Se ha enviado una copia del contrato al correo electrónico del cliente.
                            (El Asesor ya puede cerrar esta ventana).
                        </p>
                        
                        {{-- Opcional: Redirigir al Asesor al dashboard de ventas --}}
                        <a href="{{ route('ventas.index') }}" class="btn btn-primary mt-3">
                            Volver al Listado de Ventas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>