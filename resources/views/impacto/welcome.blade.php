<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INCADEV - Monitoreo de Impacto Social y Laboral</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --azul: #26BBFF;
            --morado: #201A2F;
            --negro: #000000;
            --gris-oscuro: #111115;
            --gris-claro: #848282;
            --verde-oscuro: #0F0F02;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--negro), var(--morado));
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* ---------- HEADER ---------- */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 10%;
            position: relative;
            z-index: 2;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo img {
            width: 55px;
            height: 55px;
            border-radius: 8px;
            object-fit: cover;
        }

        .logo span {
            font-size: 1.7rem;
            font-weight: bold;
            color: var(--azul);
        }

        nav {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Botones superiores */
        .btn-login,
        .btn-register {
            text-decoration: none;
            /* 🔹 elimina subrayado */
            display: inline-block;
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .btn-login {
            background-color: var(--azul);
            color: #000;
            border: none;
        }

        .btn-login:hover {
            background-color: #1ea4e0;
            transform: scale(1.05);
        }

        .btn-register {
            background-color: transparent;
            color: var(--azul);
            border: 2px solid var(--azul);
        }

        .btn-register:hover {
            background-color: var(--azul);
            color: #000;
        }

        /* Botón Regresar al Landing - AÑADIDO */
        .btn-landing {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .btn-landing:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(38, 187, 255, 0.2);
        }

        /* ---------- MAIN ---------- */
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            padding: 60px 10%;
            text-align: left;
            gap: 40px;
        }

        .content {
            flex: 1 1 500px;
            max-width: 550px;
            z-index: 2;
        }

        .content h1 {
            font-size: 2.6rem;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .content h1 span {
            color: var(--azul);
        }

        .content p {
            color: var(--gris-claro);
            line-height: 1.7;
            font-size: 1rem;
        }

        .imagen {
            flex: 1 1 400px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .imagen img {
            width: 95%;
            max-width: 550px;
            border-radius: 15px;
            box-shadow: none;
            animation: aparecer 1.2s ease-in-out;
        }

        @keyframes aparecer {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ---------- CARDS ---------- */
        .cards-section {
            padding: 70px 10%;
            text-align: center;
            background: rgba(255, 255, 255, 0.02);
        }

        .cards-section h2 {
            font-size: 2rem;
            margin-bottom: 40px;
            color: var(--azul);
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .card {
            background-color: var(--gris-oscuro);
            border-radius: 15px;
            padding: 25px 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
            transition: all 0.4s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(38, 187, 255, 0.15), rgba(0, 0, 0, 0.3));
            opacity: 0;
            transition: 0.4s ease;
        }

        .card:hover::before {
            opacity: 1;
        }

        .card:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 0 35px rgba(38, 187, 255, 0.3);
        }

        .card img {
            width: 70px;
            height: 70px;
            margin-bottom: 15px;
        }

        .card h3 {
            color: var(--azul);
            font-size: 1.3rem;
            margin-bottom: 10px;
        }

        .card p {
            color: var(--gris-claro);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        footer {
            text-align: center;
            padding: 25px;
            color: var(--gris-claro);
            font-size: 0.9rem;
            background-color: rgba(0, 0, 0, 0.3);
            border-top: none;
        }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 992px) {
            header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            nav {
                justify-content: center;
                flex-wrap: wrap;
            }

            main {
                flex-direction: column-reverse;
                text-align: center;
                padding: 40px 5%;
            }

            .content h1 {
                font-size: 2rem;
            }

            .imagen img {
                width: 80%;
                max-width: 400px;
            }

            .btn-login,
            .btn-register {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            header {
                padding: 20px;
                gap: 10px;
            }

            .logo {
                flex-direction: column;
                gap: 6px;
            }

            .logo img {
                width: 65px;
                height: 65px;
            }

            .logo span {
                font-size: 1.5rem;
            }

            nav {
                gap: 10px;
            }

            main {
                padding: 30px 6%;
            }

            .content h1 {
                font-size: 1.8rem;
                margin-bottom: 15px;
            }

            .content p {
                font-size: 0.9rem;
                line-height: 1.5;
            }

            .imagen img {
                width: 90%;
            }

            .cards-section h2 {
                font-size: 1.6rem;
            }

            .card h3 {
                font-size: 1.1rem;
            }

            .card p {
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body> <!-- HEADER -->
    <header>
        <div class="logo"> <img src="{{ asset('images/Imagen_incadev.png') }}" alt="Logo INCADEV"> <span>INCADEV</span> </div>
<nav>
    <a href="/" class="btn-landing">
                <i class="fas fa-home"></i>
                Inicio
    </a>
    <a href="{{ route('impacto.login') }}" class="btn-login">Iniciar Sesión</a>
    <a href="{{ route('impacto.register') }}" class="btn-register">Registrarse</a>
</nav>
    </header> <!-- MAIN -->
    <main>
        <div class="content">
            <h1>Monitoreo de <span>Impacto Social y Laboral</span></h1>
            <p> Plataforma para la medición de la empleabilidad de egresados y su contribución al desarrollo profesional. Un sistema que impulsa la mejora continua en la formación académica y el seguimiento laboral de los egresados. </p>
        </div>
        <div class="imagen"> <img src="{{ asset('images/monitoreo.png') }}" alt="Panel de Monitoreo"> </div>
    </main> <!-- CARDS -->
    <section class="cards-section">
        <h2>¿Qué ofrece INCADEV?</h2>
        <div class="cards">
            <div class="card"> <img src="https://img.icons8.com/fluency/96/graduation-cap.png" alt="Empleabilidad">
                <h3>Empleabilidad de Egresados</h3>
                <p>Analiza las oportunidades laborales alcanzadas por los egresados y su desempeño profesional.</p>
            </div>
            <div class="card"> <img src="https://img.icons8.com/fluency/96/collaboration.png" alt="Desarrollo">
                <h3>Desarrollo Profesional</h3>
                <p>Evalúa las competencias adquiridas y su impacto en la mejora continua de la educación.</p>
            </div>
            <div class="card"> <img src="https://img.icons8.com/fluency/96/earth-planet.png" alt="Impacto Social">
                <h3>Impacto Social</h3>
                <p>Monitorea la contribución de los egresados en el entorno laboral y su influencia social.</p>
            </div>
        </div>
    </section>
    <footer> © 2025 INCADEV — Instituto de Capacitación y Desarrollo Virtual </footer>
</body>

</html>