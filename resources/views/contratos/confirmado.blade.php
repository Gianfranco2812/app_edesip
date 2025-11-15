<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato Confirmado</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <style> body { background-color: #f4f5f8; } .card { margin-top: 50px; } </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card text-center">
                    <div class="card-header bg-warning">
                        <h4 class="fw-bold text-dark mb-0">Atención</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ session('warning') }}</h5>
                        <p class="card-text">
                            Este contrato fue confirmado el 
                            {{ $contrato->fecha_confirmacion->format('d/m/Y \a \l\a\s H:i') }}h.
                        </p>
                        <a href="{{ route('login') }}" class="btn btn-primary">Ir al Portal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>