@extends('evaluacion.layouts.app')

@section('title', 'Dashboard Administrador')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">Panel de Administración</h1>
        <p class="text-gray-400 mt-2">Bienvenido al sistema de evaluaciones docentes</p>
    </div>

    <!-- Cards de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-[#111115] p-6 rounded-xl border border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Sesiones Activas</p>
                    <p class="text-2xl font-bold text-white mt-1">
                        {{ \App\Models\EvaluationSession::where('status', 'active')->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-play-circle text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-[#111115] p-6 rounded-xl border border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Preguntas</p>
                    <p class="text-2xl font-bold text-white mt-1">
                        {{ \App\Models\EvaluationQuestion::count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-question-circle text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-[#111115] p-6 rounded-xl border border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Estudiantes</p>
                    <p class="text-2xl font-bold text-white mt-1">
                        {{ \App\Models\Student::count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-[#111115] p-6 rounded-xl border border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Instructores</p>
                    <p class="text-2xl font-bold text-white mt-1">
                        {{ \App\Models\Instructor::count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-orange-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Gestión de Sesiones -->
        <div class="bg-[#111115] p-6 rounded-xl border border-gray-800 hover:border-custom-blue transition-all duration-300">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-white text-xl font-bold mb-2">Sesiones de Evaluación</h3>
                    <p class="text-gray-400">Configura períodos de evaluación para los estudiantes</p>
                </div>
                <div class="w-12 h-12 bg-custom-blue rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-white text-xl"></i>
                </div>
            </div>
            <a href="{{ route('evaluacion.admin.sessions.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-custom-blue text-white rounded-lg hover:bg-blue-600 transition-colors">
                <i class="fas fa-cog mr-2"></i>
                Gestionar Sesiones
            </a>
        </div>

        <!-- Reportes y Resultados -->
        <div class="bg-[#111115] p-6 rounded-xl border border-gray-800 hover:border-green-500 transition-all duration-300">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-white text-xl font-bold mb-2">Reportes y Resultados</h3>
                    <p class="text-gray-400">Consulta los resultados de las evaluaciones realizadas</p>
                </div>
                <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-bar text-green-400 text-xl"></i>
                </div>
            </div>
            <a href="{{ route('evaluacion.admin.reports') }}" 
               class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                <i class="fas fa-chart-line mr-2"></i>
                Ver Reportes
            </a>
        </div>
    </div>

    <!-- Sesiones Activas Recientes -->
    <div class="bg-[#111115] rounded-xl border border-gray-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-800">
            <h2 class="text-xl font-bold text-white">Sesiones Activas Recientes</h2>
        </div>
        
        @php
            $activeSessions = \App\Models\EvaluationSession::withCount('questions')
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        @endphp

        @if($activeSessions->count() > 0)
            <div class="divide-y divide-gray-800">
                @foreach($activeSessions as $session)
                <div class="px-6 py-4 hover:bg-gray-800/30 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-white font-semibold">{{ $session->title }}</h3>
                            <p class="text-gray-400 text-sm mt-1">
                                {{ $session->start_date->format('d/m/Y') }} - {{ $session->end_date->format('d/m/Y') }}
                            </p>
                            <div class="flex items-center space-x-4 mt-2 text-sm text-gray-400">
                                <span class="flex items-center">
                                    <i class="fas fa-question-circle mr-1"></i>
                                    {{ $session->questions_count }} preguntas
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $session->academic_period }}
                                </span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('evaluacion.admin.sessions.questions.index', $session->id) }}" 
                               class="inline-flex items-center px-3 py-1 bg-custom-blue text-white rounded-lg text-sm hover:bg-blue-600 transition-colors">
                                <i class="fas fa-edit mr-1"></i>
                                Preguntas
                            </a>
                            <a href="{{ route('evaluacion.admin.session.results', $session->id) }}" 
                               class="inline-flex items-center px-3 py-1 bg-green-500 text-white rounded-lg text-sm hover:bg-green-600 transition-colors">
                                <i class="fas fa-chart-bar mr-1"></i>
                                Resultados
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="px-6 py-4 bg-gray-800/30 border-t border-gray-800">
                <a href="{{ route('evaluacion.admin.sessions.index') }}" 
                   class="text-custom-blue hover:text-blue-400 transition-colors flex items-center">
                    Ver todas las sesiones
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        @else
            <div class="px-6 py-8 text-center">
                <div class="text-gray-400 text-4xl mb-3">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h3 class="text-white font-semibold mb-2">No hay sesiones activas</h3>
                <p class="text-gray-400 mb-4">Comienza creando tu primera sesión de evaluación.</p>
                <a href="{{ route('evaluacion.admin.sessions.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-custom-blue text-white rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Crear Sesión
                </a>
            </div>
        @endif
    </div>

    <!-- Accesos Directos -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('evaluacion.admin.sessions.create') }}" 
           class="bg-[#111115] p-4 rounded-xl border border-gray-800 hover:border-custom-blue transition-all duration-300 group">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-custom-blue rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-600 transition-colors">
                    <i class="fas fa-plus text-white"></i>
                </div>
                <div>
                    <h4 class="text-white font-semibold">Nueva Sesión</h4>
                    <p class="text-gray-400 text-sm">Crear evaluación</p>
                </div>
            </div>
        </a>

        <a href="{{ route('evaluacion.admin.reports') }}" 
           class="bg-[#111115] p-4 rounded-xl border border-gray-800 hover:border-green-500 transition-all duration-300 group">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-500 transition-colors">
                    <i class="fas fa-chart-bar text-green-400 group-hover:text-white"></i>
                </div>
                <div>
                    <h4 class="text-white font-semibold">Reportes</h4>
                    <p class="text-gray-400 text-sm">Ver estadísticas</p>
                </div>
            </div>
        </a>

        <a href="{{ route('evaluacion.admin.sessions.index') }}" 
           class="bg-[#111115] p-4 rounded-xl border border-gray-800 hover:border-purple-500 transition-all duration-300 group">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center mr-3 group-hover:bg-purple-500 transition-colors">
                    <i class="fas fa-list text-purple-400 group-hover:text-white"></i>
                </div>
                <div>
                    <h4 class="text-white font-semibold">Todas las Sesiones</h4>
                    <p class="text-gray-400 text-sm">Gestionar evaluaciones</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection