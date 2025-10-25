<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationQuestionOption extends Model
{
    protected $table = 'evaluation_question_options';

    protected $fillable = [
        'question_id',
        'option_value',
        'option_text'
    ];

    // AGREGAR ESTA LÍNEA - deshabilita los timestamps
    public $timestamps = false;

    public function question(): BelongsTo
    {
        return $this->belongsTo(EvaluationQuestion::class, 'question_id');
    }
}