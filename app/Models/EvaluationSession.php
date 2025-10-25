<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationSession extends Model
{
    protected $table = 'evaluation_sessions';

    protected $fillable = [
        'title', 
        'description', 
        'start_date', 
        'end_date', 
        'academic_period',
        'status',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(EvaluationQuestion::class, 'evaluation_session_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(EvaluationResponse::class, 'evaluation_session_id');
    }
}