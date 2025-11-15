<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Contrato</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.css') }}">
    <style>
        body { background-color: #f4f5f8; }
        .contract-container {
            max-width: 900px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 25px 0 rgba(0,0,0,.1);
        }
        .contract-header {
            padding: 2rem;
            border-bottom: 1px solid #eee;
        }
        .contract-body {
            padding: 2.5rem;
            max-height: 60vh;
            overflow-y: auto;
            border-bottom: 1px solid #eee;
            background: #fdfdfd;
        }
        .contract-footer {
            padding: 2rem;
            background: #f9f9f9;
        }
        .form-check-label {
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="contract-container">
            <div class="contract-header">
                <h2 class="fw-bold mb-1">Confirmación de Matrícula</h2>
                <p class="text-muted">Por favor, lee detenidamente los términos del contrato antes de aceptar.</p>
            </div>

            <div class="contract-body">
                {!! $contrato->contenido_generado !!}
            </div>

            <div class="contract-footer">
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                
                <form id="formConfirmacion" action="{{ route('contratos.confirmar', $contrato->token_acceso) }}" method="POST">
                    @csrf
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="" id="checkAcepto" required>
                        <label class="form-check-label fw-bold" for="checkAcepto">
                            He leído y estoy de acuerdo con todos los términos y condiciones del presente contrato.
                        </label>
                    </div>

                    <button type="submit" id="btnAceptar" class="btn btn-success btn-lg w-100" disabled>
                        Aceptar y Confirmar Matrícula
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Habilita el botón de aceptar solo cuando el checkbox está marcado
        const checkAcepto = document.getElementById('checkAcepto');
        const btnAceptar = document.getElementById('btnAceptar');
        const formConfirmacion = document.getElementById('formConfirmacion');

        checkAcepto.addEventListener('change', function() {
            btnAceptar.disabled = !this.checked;
        });

        // Doble confirmación al enviar
        formConfirmacion.addEventListener('submit', function(e) {
            if (!confirm('¿Estás seguro de que deseas confirmar este contrato? Esta acción es irreversible.')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>