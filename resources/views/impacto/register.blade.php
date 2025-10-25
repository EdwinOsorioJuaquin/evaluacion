<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INCADEV - Registro</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #201A2F 0%, #111115 50%, #0F0F02 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(38,187,255,0.15) 0%, transparent 70%);
            top: -250px;
            right: -250px;
            animation: float 8s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(38,187,255,0.1) 0%, transparent 70%);
            bottom: -200px;
            left: -200px;
            animation: float 10s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(40px, 40px); }
        }

        .btn-back {
            position: fixed;
            top: 25px;
            left: 25px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: rgba(17,17,21,0.8);
            border: 2px solid rgba(38,187,255,0.3);
            border-radius: 10px;
            color: #26BBFF;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .btn-back:hover {
            background: rgba(38,187,255,0.1);
            border-color: #26BBFF;
            transform: translateX(-5px);
        }

        .btn-back svg {
            width: 20px;
            height: 20px;
        }

        .register-container {
            background: linear-gradient(145deg, #111115, #201A2F);
            border-radius: 24px;
            padding: 45px 50px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5), 0 0 0 1px rgba(38,187,255,0.1);
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo {
            width: 90px;
            height: 90px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 8px 20px rgba(38,187,255,0.3));
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        h1 {
            text-align: center;
            font-size: 1.9rem;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #26BBFF, #ffffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 600;
        }

        p.subtitle {
            text-align: center;
            color: #848282;
            font-size: 0.9rem;
            margin-bottom: 35px;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #848282;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .input-group input {
            width: 100%;
            padding: 14px 18px;
            border-radius: 12px;
            border: 2px solid rgba(132,130,130,0.2);
            background: rgba(17,17,21,0.6);
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            border-color: #26BBFF;
            outline: none;
            background: rgba(17,17,21,0.9);
            box-shadow: 0 0 0 4px rgba(38,187,255,0.1);
        }

        .input-group input::placeholder {
            color: #848282;
        }

        .name-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #26BBFF, #1a8fd4);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(38,187,255,0.3);
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(38,187,255,0.5);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: #848282;
            font-size: 0.85rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(132,130,130,0.2);
        }

        .divider::before {
            margin-right: 15px;
        }

        .divider::after {
            margin-left: 15px;
        }

        .login-link {
            text-align: center;
            color: #848282;
            font-size: 0.95rem;
        }

        .login-link a {
            color: #26BBFF;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #ffffff;
        }

        @media (max-width: 580px) {
            .register-container { 
                padding: 35px 30px; 
            }
            h1 { 
                font-size: 1.6rem; 
            }
            .name-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .btn-back {
                top: 15px;
                left: 15px;
                padding: 8px 14px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <a href="{{ route('impacto.welcome') }}" class="btn-back">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Volver al Inicio
    </a>

    <div class="register-container">
        <div class="logo">
            <img src="{{ asset('images/Imagen_incadev.png') }}" alt="INCADEV Logo">
        </div>
        
        <h1>Crear una Cuenta</h1>
        <p class="subtitle">Completa tus datos para registrarte</p>

       <form action="{{ route('impacto.register.post') }}" method="POST">
        @csrf
    <div class="name-grid">
        <div class="input-group">
            <label>Nombre</label>
            <input type="text" name="first_name" placeholder="Tu nombre" required value="{{ old('first_name') }}">
        </div>

        <div class="input-group">
            <label>Apellido</label>
            <input type="text" name="last_name" placeholder="Tu apellido" required value="{{ old('last_name') }}">
        </div>
    </div>

    <div class="input-group">
        <label>DNI</label>
        <input type="text" name="dni" placeholder="Número de DNI" required value="{{ old('dni') }}">
    </div>

    <div class="input-group">
        <label>Correo Electrónico</label>
        <input type="email" name="email" placeholder="tu@email.com" required value="{{ old('email') }}">
    </div>

    <div class="input-group">
        <label>Contraseña</label>
        <input type="password" name="password" placeholder="Mínimo 8 caracteres" required>
    </div>

    <div class="input-group">
        <label>Confirmar Contraseña</label>
        <input type="password" name="password_confirmation" placeholder="Repite tu contraseña" required>
    </div>

    @if ($errors->any())
        <div style="background: rgba(239,68,68,0.1); border: 1px solid #EF4444; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
            <ul style="list-style: none; color: #EF4444; font-size: 0.9rem;">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <button type="submit" class="btn-submit">Registrarse</button>
</form>

        <div class="divider">o</div>

        <div class="login-link">
            <p>¿Ya tienes una cuenta? <a href="{{ route('impacto.login') }}">Inicia sesión aquí</a></p>
        </div>
    </div>
</body>
</html>