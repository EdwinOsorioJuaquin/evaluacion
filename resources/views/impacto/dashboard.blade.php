<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- ⬅️ AGREGAR ESTO -->
    <title>INCADEV - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #201A2F 0%, #111115 100%);
            color: #fff;
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            background: #0F0F02;
            padding: 20px;
            box-shadow: 4px 0 10px rgba(0,0,0,0.3);
            z-index: 100;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(132,130,130,0.2);
        }

        .logo-img {
            width: 40px;
            height: 40px;
            background: #26BBFF;
            border-radius: 8px;
        }

        .logo h2 {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .menu-item {
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #848282;
        }

        .menu-item:hover, .menu-item.active {
            background: rgba(38,187,255,0.1);
            color: #26BBFF;
        }

        .menu-section {
            margin-top: 30px;
            font-size: 0.75rem;
            color: #848282;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            padding-left: 15px;
        }

        .main-content {
            margin-left: 260px;
            padding: 30px 40px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 600;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(17,17,21,0.6);
            padding: 8px 15px;
            border-radius: 10px;
            cursor: pointer;
            position: relative;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background: #26BBFF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .dropdown-menu {
            position: absolute;
            top: 55px;
            right: 0;
            background: rgba(17,17,21,0.95);
            border-radius: 10px;
            padding: 10px;
            min-width: 200px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            display: none;
            z-index: 1000;
            border: 1px solid rgba(132,130,130,0.2);
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            padding: 12px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            margin-bottom: 5px;
        }

        .dropdown-item:hover {
            background: rgba(38,187,255,0.1);
            color: #26BBFF;
        }

        .dropdown-item.logout {
            color: #EF4444;
        }

        .dropdown-item.logout:hover {
            background: rgba(239,68,68,0.1);
        }

        .section {
            display: none;
        }

        .section.active {
            display: block;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            padding: 25px;
            border-radius: 16px;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.blue {
            background: linear-gradient(135deg, #26BBFF, #1a8fd4);
        }

        .stat-card.yellow {
            background: linear-gradient(135deg, #FFC107, #FFB300);
        }

        .stat-card.green {
            background: linear-gradient(135deg, #10B981, #059669);
        }

        .stat-card.purple {
            background: linear-gradient(135deg, #8B5CF6, #7C3AED);
        }

        .stat-card h3 {
            font-size: 0.9rem;
            font-weight: 400;
            opacity: 0.9;
            margin-bottom: 10px;
        }

        .stat-card p {
            font-size: 2.5rem;
            font-weight: 700;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .chart-container {
            background: rgba(17,17,21,0.6);
            border-radius: 16px;
            padding: 25px;
            backdrop-filter: blur(10px);
        }

        .chart-container h2 {
            margin-bottom: 20px;
            font-size: 1.2rem;
        }

        .card-container {
            background: rgba(17,17,21,0.6);
            border-radius: 16px;
            padding: 30px;
            backdrop-filter: blur(10px);
            margin-bottom: 25px;
        }

        .card-container h2 {
            margin-bottom: 25px;
            font-size: 1.4rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: #26BBFF;
            color: #fff;
        }

        .btn-success {
            background: #10B981;
            color: #fff;
        }

        .btn-primary:hover, .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(38,187,255,0.4);
        }

        .btn-secondary {
            background: rgba(132,130,130,0.3);
            color: #fff;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #848282;
            font-weight: 600;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 2px solid rgba(132,130,130,0.2);
            background: rgba(17,17,21,0.6);
            color: #fff;
            font-size: 1rem;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px;
            color: #848282;
            font-weight: 600;
            font-size: 0.9rem;
            border-bottom: 1px solid rgba(132,130,130,0.2);
        }

        td {
            padding: 18px 15px;
            border-bottom: 1px solid rgba(132,130,130,0.1);
        }

        tr:hover {
            background: rgba(38,187,255,0.05);
        }

        .action-btns {
            display: flex;
            gap: 8px;
        }

        .question-item {
            background: rgba(32,26,47,0.4);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #26BBFF;
        }

        .question-item h4 {
            margin-bottom: 15px;
            color: #26BBFF;
            font-size: 1.1rem;
        }

        .options-list {
            margin-top: 15px;
        }

        .option-item {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }

        .option-item input {
            flex: 1;
        }

        .btn-small {
            padding: 8px 15px;
            font-size: 0.85rem;
        }

        .btn-danger {
            background: #EF4444;
            color: #fff;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <div class="logo-img"></div>
            <h2>INCADEV</h2>
        </div>

        @php
            $userRole = is_array(Auth::user()->role) 
                ? (Auth::user()->role[0] ?? 'student') 
                : trim(Auth::user()->role, '"');
        @endphp

        @if($userRole === 'admin')
            <!-- MENÚ PARA ADMIN -->
            <div class="menu-item active" onclick="showSection('dashboard')">
                <span>📊</span>
                <span>Dashboard </span>
            </div>
            <div class="menu-item" onclick="showSection('crear-encuesta')">
                <span>➕</span>
                <span>Crear Encuesta</span>
            </div>
            <div class="menu-item" onclick="showSection('asignar-encuesta')">
                <span>📋</span>
                <span>Asignar Encuesta</span>
            </div>
            <div class="menu-item" onclick="showSection('gestionar-encuestas')">
                <span>📝</span>
                <span>Gestionar Encuestas</span>
            </div>

            <div class="menu-section">Reportes</div>
            <div class="menu-item" onclick="showSection('reportes')">
                <span>📈</span>
                <span>Ver Reportes</span>
            </div>

            <div class="menu-section">Administración</div>
            <div class="menu-item" onclick="showSection('configuracion')">
                <span>⚙️</span>
                <span>Configuración</span>
            </div>

        @else
            <!-- MENÚ PARA STUDENT -->
            <div class="menu-item active" onclick="showSection('dashboard')">
                <span>📋</span>
                <span>Mis Encuestas</span>
            </div>
            <div class="menu-item" onclick="showSection('historial')">
                <span>📊</span>
                <span>Historial</span>
            </div>

            <div class="menu-section">Mi Cuenta</div>
            <div class="menu-item" onclick="showSection('configuracion')">
                <span>⚙️</span>
                <span>Configuración</span>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h1 id="pageTitle">Dashboard</h1>
            <div class="user-profile" onclick="toggleDropdown()">
                <div class="user-avatar">{{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}</div>
                <span>{{ Auth::user()->full_name }}</span>
                <span>▼</span>
                <div class="dropdown-menu" id="userDropdown">
                    <div class="dropdown-item logout" onclick="event.stopPropagation(); document.getElementById('logout-form').submit();">
                        <span>🚪</span>
                        <span>Cerrar Sesión</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de logout oculto -->
        <form id="logout-form" action="{{ route('impacto.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>

        <!-- Dashboard Section -->
        <div id="dashboard" class="section active">
            @if($userRole === 'admin')
                <!-- DASHBOARD PARA ADMIN -->
            <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
                <div class="stat-card blue">
                    <h3>Total Usuarios</h3>
                    <p>{{ $totalUsers }}</p>
                    <small>Registrados en el sistema</small>
                </div>
                <div class="stat-card yellow">
                    <h3>Total Graduados</h3>
                    <p>{{ $totalGraduates }}</p>
                    <small>Estudiantes graduados</small>
                </div>
                <div class="stat-card purple">
                    <h3>Encuestas Completadas</h3>
                    <p>{{ $totalSurveys }}</p>
                    <small>Respuestas recibidas</small>
                </div>
            </div>

                <div class="charts-grid">
                    <div class="chart-container">
                        <h2>Últimas Encuestas Completadas</h2>
                        <div style="max-height: 300px; overflow-y: auto;">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Graduado</th>
                                        <th>Programa</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentSurveys as $survey)
                                    <tr>
                                        <td>{{ $survey->graduate->user->full_name }}</td>
                                        <td>{{ $survey->graduate->program->name }}</td>
                                        <td>{{ $survey->completed_at ? $survey->completed_at->format('d/m/Y') : 'N/A' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" style="text-align: center; color: #848282;">
                                            No hay encuestas completadas aún
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="chart-container">
                        <h2>Programas Populares</h2>
                        <div style="max-height: 300px; overflow-y: auto;">
                            @foreach($topPrograms as $program)
                            <div style="background: rgba(32,26,47,0.4); padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                                <h4 style="color: #26BBFF; margin-bottom: 5px;">{{ $program->name }}</h4>
                                <p style="color: #848282; font-size: 0.9rem;">
                                    {{ $program->graduates_count }} graduados
                                </p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

@else
    <!-- DASHBOARD PARA STUDENT -->
    <div class="card-container">
        <h2>Bienvenido, {{ Auth::user()->first_name }}! 👋</h2>
        <p style="color: #848282; font-size: 1rem; margin-bottom: 20px;">
            Aquí puedes ver un resumen de tu actividad y completar las encuestas pendientes.
        </p>

        <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr);">
            <div class="stat-card blue">
                <h3>Programas Completados</h3>
                <p>{{ $graduates->count() }}</p>
            </div>
            <div class="stat-card green">
                <h3>Encuestas Completadas</h3>
                <p>{{ $completedSurveys }}</p>
            </div>
            <div class="stat-card yellow">
                <h3>En Progreso</h3>
                <p>{{ $inProgressSurveys }}</p>
            </div>
            <div class="stat-card purple">
                <h3>Pendientes</h3>
                <p>{{ $pendingSurveys }}</p>
            </div>
        </div>
    </div>

    @if($graduates->count() > 0)
    <div class="card-container">
        <h2 style="display: flex; justify-content: space-between; align-items: center;">
            Mis Encuestas
            <div style="display: flex; gap: 10px;">
                <button class="btn btn-small" onclick="filterSurveys('all')" id="filter-all" style="background: #26BBFF;">Todas</button>
                <button class="btn btn-small btn-secondary" onclick="filterSurveys('pending')" id="filter-pending">Pendientes</button>
                <button class="btn btn-small btn-secondary" onclick="filterSurveys('in_progress')" id="filter-in_progress">En Progreso</button>
                <button class="btn btn-small btn-secondary" onclick="filterSurveys('completed')" id="filter-completed">Completadas</button>
            </div>
        </h2>

        <div style="display: grid; gap: 15px;" id="surveysListContainer">
            @forelse($assignedSurveys as $assignedSurvey)
                <div class="survey-card" data-status="{{ $assignedSurvey->status }}" style="background: rgba(32,26,47,0.4); padding: 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; border-left: 4px solid {{ $assignedSurvey->status === 'completed' ? '#10B981' : ($assignedSurvey->status === 'in_progress' ? '#FFC107' : '#26BBFF') }};">
                    <div style="flex: 1;">
                        <h3 style="color: #26BBFF; margin-bottom: 8px; font-size: 1.1rem;">
                            {{ $assignedSurvey->survey->name }}
                        </h3>
                        <p style="color: #848282; font-size: 0.9rem; margin-bottom: 5px;">
                            📚 Programa: <strong>{{ $assignedSurvey->graduate->program->name }}</strong>
                        </p>
                        <p style="color: #848282; font-size: 0.9rem; margin-bottom: 5px;">
                            📅 Asignada: {{ $assignedSurvey->assigned_date->format('d/m/Y') }}
                        </p>
                        @if($assignedSurvey->status === 'completed' && $assignedSurvey->completed_at)
                        <p style="color: #848282; font-size: 0.9rem;">
                            ✅ Completada: {{ $assignedSurvey->completed_at->format('d/m/Y H:i') }}
                        </p>
                        @endif
                        @if($assignedSurvey->status === 'in_progress' && $assignedSurvey->started_at)
                        <p style="color: #848282; font-size: 0.9rem;">
                            🕐 Iniciada: {{ $assignedSurvey->started_at->format('d/m/Y H:i') }}
                        </p>
                        @endif
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px; align-items: flex-end;">
                        @if($assignedSurvey->status === 'completed')
                            <span style="color: #10B981; font-weight: 600; padding: 8px 16px; background: rgba(16,185,129,0.1); border-radius: 8px;">
                                ✓ Completada
                            </span>
                        @elseif($assignedSurvey->status === 'in_progress')
                            <span style="color: #FFC107; font-weight: 600; padding: 8px 16px; background: rgba(255,193,7,0.1); border-radius: 8px;">
                                ⏳ En Progreso
                            </span>
                            <a href="{{ route('impacto.survey.show', $assignedSurvey->id) }}" class="btn btn-primary">
                                ▶️ Continuar Encuesta
                            </a>
                        @else
                            <span style="color: #26BBFF; font-weight: 600; padding: 8px 16px; background: rgba(38,187,255,0.1); border-radius: 8px;">
                                📋 Pendiente
                            </span>
                            <a href="{{ route('impacto.survey.show', $assignedSurvey->id) }}" class="btn btn-primary">
                                ▶️ Comenzar Encuesta
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 40px; color: #848282;">
                    No tienes encuestas asignadas aún.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Mis Programas -->
    <div class="card-container">
        <h2>Mis Programas Completados</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
            @foreach($graduates as $graduate)
                <div style="background: rgba(32,26,47,0.4); padding: 20px; border-radius: 12px; border-left: 4px solid #26BBFF;">
                    <h3 style="color: #26BBFF; margin-bottom: 10px;">
                        {{ $graduate->program->name }}
                    </h3>
                    <p style="color: #848282; font-size: 0.9rem; margin-bottom: 5px;">
                        🎓 Graduado: {{ $graduate->graduation_date->format('d/m/Y') }}
                    </p>
                    @if($graduate->final_note)
                    <p style="color: #848282; font-size: 0.9rem; margin-bottom: 5px;">
                        📊 Nota Final: <strong style="color: #10B981;">{{ $graduate->final_note }}</strong>
                    </p>
                    @endif
                    @if($graduate->employability)
                    <p style="color: #848282; font-size: 0.9rem;">
                        💼 Estado: <strong style="color: #10B981;">{{ ucfirst($graduate->employability) }}</strong>
                    </p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    @else
    <div class="card-container">
        <h2>Mis Encuestas</h2>
        <p style="color: #848282; text-align: center; padding: 40px;">
            No tienes programas completados. Una vez que completes un programa, aparecerán las encuestas aquí.
        </p>
    </div>
    @endif
@endif
        </div>

<!-- Crear Encuesta Section (FUNCIONAL) -->
<div id="crear-encuesta" class="section">
    <div class="card-container">
        <h2>Crear Nueva Encuesta</h2>
        <p style="color: #848282; margin-bottom: 20px;">
            Crea una nueva encuesta para realizar seguimiento a los graduados.
        </p>

        @if(session('success'))
            <div style="background: rgba(16,185,129,0.1); border-left: 4px solid #10B981; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <p style="color: #10B981; margin: 0;">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div style="background: rgba(239,68,68,0.1); border-left: 4px solid #EF4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <p style="color: #EF4444; margin: 0;">{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div style="background: rgba(239,68,68,0.1); border-left: 4px solid #EF4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <ul style="color: #EF4444; margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('impacto.surveys.store') }}" method="POST" id="surveyForm">
            @csrf

            <div class="form-group">
                <label>Nombre de la Encuesta *</label>
                <input type="text" name="survey_name" placeholder="Ej: Encuesta de Satisfacción Laboral" value="{{ old('survey_name') }}" required>
            </div>

            <div class="form-group">
                <label>Descripción</label>
                <textarea name="survey_description" placeholder="Describe el propósito de esta encuesta...">{{ old('survey_description') }}</textarea>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}> Encuesta activa
                </label>
            </div>

            <h3 style="color: #26BBFF; margin: 30px 0 20px 0;">Preguntas de la Encuesta</h3>

            <div id="questionsContainer">
                <!-- Las preguntas se agregarán dinámicamente aquí -->
                <div class="question-item" data-question-index="0">
                    <h4>Pregunta #1</h4>
                    <div class="form-group">
                        <label>Texto de la Pregunta *</label>
                        <input type="text" name="questions[0][question_text]" placeholder="Escribe la pregunta aquí..." required>
                    </div>

                    <div class="form-group">
                        <label>Tipo de Pregunta *</label>
                        <select name="questions[0][question_type]" onchange="handleQuestionTypeChange(this, 0)" required>
                            <option value="text">Texto libre</option>
                            <option value="number">Número</option>
                            <option value="option">Opción única</option>
                            <option value="date">Fecha</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="questions[0][is_required]" value="1"> Pregunta obligatoria
                        </label>
                    </div>

                    <!-- Contenedor de opciones (solo visible si el tipo es "option") -->
                    <div class="options-container" id="options-container-0" style="display: none;">
                        <h5 style="color: #26BBFF; margin: 15px 0 10px 0;">Opciones de Respuesta</h5>
                        <div class="options-list" id="options-list-0">
                            <div class="option-item">
                                <input type="text" name="questions[0][options][]" placeholder="Opción 1">
                                <button type="button" class="btn btn-small btn-danger" onclick="removeOption(this)">🗑️</button>
                            </div>
                            <div class="option-item">
                                <input type="text" name="questions[0][options][]" placeholder="Opción 2">
                                <button type="button" class="btn btn-small btn-danger" onclick="removeOption(this)">🗑️</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-small btn-secondary" onclick="addOption(0)">➕ Agregar Opción</button>
                    </div>

                    <button type="button" class="btn btn-small btn-danger" onclick="removeQuestion(this)" style="margin-top: 15px;">🗑️ Eliminar Pregunta</button>
                </div>
            </div>

            <button type="button" class="btn btn-primary" onclick="addQuestion()" style="margin-top: 20px;">
                ➕ Agregar Pregunta
            </button>

            <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 30px;">
                💾 Guardar Encuesta
            </button>
        </form>
    </div>
</div>

        <!-- Asignar Encuesta Section (Solo Vista) -->
        <!-- Asignar Encuesta Section (FUNCIONAL) -->
        <div id="asignar-encuesta" class="section">
            <div class="card-container">
                <h2>Asignar Encuesta a Graduados</h2>
                <p style="color: #848282; margin-bottom: 20px;">
                    Selecciona una encuesta y asígnala a graduados específicos.
                </p>

                @if(session('success'))
                    <div style="background: rgba(16,185,129,0.1); border-left: 4px solid #10B981; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <p style="color: #10B981; margin: 0;">{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div style="background: rgba(239,68,68,0.1); border-left: 4px solid #EF4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <p style="color: #EF4444; margin: 0;">{{ session('error') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div style="background: rgba(239,68,68,0.1); border-left: 4px solid #EF4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <ul style="color: #EF4444; margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('impacto.surveys.assign') }}" method="POST" id="assignSurveyForm">
                    @csrf

                    <div class="form-group">
                        <label>Seleccionar Encuesta *</label>
                        <select name="survey_id" id="surveySelect" required>
                            <option value="">-- Cargando encuestas... --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Seleccionar Graduados * <span style="color: #848282; font-size: 0.85rem;">(Selecciona al menos uno)</span></label>
                        <div style="background: rgba(32,26,47,0.4); padding: 10px; border-radius: 8px; margin-bottom: 10px;">
                            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                <input type="checkbox" id="selectAllGraduates" onclick="toggleAllGraduates()">
                                <span style="font-weight: 600; color: #26BBFF;">Seleccionar Todos</span>
                            </label>
                        </div>
                        <div id="graduatesContainer" style="max-height: 300px; overflow-y: auto; background: rgba(32,26,47,0.4); padding: 15px; border-radius: 8px;">
                            <p style="text-align: center; color: #848282;">Cargando graduados...</p>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success" style="width: 100%;">
                        📋 Asignar Encuesta
                    </button>
                </form>
            </div>
        </div>

        <!-- Gestionar Encuestas Section (Solo Vista) -->
        <!-- Gestionar Encuestas Section (FUNCIONAL) -->
        <div id="gestionar-encuestas" class="section">
            <div class="card-container">
                <h2>Gestionar Encuestas Existentes</h2>
                <p style="color: #848282; margin-bottom: 20px;">
                    Visualiza, edita o elimina encuestas existentes.
                </p>

                @if(session('success'))
                    <div style="background: rgba(16,185,129,0.1); border-left: 4px solid #10B981; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <p style="color: #10B981; margin: 0;">{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div style="background: rgba(239,68,68,0.1); border-left: 4px solid #EF4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <p style="color: #EF4444; margin: 0;">{{ session('error') }}</p>
                    </div>
                @endif

                <div id="surveysTableContainer">
                    <p style="text-align: center; color: #848282; padding: 40px;">Cargando encuestas...</p>
                </div>
            </div>

            <!-- Modal para Ver Detalles -->
            <div id="detailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; overflow-y: auto;">
                <div style="background: #0F0F02; max-width: 800px; margin: 50px auto; border-radius: 16px; padding: 30px; position: relative;">
                    <button onclick="closeDetailsModal()" style="position: absolute; top: 20px; right: 20px; background: transparent; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">✕</button>
                    <div id="detailsContent"></div>
                </div>
            </div>

            <!-- Modal para Editar -->
            <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; overflow-y: auto;">
                <div style="background: #0F0F02; max-width: 600px; margin: 50px auto; border-radius: 16px; padding: 30px; position: relative;">
                    <button onclick="closeEditModal()" style="position: absolute; top: 20px; right: 20px; background: transparent; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">✕</button>
                    <h2 style="margin-bottom: 20px;">Editar Encuesta</h2>
                    <form id="editSurveyForm" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Nombre de la Encuesta *</label>
                            <input type="text" name="name" id="edit_name" required>
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea name="description" id="edit_description"></textarea>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="is_active" id="edit_is_active" value="1"> Encuesta activa
                            </label>
                        </div>
                        <button type="submit" class="btn btn-success" style="width: 100%;">💾 Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>

<!-- Reportes Section (FUNCIONAL CON PDF) -->
<div id="reportes" class="section">
    <div class="card-container">
        <h2>Reportes de Encuestas</h2>
        <p style="color: #848282; margin-bottom: 20px;">
            Descarga reportes detallados en PDF de las encuestas completadas.
        </p>

        @if(session('success'))
            <div style="background: rgba(16,185,129,0.1); border-left: 4px solid #10B981; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <p style="color: #10B981; margin: 0;">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div style="background: rgba(239,68,68,0.1); border-left: 4px solid #EF4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <p style="color: #EF4444; margin: 0;">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Lista de encuestas con respuestas -->
        <div id="reportsListContainer">
            <p style="text-align: center; color: #848282; padding: 40px;">Cargando encuestas...</p>
        </div>
    </div>
</div>

<script>
// Cargar encuestas con respuestas al mostrar la sección de reportes
document.addEventListener('DOMContentLoaded', function() {
    // Cargar cuando se muestre la sección de reportes
    const reportesMenuItem = document.querySelector('.menu-item[onclick*="reportes"]');
    if (reportesMenuItem) {
        reportesMenuItem.addEventListener('click', loadReportsSurveys);
    }
});

// Cargar encuestas para reportes
function loadReportsSurveys() {
    fetch("{{ route('impacto.reports.data') }}")
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderReportsSurveys(data.surveys);
            } else {
                showReportsError('No se pudieron cargar las encuestas');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showReportsError('Error de conexión');
        });
}

// Renderizar lista de encuestas para reportes
function renderReportsSurveys(surveys) {
    const container = document.getElementById('reportsListContainer');
    
    if (!container) return;
    
    if (surveys.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 60px 20px; background: rgba(32,26,47,0.4); border-radius: 12px;">
                <div style="font-size: 3rem; margin-bottom: 15px;">📊</div>
                <h3 style="color: #848282; margin-bottom: 10px;">No hay encuestas creadas</h3>
                <p style="color: #848282; font-size: 0.9rem;">
                    Crea una encuesta primero y asígnala a graduados para generar reportes.
                </p>
            </div>
        `;
        return;
    }
    
    let html = '<div style="display: grid; gap: 15px;">';
    
    surveys.forEach(survey => {
        const completedCount = survey.completed_count || 0;
        const totalAssigned = survey.total_assigned || 0;
        const questionsCount = survey.questions_count || 0;
        const hasResponses = completedCount > 0;
        
        // Calcular porcentaje de completado
        const completionRate = totalAssigned > 0 ? Math.round((completedCount / totalAssigned) * 100) : 0;
        
        html += `
            <div style="background: rgba(32,26,47,0.4); padding: 20px; border-radius: 12px; border-left: 4px solid ${hasResponses ? '#10B981' : '#848282'};">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex: 1;">
                        <h3 style="color: #26BBFF; margin-bottom: 8px; font-size: 1.2rem;">
                            ${survey.name}
                        </h3>
                        ${survey.description ? `
                            <p style="color: #848282; font-size: 0.9rem; margin-bottom: 10px;">
                                ${survey.description}
                            </p>
                        ` : ''}
                        
                        <!-- Estadísticas de la encuesta -->
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-top: 15px;">
                            <div>
                                <p style="color: #848282; font-size: 0.8rem; margin-bottom: 3px;">Preguntas</p>
                                <p style="color: #26BBFF; font-size: 1.3rem; font-weight: 600;">${questionsCount}</p>
                            </div>
                            <div>
                                <p style="color: #848282; font-size: 0.8rem; margin-bottom: 3px;">Asignadas</p>
                                <p style="color: #FFC107; font-size: 1.3rem; font-weight: 600;">${totalAssigned}</p>
                            </div>
                            <div>
                                <p style="color: #848282; font-size: 0.8rem; margin-bottom: 3px;">Completadas</p>
                                <p style="color: #10B981; font-size: 1.3rem; font-weight: 600;">${completedCount}</p>
                            </div>
                            <div>
                                <p style="color: #848282; font-size: 0.8rem; margin-bottom: 3px;">Tasa</p>
                                <p style="color: ${completionRate > 50 ? '#10B981' : '#FFC107'}; font-size: 1.3rem; font-weight: 600;">${completionRate}%</p>
                            </div>
                        </div>

                        <!-- Barra de progreso -->
                        ${totalAssigned > 0 ? `
                            <div style="margin-top: 12px;">
                                <div style="background: rgba(132,130,130,0.2); height: 8px; border-radius: 4px; overflow: hidden;">
                                    <div style="background: #10B981; height: 100%; width: ${completionRate}%; transition: width 0.3s ease;"></div>
                                </div>
                            </div>
                        ` : ''}

                        <!-- Estado de la encuesta -->
                        <div style="margin-top: 10px;">
                            <span style="color: ${survey.is_active ? '#10B981' : '#FFC107'}; font-size: 0.85rem; font-weight: 600; padding: 5px 10px; background: ${survey.is_active ? 'rgba(16,185,129,0.1)' : 'rgba(255,193,7,0.1)'}; border-radius: 6px;">
                                ${survey.is_active ? '● Activa' : '○ Inactiva'}
                            </span>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div style="display: flex; flex-direction: column; gap: 10px; margin-left: 20px;">
                        ${hasResponses ? `
                            <a href="/reports/survey/${survey.id}/download" 
                               class="btn btn-success" 
                               style="white-space: nowrap; text-decoration: none; display: inline-block; text-align: center;">
                                📥 Descargar PDF
                            </a>
                            <a href="/reports/survey/${survey.id}/preview" 
                               target="_blank"
                               class="btn btn-primary" 
                               style="white-space: nowrap; text-decoration: none; display: inline-block; text-align: center;">
                                👁️ Vista Previa
                            </a>
                        ` : `
                            <button class="btn btn-secondary" 
                                    disabled 
                                    style="white-space: nowrap; cursor: not-allowed; opacity: 0.5;"
                                    title="No hay respuestas completadas aún">
                                📥 Sin Respuestas
                            </button>
                            <p style="color: #848282; font-size: 0.75rem; text-align: center; margin-top: 5px;">
                                Espera a que los graduados completen la encuesta
                            </p>
                        `}
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    
    // Agregar resumen general al final
    const totalSurveys = surveys.length;
    const totalWithResponses = surveys.filter(s => s.completed_count > 0).length;
    const totalResponses = surveys.reduce((sum, s) => sum + (s.completed_count || 0), 0);
    
    html += `
        <div style="background: rgba(38,187,255,0.1); padding: 20px; border-radius: 12px; border-left: 4px solid #26BBFF; margin-top: 20px;">
            <h4 style="color: #26BBFF; margin-bottom: 15px; font-size: 1.1rem;">📊 Resumen General</h4>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div>
                    <p style="color: #848282; font-size: 0.85rem;">Total de Encuestas</p>
                    <p style="color: #fff; font-size: 1.8rem; font-weight: 600;">${totalSurveys}</p>
                </div>
                <div>
                    <p style="color: #848282; font-size: 0.85rem;">Con Respuestas</p>
                    <p style="color: #10B981; font-size: 1.8rem; font-weight: 600;">${totalWithResponses}</p>
                </div>
                <div>
                    <p style="color: #848282; font-size: 0.85rem;">Total de Respuestas</p>
                    <p style="color: #26BBFF; font-size: 1.8rem; font-weight: 600;">${totalResponses}</p>
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
}

// Mostrar error en reportes
function showReportsError(message) {
    const container = document.getElementById('reportsListContainer');
    if (container) {
        container.innerHTML = `
            <div style="text-align: center; padding: 40px; background: rgba(239,68,68,0.1); border-radius: 12px; border-left: 4px solid #EF4444;">
                <p style="color: #EF4444; font-size: 1.1rem;">❌ ${message}</p>
            </div>
        `;
    }
}
</script>

<!-- Historial Section (Student - FUNCIONAL) -->
<div id="historial" class="section">
    <div class="card-container">
        <h2>Historial de Encuestas Completadas</h2>
        <p style="color: #848282; margin-bottom: 20px;">
            Revisa las encuestas que ya has completado.
        </p>

        <div style="display: grid; gap: 15px;">
            @php
                // Obtener solo encuestas completadas
                $completedSurveysList = isset($assignedSurveys) 
                    ? $assignedSurveys->where('status', 'completed') 
                    : collect([]);
            @endphp

            @forelse($completedSurveysList as $survey)
                <div style="background: rgba(32,26,47,0.4); padding: 20px; border-radius: 12px; border-left: 4px solid #10B981;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex: 1;">
                            <h3 style="color: #26BBFF; margin-bottom: 8px; font-size: 1.1rem;">
                                {{ $survey->survey->name }}
                            </h3>
                            <p style="color: #848282; font-size: 0.9rem; margin-bottom: 5px;">
                                📚 Programa: <strong>{{ $survey->graduate->program->name }}</strong>
                            </p>
                            <p style="color: #848282; font-size: 0.9rem; margin-bottom: 5px;">
                                📅 Asignada: {{ $survey->assigned_date->format('d/m/Y') }}
                            </p>
                            @if($survey->started_at)
                            <p style="color: #848282; font-size: 0.9rem; margin-bottom: 5px;">
                                🕐 Iniciada: {{ $survey->started_at->format('d/m/Y H:i') }}
                            </p>
                            @endif
                            @if($survey->completed_at)
                            <p style="color: #10B981; font-size: 0.9rem; font-weight: 600;">
                                ✅ Completada: {{ $survey->completed_at->format('d/m/Y H:i') }}
                            </p>
                            @endif
                        </div>

                        <span style="color: #10B981; font-weight: 600; padding: 8px 16px; background: rgba(16,185,129,0.1); border-radius: 8px; white-space: nowrap;">
                            ✓ Completada
                        </span>
                    </div>

                    @if($survey->survey->description)
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(132,130,130,0.2);">
                        <p style="color: #848282; font-size: 0.85rem; font-style: italic;">
                            {{ $survey->survey->description }}
                        </p>
                    </div>
                    @endif
                </div>
            @empty
                <div style="text-align: center; padding: 60px 20px; background: rgba(32,26,47,0.4); border-radius: 12px;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">📋</div>
                    <h3 style="color: #848282; margin-bottom: 10px;">No hay encuestas completadas</h3>
                    <p style="color: #848282; font-size: 0.9rem;">
                        Cuando completes una encuesta, aparecerá aquí en tu historial.
                    </p>
                </div>
            @endforelse
        </div>

        @if($completedSurveysList->count() > 0)
        <div style="margin-top: 30px; padding: 20px; background: rgba(38,187,255,0.1); border-radius: 12px; border-left: 4px solid #26BBFF;">
            <h4 style="color: #26BBFF; margin-bottom: 10px;">📊 Resumen</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div>
                    <p style="color: #848282; font-size: 0.85rem;">Total Completadas</p>
                    <p style="color: #fff; font-size: 1.5rem; font-weight: 600;">{{ $completedSurveysList->count() }}</p>
                </div>
                <div>
                    <p style="color: #848282; font-size: 0.85rem;">Última Completada</p>
                    <p style="color: #fff; font-size: 1.1rem; font-weight: 600;">
                        {{ $completedSurveysList->sortByDesc('completed_at')->first()->completed_at->format('d/m/Y') }}
                    </p>
                </div>
                <div>
                    <p style="color: #848282; font-size: 0.85rem;">Programas Evaluados</p>
                    <p style="color: #fff; font-size: 1.5rem; font-weight: 600;">
                        {{ $completedSurveysList->pluck('graduate.program_id')->unique()->count() }}
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

        <!-- Configuración Section (Mantenida igual) -->
        <div id="configuracion" class="section">
            <div class="card-container">
                <h2>Información de la Sesión</h2>
                <div class="form-group">
                    <label>Nombre de Usuario</label>
                    <input type="text" value="{{ Auth::user()->full_name }}" readonly style="background: rgba(32,26,47,0.4);">
                </div>

                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input type="email" value="{{ Auth::user()->email }}" readonly style="background: rgba(32,26,47,0.4);">
                </div>

                <div class="form-group">
                    <label>DNI</label>
                    <input type="text" value="{{ Auth::user()->dni }}" readonly style="background: rgba(32,26,47,0.4);">
                </div>

                <div class="form-group">
                    <label>Rol</label>
                    <input type="text" value="{{ $userRole }}" readonly style="background: rgba(32,26,47,0.4);">
                </div>

                <div class="form-group">
                    <label>Última Conexión</label>
                    <input type="text" value="{{ Auth::user()->last_access ? Auth::user()->last_access->format('d-m-Y H:i A') : 'Primera vez' }}" readonly style="background: rgba(32,26,47,0.4);">
                </div>               
                                
            </div>

            <div class="card-container">
                <h2>Cerrar Sesión</h2>
                <p style="color: #848282; margin-bottom: 20px;">Al cerrar sesión, serás redirigido a la página de inicio de sesión.</p>
                
                <form action="{{ route('impacto.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger" style="width: 100%; padding: 15px; font-size: 1.1rem; background: #EF4444;">
                        🚪 Cerrar Sesión
                    </button>
                </form>
            </div>

            <div class="card-container">
                <h2>Información del Sistema</h2>
                <table>
                    <tr>
                        <td style="color: #848282;">Versión del Sistema:</td>
                        <td>INCADEV v1.0.0</td>
                    </tr>
                    <tr>
                        <td style="color: #848282;">Laravel:</td>
                        <td>v11.x</td>
                    </tr>
                    <tr>
                        <td style="color: #848282;">PHP:</td>
                        <td>8.2.x</td>
                    </tr>
                    <tr>
                        <td style="color: #848282;">Zona Horaria:</td>
                        <td>America/Lima</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Navigation básica (sin funcionalidad de formularios)
        function showSection(sectionId) {
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('active'));
            
            const section = document.getElementById(sectionId);
            if (section) {
                section.classList.add('active');
            }
            
            if (event && event.target && event.target.closest('.menu-item')) {
                event.target.closest('.menu-item').classList.add('active');
            }
            
            const titles = {
                'dashboard': 'Dashboard Admin',
                'crear-encuesta': 'Crear Encuesta',
                'asignar-encuesta': 'Asignar Encuesta',
                'gestionar-encuestas': 'Gestionar Encuestas',
                'reportes': 'Reportes y Estadísticas',
                'historial': 'Historial de Encuestas',
                'configuracion': 'Configuración'
            };
            
            const titleElement = document.getElementById('pageTitle');
            if (titleElement && titles[sectionId]) {
                titleElement.textContent = titles[sectionId];
            }
            
            // Close dropdown
            const dropdown = document.getElementById('userDropdown');
            if (dropdown) {
                dropdown.classList.remove('show');
            }
        }

        function toggleDropdown() {
            event.stopPropagation();
            document.getElementById('userDropdown').classList.toggle('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            document.getElementById('userDropdown').classList.remove('show');
        });

        // Charts básicos (solo visual)
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de programas
            const programsCtx = document.getElementById('programsChart');
            if (programsCtx) {
                new Chart(programsCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Programa 1', 'Programa 2', 'Programa 3', 'Programa 4'],
                        datasets: [{
                            label: 'Graduados',
                            data: [25, 18, 32, 12],
                            backgroundColor: '#26BBFF',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { 
                                beginAtZero: true,
                                ticks: { color: '#848282' }, 
                                grid: { color: 'rgba(132,130,130,0.1)' } 
                            },
                            x: { 
                                ticks: { color: '#848282' }, 
                                grid: { display: false } 
                            }
                        }
                    }
                });
            }

            // Gráfico de encuestas
            const surveysCtx = document.getElementById('surveysChart');
            if (surveysCtx) {
                new Chart(surveysCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Completadas', 'Pendientes'],
                        datasets: [{
                            data: [65, 35],
                            backgroundColor: ['#10B981', '#FFC107'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: '#fff', padding: 20 }
                            }
                        }
                    }
                });
            }

            // Gráfico de reportes
            const reportsCtx = document.getElementById('reportsChart');
            if (reportsCtx) {
                new Chart(reportsCtx, {
                    type: 'line',
                    data: {
                        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Respuestas Mensuales',
                            data: [45, 78, 65, 92, 105, 120],
                            borderColor: '#26BBFF',
                            backgroundColor: 'rgba(38,187,255,0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { labels: { color: '#fff' } } },
                        scales: {
                            y: { ticks: { color: '#848282' }, grid: { color: 'rgba(132,130,130,0.1)' } },
                            x: { ticks: { color: '#848282' }, grid: { color: 'rgba(132,130,130,0.1)' } }
                        }
                    }
                });
            }
        });

                // ========================
        // FUNCIONALIDAD DE CREAR ENCUESTA
        // ========================

        let questionCounter = 1; // Contador de preguntas (empieza en 1 porque ya hay una pregunta inicial)

        // Agregar nueva pregunta
        function addQuestion() {
            const container = document.getElementById('questionsContainer');
            const questionIndex = questionCounter;
            
            const questionHTML = `
                <div class="question-item" data-question-index="${questionIndex}">
                    <h4>Pregunta #${questionIndex + 1}</h4>
                    <div class="form-group">
                        <label>Texto de la Pregunta *</label>
                        <input type="text" name="questions[${questionIndex}][question_text]" placeholder="Escribe la pregunta aquí..." required>
                    </div>

                    <div class="form-group">
                        <label>Tipo de Pregunta *</label>
                        <select name="questions[${questionIndex}][question_type]" onchange="handleQuestionTypeChange(this, ${questionIndex})" required>
                            <option value="text">Texto libre</option>
                            <option value="number">Número</option>
                            <option value="option">Opción única</option>
                            <option value="date">Fecha</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="questions[${questionIndex}][is_required]" value="1"> Pregunta obligatoria
                        </label>
                    </div>

                    <div class="options-container" id="options-container-${questionIndex}" style="display: none;">
                        <h5 style="color: #26BBFF; margin: 15px 0 10px 0;">Opciones de Respuesta</h5>
                        <div class="options-list" id="options-list-${questionIndex}">
                            <div class="option-item">
                                <input type="text" name="questions[${questionIndex}][options][]" placeholder="Opción 1">
                                <button type="button" class="btn btn-small btn-danger" onclick="removeOption(this)">🗑️</button>
                            </div>
                            <div class="option-item">
                                <input type="text" name="questions[${questionIndex}][options][]" placeholder="Opción 2">
                                <button type="button" class="btn btn-small btn-danger" onclick="removeOption(this)">🗑️</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-small btn-secondary" onclick="addOption(${questionIndex})">➕ Agregar Opción</button>
                    </div>

                    <button type="button" class="btn btn-small btn-danger" onclick="removeQuestion(this)" style="margin-top: 15px;">🗑️ Eliminar Pregunta</button>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', questionHTML);
            questionCounter++;
            updateQuestionNumbers();
        }

        // Eliminar pregunta
        function removeQuestion(button) {
            const questionItem = button.closest('.question-item');
            questionItem.remove();
            updateQuestionNumbers();
        }

        // Actualizar numeración de preguntas
        function updateQuestionNumbers() {
            const questions = document.querySelectorAll('.question-item');
            questions.forEach((question, index) => {
                question.querySelector('h4').textContent = `Pregunta #${index + 1}`;
            });
        }

        // Manejar cambio de tipo de pregunta
        function handleQuestionTypeChange(selectElement, questionIndex) {
            const optionsContainer = document.getElementById(`options-container-${questionIndex}`);
            
            if (selectElement.value === 'option') {
                optionsContainer.style.display = 'block';
            } else {
                optionsContainer.style.display = 'none';
            }
        }

        // Agregar nueva opción a una pregunta
        function addOption(questionIndex) {
            const optionsList = document.getElementById(`options-list-${questionIndex}`);
            const optionCount = optionsList.querySelectorAll('.option-item').length + 1;
            
            const optionHTML = `
                <div class="option-item">
                    <input type="text" name="questions[${questionIndex}][options][]" placeholder="Opción ${optionCount}">
                    <button type="button" class="btn btn-small btn-danger" onclick="removeOption(this)">🗑️</button>
                </div>
            `;
            
            optionsList.insertAdjacentHTML('beforeend', optionHTML);
        }

        // Eliminar opción
        function removeOption(button) {
            const optionItem = button.closest('.option-item');
            optionItem.remove();
        }
        // ========================
        // FUNCIONALIDAD DE ASIGNAR ENCUESTA
        // ========================

        // Cargar datos al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            loadAssignmentData();
        });

        // Cargar encuestas y graduados
        function loadAssignmentData() {
            fetch("{{ route('impacto.surveys.assignment.data') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadSurveys(data.surveys);
                        loadGraduates(data.graduates);
                    } else {
                        console.error('Error al cargar datos:', data.message);
                        showError('No se pudieron cargar los datos');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Error de conexión');
                });
        }

        // Cargar encuestas en el select
        function loadSurveys(surveys) {
            const surveySelect = document.getElementById('surveySelect');
            
            if (!surveySelect) return;
            
            if (surveys.length === 0) {
                surveySelect.innerHTML = '<option value="">-- No hay encuestas activas --</option>';
                return;
            }
            
            surveySelect.innerHTML = '<option value="">-- Selecciona una encuesta --</option>';
            
            surveys.forEach(survey => {
                const option = document.createElement('option');
                option.value = survey.id;
                option.textContent = survey.name;
                if (survey.description) {
                    option.title = survey.description;
                }
                surveySelect.appendChild(option);
            });
        }

        // Cargar graduados en la lista
        function loadGraduates(graduates) {
            const container = document.getElementById('graduatesContainer');
            
            if (!container) return;
            
            if (graduates.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #848282;">No hay graduados disponibles</p>';
                return;
            }
            
            container.innerHTML = '';
            
            graduates.forEach(graduate => {
                const graduateDiv = document.createElement('div');
                graduateDiv.style.cssText = 'margin-bottom: 12px; padding: 12px; background: rgba(17,17,21,0.6); border-radius: 8px; transition: all 0.3s ease;';
                graduateDiv.onmouseover = function() { this.style.background = 'rgba(38,187,255,0.1)'; };
                graduateDiv.onmouseout = function() { this.style.background = 'rgba(17,17,21,0.6)'; };
                
                const userName = graduate.user ? graduate.user.full_name : 'Sin nombre';
                const programName = graduate.program ? graduate.program.name : 'Sin programa';
                const graduationDate = formatDate(graduate.graduation_date);
                
                graduateDiv.innerHTML = `
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="graduate_ids[]" value="${graduate.id}" class="graduate-checkbox">
                        <div>
                            <div style="font-weight: 600; color: #fff;">${userName}</div>
                            <div style="font-size: 0.85rem; color: #848282;">
                                Programa: ${programName} | Graduado: ${graduationDate}
                            </div>
                        </div>
                    </label>
                `;
                
                container.appendChild(graduateDiv);
            });
        }

        // Seleccionar/Deseleccionar todos
        function toggleAllGraduates() {
            const selectAll = document.getElementById('selectAllGraduates');
            const checkboxes = document.querySelectorAll('.graduate-checkbox');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        }

        // Formatear fecha
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            try {
                const date = new Date(dateString);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${day}/${month}/${year}`;
            } catch (e) {
                return 'N/A';
            }
        }

        // Mostrar error en UI
        function showError(message) {
            const surveySelect = document.getElementById('surveySelect');
            const graduatesContainer = document.getElementById('graduatesContainer');
            
            if (surveySelect) {
                surveySelect.innerHTML = `<option value="">Error: ${message}</option>`;
            }
            
            if (graduatesContainer) {
                graduatesContainer.innerHTML = `<p style="text-align: center; color: #EF4444;">${message}</p>`;
            }
        }
        // ========================
        // FUNCIONALIDAD DE GESTIONAR ENCUESTAS
        // ========================

        // Cargar encuestas al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            loadSurveysTable();
        });

        // Cargar tabla de encuestas
        function loadSurveysTable() {
            fetch("{{ route('impacto.surveys.list') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderSurveysTable(data.surveys);
                    } else {
                        showSurveysError('No se pudieron cargar las encuestas');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showSurveysError('Error de conexión');
                });
        }

        // Renderizar tabla de encuestas
        function renderSurveysTable(surveys) {
            const container = document.getElementById('surveysTableContainer');
            
            if (!container) return;
            
            if (surveys.length === 0) {
                container.innerHTML = '<p style="text-align: center; color: #848282; padding: 40px;">No hay encuestas creadas aún</p>';
                return;
            }
            
            let tableHTML = `
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Estado</th>
                            <th>Preguntas</th>
                            <th>Asignaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            surveys.forEach(survey => {
                const statusColor = survey.is_active ? '#10B981' : '#FFC107';
                const statusText = survey.is_active ? 'Activa' : 'Inactiva';
                
                tableHTML += `
                    <tr>
                        <td>${survey.id}</td>
                        <td>${survey.name}</td>
                        <td><span style="color: ${statusColor};">● ${statusText}</span></td>
                        <td>${survey.questions_count || 0}</td>
                        <td>${survey.graduate_surveys_count || 0}</td>
                        <td>
                            <div class="action-btns">
                                <button class="btn btn-small btn-primary" onclick="viewSurveyDetails(${survey.id})">👁️ Ver</button>
                                <button class="btn btn-small btn-primary" onclick="editSurvey(${survey.id})">✏️ Editar</button>
                                <button class="btn btn-small btn-danger" onclick="deleteSurvey(${survey.id}, '${survey.name}')">🗑️ Eliminar</button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            tableHTML += `
                    </tbody>
                </table>
            `;
            
            container.innerHTML = tableHTML;
        }

        // Ver detalles de encuesta
        function viewSurveyDetails(surveyId) {
            fetch(`/impacto/surveys/${surveyId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showDetailsModal(data.survey);
                    } else {
                        alert('Error al cargar detalles');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de conexión');
                });
        }

        // Mostrar modal de detalles
        function showDetailsModal(survey) {
            const modal = document.getElementById('detailsModal');
            const content = document.getElementById('detailsContent');
            
            let questionsHTML = '';
            
            survey.questions.forEach((question, index) => {
                questionsHTML += `
                    <div class="question-item">
                        <h4>Pregunta #${index + 1}</h4>
                        <p style="margin-bottom: 10px;"><strong>Texto:</strong> ${question.question_text}</p>
                        <p style="margin-bottom: 10px;"><strong>Tipo:</strong> ${getQuestionTypeName(question.question_type)}</p>
                        <p style="margin-bottom: 10px;"><strong>Obligatoria:</strong> ${question.is_required ? 'Sí' : 'No'}</p>
                `;
                
                if (question.options && question.options.length > 0) {
                    questionsHTML += '<p style="margin-bottom: 5px;"><strong>Opciones:</strong></p><ul style="padding-left: 20px;">';
                    question.options.forEach(option => {
                        questionsHTML += `<li>${option.option_text}</li>`;
                    });
                    questionsHTML += '</ul>';
                }
                
                questionsHTML += '</div>';
            });
            
            content.innerHTML = `
                <h2 style="color: #26BBFF; margin-bottom: 20px;">${survey.name}</h2>
                <p style="color: #848282; margin-bottom: 20px;">${survey.description || 'Sin descripción'}</p>
                <p style="margin-bottom: 20px;"><strong>Estado:</strong> <span style="color: ${survey.is_active ? '#10B981' : '#FFC107'};">${survey.is_active ? 'Activa' : 'Inactiva'}</span></p>
                <h3 style="color: #26BBFF; margin: 20px 0;">Preguntas (${survey.questions.length})</h3>
                ${questionsHTML}
            `;
            
            modal.style.display = 'block';
        }

        // Cerrar modal de detalles
        function closeDetailsModal() {
            document.getElementById('detailsModal').style.display = 'none';
        }

        // Editar encuesta
        function editSurvey(surveyId) {
            fetch(`/impacto/surveys/${surveyId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showEditModal(data.survey);
                    } else {
                        alert('Error al cargar datos');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de conexión');
                });
        }

        // Mostrar modal de edición
        function showEditModal(survey) {
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editSurveyForm');
            
            form.action = `/impacto/surveys/${survey.id}/update`;
            document.getElementById('edit_name').value = survey.name;
            document.getElementById('edit_description').value = survey.description || '';
            document.getElementById('edit_is_active').checked = survey.is_active;
            
            modal.style.display = 'block';
        }// Eliminar encuesta
        // Eliminar encuesta
            function deleteSurvey(surveyId, surveyName) {
                if (!confirm(`¿Estás seguro de eliminar la encuesta "${surveyName}"?\n\nEsta acción no se puede deshacer.`)) {
                    return;
                }
                
                // Obtener el token CSRF
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                
                if (!csrfToken) {
                    alert('❌ Error: Token CSRF no encontrado');
                    return;
                }

                fetch(`/impacto/surveys/${surveyId}/delete`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken.content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Error al eliminar');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('✅ ' + data.message);
                        loadSurveysTable(); // Recargar la tabla
                    } else {
                        alert('❌ ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('❌ ' + error.message);
                });
            }

        // Cerrar modal de edición
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Eliminar encuesta
        function deleteSurvey(surveyId, surveyName) {
            if (!confirm(`¿Estás seguro de eliminar la encuesta "${surveyName}"?\n\nEsta acción no se puede deshacer.`)) {
                return;
            }
            
            fetch(`/impacto/surveys/${surveyId}/delete`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Encuesta eliminada exitosamente');
                    loadSurveysTable();
                } else {
                    alert('❌ ' + (data.message || 'Error al eliminar'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error de conexión');
            });
        }

        // Obtener nombre del tipo de pregunta
        function getQuestionTypeName(type) {
            const types = {
                'text': 'Texto libre',
                'number': 'Número',
                'option': 'Opción única',
                'date': 'Fecha'
            };
            return types[type] || type;
        }

        // Mostrar error
        function showSurveysError(message) {
            const container = document.getElementById('surveysTableContainer');
            if (container) {
                container.innerHTML = `<p style="text-align: center; color: #EF4444; padding: 40px;">${message}</p>`;
            }
        }

        // ========================
// FUNCIONALIDAD DE MIS ENCUESTAS (ESTUDIANTE)
// ========================

let currentFilter = 'all';

// Filtrar encuestas por estado
function filterSurveys(status) {
    currentFilter = status;
    const surveyCards = document.querySelectorAll('.survey-card');
    
    // Actualizar botones de filtro
    document.querySelectorAll('[id^="filter-"]').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-secondary');
        btn.style.background = 'rgba(132,130,130,0.3)';
    });
    
    const activeBtn = document.getElementById(`filter-${status}`);
    if (activeBtn) {
        activeBtn.classList.remove('btn-secondary');
        activeBtn.classList.add('btn-primary');
        activeBtn.style.background = '#26BBFF';
    }
    
    // Filtrar tarjetas
    surveyCards.forEach(card => {
        const cardStatus = card.getAttribute('data-status');
        
        if (status === 'all' || cardStatus === status) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
    
    // Mostrar mensaje si no hay resultados
    const visibleCards = Array.from(surveyCards).filter(card => card.style.display !== 'none');
    const container = document.getElementById('surveysListContainer');
    
    if (visibleCards.length === 0 && container) {
        const existingMessage = container.querySelector('.no-results-message');
        if (!existingMessage) {
            const message = document.createElement('div');
            message.className = 'no-results-message';
            message.style.cssText = 'text-align: center; padding: 40px; color: #848282;';
            message.textContent = 'No hay encuestas con este estado';
            container.appendChild(message);
        }
    } else {
        const existingMessage = container?.querySelector('.no-results-message');
        if (existingMessage) {
            existingMessage.remove();
        }
    }
}


    </script>
</body>
</html>