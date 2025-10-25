<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpGraduateSurvey extends Model
{
    use HasFactory;

    protected $table = 'emp_graduate_surveys';
    public $timestamps = false; // ⬅️ AGREGAR ESTA LÍNEA

    protected $fillable = [
        'graduate_id', 'survey_id', 'status', 'assigned_date', 'started_at', 'completed_at'
    ];

    protected $casts = [
        'assigned_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function graduate()
    {
        return $this->belongsTo(Graduate::class, 'graduate_id');
    }

    public function survey()
    {
        return $this->belongsTo(EmpSurvey::class, 'survey_id');
    }

    public function responses()
    {
        return $this->hasMany(EmpSurveyResponse::class, 'graduate_survey_id');
    }
}