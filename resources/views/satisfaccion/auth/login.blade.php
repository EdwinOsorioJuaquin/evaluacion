{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Satisfacción Estudiantil</title>
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
<body class="min-h-screen bg-gradient-to-br from-night to-darkPurple flex items-center justify-center font-sans antialiased">

    <div class="bg-smokyBlack/60 backdrop-blur-lg shadow-2xl rounded-3xl p-10 w-full max-w-md text-white border border-gray-800">
        {{-- LOGO --}}
        <div class="flex justify-center mb-6">
            <img src="{{ asset('storage/incadev/incadev_logo_navbar_400h.png') }}"
                 alt="INCADEV"
                 class="h-20 object-contain drop-shadow-lg">
        </div>

        

        {{-- ERRORES --}}
        @if($errors->any())
            <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORMULARIO --}}
        <form action="{{ route('satisfaccion.login.post') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-grayish font-medium mb-2">Correo electrónico</label>
                <input type="email" name="email" id="email" required
                    class="w-full px-4 py-3 bg-darkPurple text-white border border-gray-700 rounded-xl 
                    focus:outline-none focus:ring-2 focus:ring-deepSky focus:border-deepSky placeholder-grayish transition duration-300"
                    placeholder="ejemplo@correo.com">
            </div>

            <div>
                <label for="password" class="block text-grayish font-medium mb-2">Contraseña</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-3 bg-darkPurple text-white border border-gray-700 rounded-xl 
                    focus:outline-none focus:ring-2 focus:ring-deepSky focus:border-deepSky placeholder-grayish transition duration-300"
                    placeholder="••••••••">
            </div>

            <button type="submit"
                class="w-full bg-deepSky hover:bg-sky-400 text-night font-bold py-3 px-4 rounded-xl shadow-lg 
                transition duration-300 transform hover:-translate-y-0.5 hover:shadow-xl">
                Entrar
            </button>
        </form>
    </div>

</body>
</html>



