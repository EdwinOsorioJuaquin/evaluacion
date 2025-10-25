{{-- resources/views/reports/survey_report.blade.php --}} 
@extends('satisfaccion.layouts.app')

@section('title', 'Reporte de Encuesta')

@section('content')
<div class="min-h-screen bg-gray-900 text-white px-6 py-12 font-sans">
    <div class="max-w-7xl mx-auto">

        <!-- Encabezado -->
        <h1 class="text-4xl font-extrabold text-deepSky text-center mb-10 tracking-wide">
            📊 Reporte: {{ $survey->qualification }}
        </h1>

        {{-- 🌟 KPIs --}}
        @php
            use App\Models\Student;

            $totalQuestions = $survey->questions->count();
            $totalResponses = $survey->questions->flatMap->responses->count();
            $positive = $survey->questions->flatMap->responses
                ->filter(fn($r) => in_array(strtolower($r->response_text), ['sí','si']))
                ->count();

            // ✅ Tasa de Respuesta corregida
            $totalStudents = Student::count(); 
            $studentsAnswered = $survey->questions->flatMap->responses
                ->pluck('id_student')
                ->unique()
                ->count();

            $responseRate = $totalStudents > 0 ? round(($studentsAnswered / $totalStudents) * 100, 1) : 0;
            $positivePercentage = $totalResponses > 0 ? round(($positive / $totalResponses) * 100,1) : 0;

            // Datos para gráficos
            $responsesByDate = $survey->questions->flatMap->responses
                ->groupBy(fn($r) => \Carbon\Carbon::parse($r->response_date)->format('d/m/Y'))
                ->map->count();
            $dates = $responsesByDate->keys()->toArray();
            $countsByDate = $responsesByDate->values()->toArray();

            $labels = $survey->questions->pluck('question_text')->toArray();
            $values = $survey->questions->map(fn($q) => $q->responses->count())->toArray();
            $positiveCounts = $survey->questions->map(fn($q) => $q->responses->filter(fn($r) => in_array(strtolower($r->response_text), ['sí','si']))->count())->toArray();
        @endphp

        {{-- KPIs visuales --}}
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 mb-10">
            <div class="bg-blue-600 p-6 rounded-2xl shadow-xl text-center">
                <p class="text-sm uppercase font-bold">Total Preguntas</p>
                <p class="text-3xl font-extrabold">{{ $totalQuestions }}</p>
            </div>
            <div class="bg-green-600 p-6 rounded-2xl shadow-xl text-center">
                <p class="text-sm uppercase font-bold">Total Respuestas</p>
                <p class="text-3xl font-extrabold">{{ $totalResponses }}</p>
            </div>
            <div class="bg-yellow-500 p-6 rounded-2xl shadow-xl text-center">
                <p class="text-sm uppercase font-bold">Tasa de Respuesta (%)</p>
                <p class="text-3xl font-extrabold">{{ $responseRate }}</p>
            </div>
            <div class="bg-red-500 p-6 rounded-2xl shadow-xl text-center">
                <p class="text-sm uppercase font-bold">Respuestas Positivas (%)</p>
                <p class="text-3xl font-extrabold">{{ $positivePercentage }}</p>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">

            <div class="bg-gray-800 p-6 rounded-2xl shadow-xl">
                <h2 class="text-xl font-bold mb-4">📊 Respuestas por Pregunta</h2>
                <div class="relative h-80">
                    <canvas id="chartBar" class="absolute inset-0 w-full h-full"></canvas>
                </div>
            </div>

            <div class="bg-gray-800 p-6 rounded-2xl shadow-xl">
                <h2 class="text-xl font-bold mb-4">📊 Respuestas Positivas por Pregunta</h2>
                <div class="relative h-80">
                    <canvas id="chartBarPositive" class="absolute inset-0 w-full h-full"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 w-full lg:col-span-2">
                @foreach($survey->questions as $index => $question)
                    @php
                        $optionsLabels = $question->options->pluck('option_text')->toArray();
                        $optionsCounts = $question->options->map(fn($opt) => $question->responses->where('response_text', $opt->option_text)->count())->toArray();
                    @endphp
                    <div class="bg-gray-800 p-6 rounded-2xl shadow-xl">
                        <h2 class="text-xl font-bold mb-4">📊 Distribución: "{{ $question->question_text }}"</h2>
                        <div class="relative h-64">
                            <canvas id="chartPie{{ $index }}" class="absolute inset-0 w-full h-full"></canvas>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-gray-800 p-6 rounded-2xl shadow-xl lg:col-span-2">
                <h2 class="text-xl font-bold mb-4">📈 Evolución de Respuestas por Fecha</h2>
                <div class="relative h-80">
                    <canvas id="chartLine" class="absolute inset-0 w-full h-full"></canvas>
                </div>
            </div>

        </div>

        <!-- Tabla de preguntas y exportación -->
        <div class="bg-gray-800 p-6 rounded-2xl shadow-xl mb-10">
            <h2 class="text-xl font-bold mb-4">📋 Detalle de Preguntas y Respuestas</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead>
                        <tr class="bg-gray-700">
                            <th class="px-4 py-2 text-left">Nº</th>
                            <th class="px-4 py-2 text-left">Pregunta</th>
                            <th class="px-4 py-2 text-left">Resumen de Respuestas</th>
                            <th class="px-4 py-2 text-left">Detalle de Respuestas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-600">
                        @php
                            $firstResponseDate = $survey->questions->flatMap->responses->min('response_date') ?? $survey->creation_date;
                            $lastResponseDate = $survey->questions->flatMap->responses->max('response_date') ?? $survey->creation_date;
                        @endphp

                        @foreach ($survey->questions as $index => $question)
                            @php
                                $total = $question->responses->count();
                                $grouped = $question->responses->groupBy('response_text')->map->count();
                            @endphp
                            <tr>
                                <td class="px-4 py-2">{{ $index + 1 }}</td>
                                <td class="px-4 py-2">{{ $question->question_text }}</td>
                                <td class="px-4 py-2">
                                    @if($total > 0)
                                        @foreach ($grouped as $answer => $count)
                                            @php $percentage = round(($count / $total) * 100, 1); @endphp
                                            • {{ $answer ?? 'Sin respuesta' }} — {{ $count }} ({{ $percentage }}%) <br>
                                        @endforeach
                                    @else
                                        Sin respuestas registradas
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    @if($total > 0)
                                        Encuesta respondida desde {{ \Carbon\Carbon::parse($firstResponseDate)->format('d/m/Y H:i') }}
                                        hasta {{ \Carbon\Carbon::parse($lastResponseDate)->format('d/m/Y H:i') }}
                                    @else
                                        No se han registrado respuestas aún.
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex flex-wrap gap-4 justify-center mb-10">
            <a href="{{ route('satisfaccion.admin.reports.pdf', $survey->id) }}" class="bg-red-500 hover:bg-red-600 text-white font-bold px-6 py-3 rounded-xl transition duration-300">📄 Descargar PDF</a>
            <a href="{{ route('satisfaccion.admin.reports.excel', $survey->id) }}" class="bg-green-500 hover:bg-green-600 text-night font-bold px-6 py-3 rounded-xl transition duration-300">📊 Descargar Excel</a>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('chartBar'), {
        type: 'bar',
        data: { labels:@json($labels), datasets:[{label:'Total respuestas', data:@json($values), backgroundColor:'#26BBFF'}] },
        options: { responsive:true, maintainAspectRatio:true, plugins:{legend:{display:false}}, scales:{ x:{ticks:{color:'#ffffff'},grid:{color:'#848282'}}, y:{beginAtZero:true,ticks:{color:'#ffffff'},grid:{color:'#848282'}} } }
    });

    new Chart(document.getElementById('chartBarPositive'), {
        type: 'bar',
        data: { labels:@json($labels), datasets:[{label:'Respuestas positivas', data:@json($positiveCounts), backgroundColor:'#4BC0C0'}] },
        options: { responsive:true, maintainAspectRatio:true, plugins:{legend:{display:false}}, scales:{ x:{ticks:{color:'#ffffff'},grid:{color:'#848282'}}, y:{beginAtZero:true,ticks:{color:'#ffffff'},grid:{color:'#848282'}} } }
    });

    @foreach($survey->questions as $index => $question)
        new Chart(document.getElementById('chartPie{{ $index }}'), {
            type:'doughnut',
            data:{ labels:@json($question->options->pluck('option_text')), datasets:[{ data:@json($question->options->map(fn($opt) => $question->responses->where('response_text', $opt->option_text)->count())), backgroundColor:['#FF6384','#36A2EB','#FFCE56','#4BC0C0','#9966FF','#FF9F40'] }] },
            options:{ responsive:true, maintainAspectRatio:true }
        });
    @endforeach

    new Chart(document.getElementById('chartLine'), {
        type:'line',
        data:{ labels:@json($dates), datasets:[{ label:'Respuestas', data:@json($countsByDate), fill:false, borderColor:'#FFCE56', tension:0.3, pointBackgroundColor:'#FFCE56' }] },
        options:{ responsive:true, maintainAspectRatio:true, plugins:{legend:{labels:{color:'#ffffff'}}}, scales:{ x:{ticks:{color:'#ffffff'},grid:{color:'#848282'}}, y:{ticks:{color:'#ffffff'},grid:{color:'#848282'},beginAtZero:true} } }
    });
</script>
@endsection


