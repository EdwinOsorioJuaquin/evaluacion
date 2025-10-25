<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Módulo de Evaluación Docente')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-custom-dark { background-color: #201A2F; }
        .bg-custom-blue { background-color: #26BBFF; }
        .text-custom-blue { color: #26BBFF; }
        .border-custom-blue { border-color: #26BBFF; }
    </style>
</head>
<body class="bg-custom-dark min-h-screen">
    <!-- Header/Navigation -->
    <nav class="bg-[#111115] border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-custom-blue rounded-lg flex items-center justify-center">
                            <i class="fas fa-chalkboard-teacher text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-white font-bold text-xl">Evaluación Docente</h1>
                        <p class="text-gray-400 text-sm">Panel de Administración</p>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <span class="text-white">
                        <i class="fas fa-user-circle mr-2"></i>
                        {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                    </span>
                    <span class="px-2 py-1 bg-custom-blue text-white text-xs rounded-full">
                        {{ Auth::user()->isAdmin() ? 'Administrador' : (Auth::user()->isInstructor() ? 'Instructor' : 'Estudiante') }}
                    </span>
                    
                    <!-- Logout Form -->
                    <form method="POST" action="{{ route('evaluacion.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>