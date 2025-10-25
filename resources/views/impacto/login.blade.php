<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INCADEV - Monitoreo de Impacto Social y Laboral</title>
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
            padding: 20px;
            overflow: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(38,187,255,0.2) 0%, transparent 70%);
            top: -200px;
            right: -200px;
            animation: float 8s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(38,187,255,0.15) 0%, transparent 70%);
            bottom: -150px;
            left: -150px;
            animation: float 10s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, 30px) scale(1.1); }
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

        .login-container {
            background: linear-gradient(145deg, #111115, #201A2F);
            border-radius: 24px;
            padding: 50px 45px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5), 0 0 0 1px rgba(38,187,255,0.1);
            text-align: center;
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
            width: 100px;
            height: 100px;
            margin: 0 auto 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s ease-in-out infinite;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 8px 20px rgba(38,187,255,0.3));
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        h1 {
            font-size: 1.8rem;
            margin-bottom: 35px;
            background: linear-gradient(135deg, #26BBFF, #ffffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 600;
        }

        .input-group {
            margin-bottom: 25px;
            position: relative;
        }

        .input-group svg {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            stroke: #848282;
            transition: stroke 0.3s ease;
            pointer-events: none;
        }

        .input-group input:focus + svg {
            stroke: #26BBFF;
        }

        .input-group input {
            width: 100%;
            padding: 15px 20px 15px 50px;
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

        .register-link {
            margin-top: 25px;
            color: #848282;
            font-size: 0.95rem;
        }

        .register-link a {
            color: #26BBFF;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: #ffffff;
        }

        @media (max-width: 480px) {
            .login-container { padding: 40px 30px; }
            h1 { font-size: 1.5rem; }
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

    <div class="login-container">
        <div class="logo">
            <img src="{{ asset('images/Imagen_incadev.png') }}" alt="INCADEV Logo">
        </div>
        
        <h1>Monitoreo de Impacto Social y Laboral</h1>

        @if (session('success'))
            <div style="background: rgba(16,185,129,0.1); border: 1px solid #10B981; border-radius: 8px; padding: 15px; margin-bottom: 20px; color: #10B981; font-size: 0.9rem; text-align: center;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="background: rgba(239,68,68,0.1); border: 1px solid #EF4444; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                <ul style="list-style: none; color: #EF4444; font-size: 0.9rem; margin: 0; padding: 0;">
                    @foreach ($errors->all() as $error)
                        <li style="margin-bottom: 5px;">• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('impacto.login.post') }}" method="POST">
            @csrf
            <div class="input-group">
                <input type="email" id="email" name="email" placeholder="Correo electrónico" required value="{{ old('email') }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
            </div>

            <div class="input-group">
                <input type="password" id="password" name="password" placeholder="Contraseña" required>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
            </div>

            <button type="submit" class="btn-submit">Iniciar sesión</button>
        </form>

        <div class="register-link">
            <p>¿No tienes cuenta? <a href="{{ route('impacto.register') }}">Regístrate</a></p>
        </div>
    </div>
</body>
</html>