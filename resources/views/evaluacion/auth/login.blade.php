<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Evaluación Docente</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-custom-dark { background-color: #201A2F; }
        .bg-custom-blue { background-color: #26BBFF; }
        .text-custom-blue { color: #26BBFF; }
    </style>
</head>
<body class="bg-custom-dark min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Logo y Título -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-custom-blue rounded-2xl mb-4">
                <i class="fas fa-chalkboard-teacher text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Iniciar Sesión</h1>
            <p class="text-gray-400">Accede a tu cuenta para continuar</p>
        </div>

        <!-- Formulario de Login -->
        <div class="bg-[#111115] p-8 rounded-2xl border border-gray-800">
            <form method="POST" action="{{ route('evaluacion.login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-white mb-2 font-medium">
                        <i class="fas fa-envelope text-custom-blue mr-2"></i>Correo Electrónico
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           required 
                           autofocus
                           class="w-full px-4 py-3 bg-[#201A2F] border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-custom-blue transition-all"
                           placeholder="tu@email.com">
                    @error('email')
                        <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Contraseña -->
                <div class="mb-6">
                    <label for="password" class="block text-white mb-2 font-medium">
                        <i class="fas fa-lock text-custom-blue mr-2"></i>Contraseña
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           class="w-full px-4 py-3 bg-[#201A2F] border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-custom-blue transition-all"
                           placeholder="••••••••">
                    @error('password')
                        <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Recordar sesión -->
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="remember" 
                               class="rounded bg-[#201A2F] border-gray-700 text-custom-blue focus:ring-custom-blue">
                        <span class="ml-2 text-gray-400 text-sm">Recordar sesión</span>
                    </label>
                </div>

                <!-- Botón de Login -->
                <button type="submit" 
                        class="w-full bg-custom-blue hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition-all duration-300">
                    <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                </button>
            </form>
        </div>
    </div>
</body>
</html>