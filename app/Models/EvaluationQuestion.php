<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationQuestion extends Model
{
    protected $table = 'evaluation_questions';

    protected $fillable = [
        'evaluation_session_id',
        'question_text',
        'question_type',
        'question_order',
        'is_required',
        'status'
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(EvaluationSession::class, 'evaluation_session_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(EvaluationQuestionOption::class, 'question_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(EvaluationResponse::class, 'question_id');
    }
}