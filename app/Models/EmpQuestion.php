<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpQuestion extends Model
{
    use HasFactory;

    protected $table = 'emp_questions';
    public $timestamps = false; // ⬅️ AGREGAR ESTA LÍNEA

    protected $fillable = [
        'survey_id', 'question_text', 'question_type', 'question_order', 'is_required'
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function survey()
    {
        return $this->belongsTo(EmpSurvey::class, 'survey_id');
    }

    public function options()
    {
        return $this->hasMany(EmpQuestionOption::class, 'question_id');
    }

    public function responses()
    {
        return $this->hasMany(EmpSurveyResponse::class, 'question_id');
    }
}