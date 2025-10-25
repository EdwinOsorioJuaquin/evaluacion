<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorEvaluationSummary extends Model
{
    protected $table = 'instructor_evaluation_summary';

    protected $fillable = [
        'instructor_id',
        'evaluation_session_id',
        'course_offering_id',
        'total_questions',
        'total_responses',
        'average_rating',
        'completion_rate',
        'evaluation_period'
    ];

    protected $casts = [
        'total_questions' => 'integer',
        'total_responses' => 'integer',
        'average_rating' => 'decimal:2',
        'completion_rate' => 'decimal:2',
        'evaluation_period' => 'date'
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function evaluationSession(): BelongsTo
    {
        return $this->belongsTo(EvaluationSession::class, 'evaluation_session_id');
    }

    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }
}