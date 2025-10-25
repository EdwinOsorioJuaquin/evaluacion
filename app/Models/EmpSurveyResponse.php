<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpSurveyResponse extends Model
{
    use HasFactory;

    protected $table = 'emp_survey_responses';
       public $timestamps = false; // ⬅️ AGREGAR ESTA LÍNEA

    protected $fillable = [
        'graduate_survey_id', 'question_id', 'option_id',
        'text_response', 'number_response', 'date_response', 'responded_at'
    ];

    protected $casts = [
        'date_response' => 'date',
        'responded_at' => 'datetime',
    ];

    public function graduateSurvey()
    {
        return $this->belongsTo(EmpGraduateSurvey::class, 'graduate_survey_id');
    }

    public function question()
    {
        return $this->belongsTo(EmpQuestion::class, 'question_id');
    }

    public function option()
    {
        return $this->belongsTo(EmpQuestionOption::class, 'option_id');
    }
}