<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso - EDESIP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            overflow: hidden; /* Evita scroll en escritorio */
        }

        .login-container {
            height: 100vh;
        }

        /* --- LADO IZQUIERDO: TEXTO Y BRANDING --- */
        .brand-side {
            /* Fondo con imagen y capa azul encima */
            background: linear-gradient(rgba(0, 37, 105, 0.9), rgba(0, 86, 179, 0.9)), 
                        url('https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem;
            text-align: left; /* Alineado a la izquierda o center si prefieres */
        }

        .brand-title {
            font-size: 5rem; /* Texto Gigante */
            font-weight: 800;
            letter-spacing: -2px;
            line-height: 1;
            margin-bottom: 1rem;
            text-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        .brand-slogan {
            font-size: 1.2rem;
            font-weight: 300; /* Letra fina para elegancia */
            letter-spacing: 4px; /* Letras separadas */
            text-transform: uppercase;
            border-top: 1px solid rgba(255,255,255,0.3);
            padding-top: 1rem;
            display: inline-block;
        }

        /* --- LADO DERECHO: LOGO Y FORMULARIO --- */
        .form-side {
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }

        .form-content {
            width: 100%;
            max-width: 400px;
            text-align: center; /* Para centrar el logo y título */
        }

        /* Estilo para el Logo en la parte derecha */
        .login-logo-img {
            max-height: 120px; /* Tamaño controlado del logo */
            width: auto;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }

        /* Inputs Flotantes */
        .form-floating > .form-control {
            border: none;
            border-bottom: 2px solid #eee;
            border-radius: 0;
            padding-left: 0;
            box-shadow: none !important;
        }

        .form-floating > .form-control:focus {
            border-color: #0d47a1;
        }

        .form-floating > label {
            padding-left: 0;
            opacity: 0.6;
        }

        /* Botón */
        .btn-elegant {
            background-color: #0d47a1;
            color: white;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            border: none;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-elegant:hover {
            background-color: #002171;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(13, 71, 161, 0.3);
        }

        /* Ajustes para Móvil */
        @media (max-width: 768px) {
            .brand-side { display: none; } /* Oculta el lado azul en móvil */
            body { overflow: auto; }
            .login-container { height: auto; min-height: 100vh; }
            .form-side { padding-top: 3rem; padding-bottom: 3rem; }
        }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0 login-container">
        
        <div class="col-md-6 col-lg-7 brand-side">
            <div>
                <h1 class="brand-title">EDESIP</h1>
                <p class="brand-slogan">
                    Escuela de Desarrollo e Innovación Profesional
                </p>
            </div>
        </div>

        <div class="col-md-6 col-lg-5 form-side">
            <div class="form-content">
                
                <img src="{{ asset('img/logo_p.png') }}" alt="Logo EDESIP" class="login-logo-img">

                <h2 class="fw-bold mb-2 text-dark">Bienvenido</h2>
                <p class="text-muted mb-4 small">Ingresa a tu cuenta para continuar</p>

                <form method="POST" action="{{ route('login') }}" class="text-start">
                    @csrf

                    <div class="form-floating mb-4">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                id="email" name="email" placeholder="name@example.com" 
                                value="{{ old('email') }}" required autofocus>
                        <label for="email">Correo Electrónico</label>
                        @error('email')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-floating mb-4">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                id="password" name="password" placeholder="Password" required>
                        <label for="password">Contraseña</label>
                        @error('password')
                            <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label small text-muted" for="remember">
                                Recordarme
                            </label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-decoration-none small text-secondary">
                                ¿Olvidaste contraseña?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn-elegant">
                        INGRESAR
                    </button>
                </form>
                
                <div class="text-center mt-5">
                    <p class="small text-muted">&copy; {{ date('Y') }} EDESIP</p>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
