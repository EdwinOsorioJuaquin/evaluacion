@extends('evaluacion.layouts.app')

@section('title', 'Sesiones de Evaluación')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
           <a href="{{ route('evaluacion.dashboard') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Dashboard
            </a>
        <div>
            <h1 class="text-3xl font-bold text-white">Sesiones de Evaluación</h1>
            <p class="text-gray-400 mt-2">Configura períodos de evaluación para los estudiantes</p>
        </div>
        <button id="createSessionBtn" 
                class="bg-custom-blue hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors flex items-center shadow-lg shadow-blue-500/25">
            <i class="fas fa-plus-circle mr-2"></i> Nueva Sesión
        </button>
    </div>

    <!-- Lista de Sesiones -->
    <div class="space-y-4">
        @forelse($sessions as $session)
        <div class="session-card bg-[#1f2937] rounded-xl border border-gray-800 p-6 hover:border-custom-blue transition-all duration-300">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xl font-semibold text-white">{{ $session->title }}</h3>
                        <span class="status-badge px-3 py-1 rounded-full text-xs font-medium
                            @if($session->status == 'active') bg-green-500/20 text-green-400 border border-green-500/30
                            @elseif($session->status == 'upcoming') bg-blue-500/20 text-blue-400 border border-blue-500/30
                            @elseif($session->status == 'completed') bg-yellow-500/20 text-yellow-400 border border-yellow-500/30
                            @else bg-gray-500/20 text-gray-400 border border-gray-500/30
                            @endif">
                            {{ ucfirst($session->status) }}
                        </span>
                    </div>
                    
                    @if($session->description)
                    <p class="text-gray-400 mb-4">{{ $session->description }}</p>
                    @endif
                    
                    <div class="flex items-center space-x-6 text-sm text-gray-400">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-custom-blue"></i>
                            <span>{{ \Carbon\Carbon::parse($session->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($session->end_date)->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-question-circle mr-2 text-custom-blue"></i>
                            <span>{{ $session->questions_count }} preguntas</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="flex justify-between items-center pt-4 mt-4 border-t border-gray-700">
                <div class="text-sm text-gray-400">
                    Creado: {{ \Carbon\Carbon::parse($session->created_at)->format('d M Y') }}
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('evaluacion.admin.sessions.questions.index', $session->id) }}" 
                       class="bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 px-4 py-2 rounded-lg transition-colors flex items-center text-sm">
                        <i class="fas fa-question-circle mr-2"></i> Gestionar
                    </a>
                    <button class="edit-btn bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-400 px-4 py-2 rounded-lg transition-colors flex items-center text-sm"
                            data-session-id="{{ $session->id }}">
                        <i class="fas fa-edit mr-2"></i> Editar
                    </button>
                    <button class="delete-btn bg-red-500/20 hover:bg-red-500/30 text-red-400 px-4 py-2 rounded-lg transition-colors flex items-center text-sm"
                            data-session-id="{{ $session->id }}"
                            data-session-title="{{ $session->title }}">
                        <i class="fas fa-trash mr-2"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-12 text-center">
            <i class="fas fa-calendar-times text-4xl text-gray-500 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-400 mb-2">No hay sesiones creadas</h3>
            <p class="text-gray-500 mb-4">Comienza creando tu primera sesión de evaluación</p>
            <button id="createFirstSessionBtn" class="bg-custom-blue hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center">
                <i class="fas fa-plus-circle mr-2"></i> Crear Primera Sesión
            </button>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Crear Sesión -->
<div id="sessionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-[#1f2937] rounded-xl border border-gray-800 p-6 w-full max-w-2xl mx-4">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-white">Crear Nueva Sesión</h3>
            <button id="closeModal" class="text-gray-400 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="sessionForm" action="{{ route('evaluacion.admin.sessions.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        <i class="fas fa-heading mr-2 text-custom-blue"></i>
                        Título de la Sesión *
                    </label>
                    <input type="text" name="title" required
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-custom-blue focus:ring-2 focus:ring-custom-blue/20"
                           placeholder="Ej: Evaluación Docente - Abril 2023">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        <i class="fas fa-align-left mr-2 text-custom-blue"></i>
                        Descripción
                    </label>
                    <textarea name="description" rows="3"
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-custom-blue focus:ring-2 focus:ring-custom-blue/20"
                              placeholder="Descripción de la sesión de evaluación..."></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-play-circle mr-2 text-custom-blue"></i>
                            Fecha Inicio *
                        </label>
                        <input type="date" name="start_date" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-custom-blue focus:ring-2 focus:ring-custom-blue/20">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-stop-circle mr-2 text-custom-blue"></i>
                            Fecha Fin *
                        </label>
                        <input type="date" name="end_date" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-custom-blue focus:ring-2 focus:ring-custom-blue/20">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        <i class="fas fa-toggle-on mr-2 text-custom-blue"></i>
                        Estado
                    </label>
                    <select name="status"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-custom-blue focus:ring-2 focus:ring-custom-blue/20">
                        <option value="upcoming">Próxima</option>
                        <option value="active">Activa</option>
                        <option value="inactive">Inactiva</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-700">
                <button type="button" id="cancelModal" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors">
                    Cancelar
                </button>
                <button type="submit" 
                        class="bg-custom-blue hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors flex items-center">
                    <i class="fas fa-save mr-2"></i> Guardar Sesión
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('sessionModal');
    const createBtn = document.getElementById('createSessionBtn');
    const closeBtn = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelModal');
    const form = document.getElementById('sessionForm');

    // Abrir modal para crear
    createBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
    });

    // Cerrar modal
    closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
    cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));

    // Crear primera sesión
    document.getElementById('createFirstSessionBtn')?.addEventListener('click', () => {
        modal.classList.remove('hidden');
    });

    // Eliminar sesión
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const sessionId = this.getAttribute('data-session-id');
            const sessionTitle = this.getAttribute('data-session-title');
            
            if(confirm(`¿Estás seguro de que quieres eliminar la sesión "${sessionTitle}"?`)) {
                // Aquí enviarías la solicitud de eliminación
                fetch(`/admin/sessions/${sessionId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar la sesión');
                });
            }
        });
    });
});
</script>

<style>
.session-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.session-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
}

.status-badge {
    transition: all 0.3s ease;
}
</style>
@endsection