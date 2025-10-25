@extends('layouts.app')

@section('title', 'Panel de Auditoría')

@section('content')
<h2 class="text-3xl font-bold mb-6 text-gray-800">Panel de Control - Auditoría</h2>

<div class="grid md:grid-cols-3 gap-6">
    <a href="{{ route('auditoria.audits.index') }}" class="bg-white shadow p-6 rounded hover:shadow-lg transition">
        <h3 class="font-semibold text-lg mb-2">Auditorías</h3>
        <p class="text-gray-600">Gestiona todas las auditorías registradas.</p>
    </a>
    <a href="{{ route('auditoria.auditores.index') }}" class="bg-white shadow p-6 rounded hover:shadow-lg transition">
        <h3 class="font-semibold text-lg mb-2">Auditores</h3>
        <p class="text-gray-600">Administra usuarios con rol de auditor o admin.</p>
    </a>
    <a href="{{ route('auditoria.settings.index') }}" class="bg-white shadow p-6 rounded hover:shadow-lg transition">
        <h3 class="font-semibold text-lg mb-2">Configuraciones</h3>
        <p class="text-gray-600">Preferencias, perfil y notificaciones.</p>
    </a>
</div>
@endsection
