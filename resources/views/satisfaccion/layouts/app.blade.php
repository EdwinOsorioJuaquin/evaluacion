{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Satisfacción Estudiantil')</title>
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
                        grayish: '#B3B3B3',
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-night font-sans text-grayish antialiased">

    {{-- NAVBAR --}}
    <nav class="bg-darkPurple/90 backdrop-blur-sm text-white shadow-lg border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-6 py-3 flex justify-between items-center">
            {{-- LOGO --}}
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/incadev-logo.png') }}"
                    alt="INCADEV"
                    class="h-20 object-contain drop-shadow-lg border border-gray-700 rounded-lg" />
            </div>

            {{-- BOTÓN CERRAR SESIÓN --}}
            @auth
                <form action="{{ route('satisfaccion.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="bg-red-500 hover:bg-red-600 px-5 py-2 rounded-full font-semibold text-white shadow transition-all duration-300 hover:scale-105">
                        Cerrar sesión
                    </button>
                </form>
            @endauth
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-6 mt-6">
        @yield('content')
    </main>

        <footer class="mt-10 py-6 border-t border-gray-800 text-center text-sm text-gray-500">
        © {{ date('Y') }} INCADEV — Módulo de Satisfacción Estudiantil
    </footer>

</body>
</html>
