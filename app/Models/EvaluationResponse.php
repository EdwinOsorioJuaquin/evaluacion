<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationResponse extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'instructor_id',
        'evaluation_session_id',
        'question_id',
        'text_response',
        'response_date',
        'course_offering_id',
        'rating',
        'question_option_id' // AGREGAR este campo si existe
    ];

    protected $casts = [
        'response_date' => 'datetime',
        'rating' => 'integer',
    ];

    // RELACIONES CORRECTAS según tu estructura de DB
    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Instructor::class, 'instructor_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(EvaluationSession::class, 'evaluation_session_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(EvaluationQuestion::class, 'question_id');
    }

    // AGREGAR ESTA RELACIÓN QUE TE FALTA
    public function questionOption(): BelongsTo
    {
        return $this->belongsTo(EvaluationQuestionOption::class, 'question_option_id');
    }
}