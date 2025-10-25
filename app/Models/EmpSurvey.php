<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpSurvey extends Model
{
    use HasFactory;

    protected $table = 'emp_surveys';
    public $timestamps = false; // ⬅️ AGREGAR ESTA LÍNEA

    protected $fillable = [
        'name', 'description', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(EmpQuestion::class, 'survey_id');
    }

    public function graduateSurveys()
    {
        return $this->hasMany(EmpGraduateSurvey::class, 'survey_id');
    }
}