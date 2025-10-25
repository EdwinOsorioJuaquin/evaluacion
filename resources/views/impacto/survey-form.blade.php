<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta de Seguimiento - INCADEV</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Tus estilos actuales se mantienen igual */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #201A2F 0%, #111115 100%);
            color: #fff;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(17,17,21,0.6);
            border-radius: 16px;
            padding: 30px;
            backdrop-filter: blur(10px);
        }
        h1 { color: #26BBFF; margin-bottom: 10px; }
        .program-info {
            color: #848282;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(132,130,130,0.2);
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
        .question-item label {
            display: block;
            margin-bottom: 10px;
            color: #848282;
            cursor: pointer;
        }
        .question-item input[type="radio"],
        .question-item input[type="checkbox"] {
            margin-right: 10px;
        }
        .question-item input[type="text"],
        .question-item textarea,
        .question-item select {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 2px solid rgba(132,130,130,0.2);
            background: rgba(17,17,21,0.6);
            color: #fff;
            font-size: 1rem;
            margin-top: 10px;
        }
        .question-item textarea {
            resize: vertical;
            min-height: 80px;
        }
        .btn {
            padding: 15px 30px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-success {
            background: #10B981;
            color: #fff;
            width: 100%;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16,185,129,0.4);
        }
        .btn-secondary {
            background: rgba(132,130,130,0.3);
            color: #fff;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Volver al Dashboard</a>
        
        <h1>{{ $survey->name }}</h1>
        <div class="program-info">
            <strong>Programa:</strong> {{ $graduateSurvey->graduate->program->name }}<br>
            <strong>Fecha de graduación:</strong> {{ $graduateSurvey->graduate->graduation_date->format('d/m/Y') }}<br>
            <strong>Descripción:</strong> {{ $survey->description }}
        </div>

        <form action="{{ route('survey.submit', $graduateSurvey->id) }}" method="POST">
            @csrf
            
            @foreach($questions as $question)
            <div class="question-item">
                <h4>
                    {{ $loop->iteration }}. {{ $question->question_text }}
                    @if($question->is_required) <span style="color: #EF4444;">*</span> @endif
                </h4>
                
                @if($question->question_type === 'option')
                    @foreach($question->options as $option)
                        <label>
                            <input type="radio" name="question_{{ $question->id }}" 
                                   value="{{ $option->id }}" 
                                   @if($question->is_required) required @endif>
                            {{ $option->option_text }}
                        </label>
                    @endforeach
                @elseif($question->question_type === 'text')
                    <textarea name="question_{{ $question->id }}" 
                              placeholder="Escriba su respuesta aquí..."
                              @if($question->is_required) required @endif></textarea>
                @elseif($question->question_type === 'number')
                    <input type="number" name="question_{{ $question->id }}" 
                           step="0.1" 
                           @if($question->is_required) required @endif>
                @elseif($question->question_type === 'date')
                    <input type="date" name="question_{{ $question->id }}" 
                           @if($question->is_required) required @endif>
                @endif
            </div>
            @endforeach

            <button type="submit" class="btn btn-success">
                💾 Enviar Encuesta
            </button>
        </form>
    </div>
</body>
</html>