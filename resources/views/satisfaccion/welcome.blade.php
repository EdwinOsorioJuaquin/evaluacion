<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta de Satisfacción Estudiantil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        deepSky: '#26BBFF',
                        darkPurple: '#201A2F',
                        night: '#111115',
                        smokyBlack: '#0F0F0F',
                        grayish: '#848282',
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-deepSky via-darkPurple to-night flex items-center justify-center">

    <div class="text-center px-6">
        <!-- Título -->
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-8 drop-shadow-lg">
            Encuesta de Satisfacción Estudiantil
        </h1>

        <!-- Contenedor de botones -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <!-- Botón de Identificarse -->
            <a href="{{ route('satisfaccion.login') }}"
               class="group relative inline-flex items-center justify-center px-10 py-4 bg-white text-darkPurple font-semibold rounded-full text-lg shadow-lg transition-transform transform hover:-translate-y-1 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-white/40">
                <span class="absolute inset-0 rounded-full opacity-0 group-hover:opacity-10 transition-opacity bg-gradient-to-r from-deepSky to-green-300 blur-md"></span>
                <svg class="w-5 h-5 mr-3 -ml-1 text-deepSky" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" />
                </svg>
                <span class="relative z-10">Identificarse</span>
            </a>

            <!-- Botón para volver al landing -->
            <a href="{{ url('/') }}"
               class="group relative inline-flex items-center justify-center px-10 py-4 bg-transparent border-2 border-white text-white font-semibold rounded-full text-lg shadow-md transition-all hover:bg-white hover:text-darkPurple focus:outline-none focus:ring-4 focus:ring-white/40">
                <svg class="w-5 h-5 mr-3 -ml-1 text-white group-hover:text-darkPurple transition" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span>Volver al inicio</span>
            </a>
        </div>
    </div>

</body>
</html>
