@extends('layouts.app')

@section('title', 'Iniciar Auditoría')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-4">Iniciar Auditoría #{{ $audit->id }} - {{ $audit->objective }}</h1>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-2xl font-semibold mb-4">Registrar Hallazgos</h3>

        <form action="{{ route('audits.storeFinding', $audit->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Hallazgo -->
            <div class="mb-4">
                <label for="finding_description" class="block text-sm font-semibold text-gray-700">Descripción del Hallazgo</label>
                <textarea name="finding_description" id="finding_description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50" required>{{ old('finding_description') }}</textarea>
                @error('finding_description') <span class="text-red-600">{{ $message }}</span> @enderror
            </div>

            <!-- Adjuntar documento -->
            <div class="mb-4">
                <label for="document" class="block text-sm font-semibold text-gray-700">Adjuntar Documento (opcional)</label>
                <input type="file" name="document" id="document" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                @error('document') <span class="text-red-600">{{ $message }}</span> @enderror
            </div>

            <!-- Botón para guardar hallazgo -->
            <div class="mb-4 text-right">
                <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-400 text-white font-semibold rounded-lg">Registrar Hallazgo</button>
            </div>
        </form>

        <!-- Listado de hallazgos ya registrados -->
        <h4 class="text-xl font-semibold mt-6">Hallazgos Registrados</h4>
        @foreach ($audit->findings as $finding)
            <div class="bg-gray-100 p-4 mb-4 rounded-lg">
                <p><strong>Descripción:</strong> {{ $finding->description }}</p>
                @if($finding->document)
                    <p><strong>Documento adjunto:</strong> <a href="{{ asset('storage/' . $finding->document) }}" class="text-blue-500">Ver Documento</a></p>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
