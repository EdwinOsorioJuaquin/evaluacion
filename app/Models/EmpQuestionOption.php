<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpQuestionOption extends Model
{
    use HasFactory;

    protected $table = 'emp_question_options';

    public $timestamps = false; // ⬅️ AGREGAR ESTA LÍNEA

    protected $fillable = [
        'question_id', 'option_text', 'option_value', 'option_order'
    ];

    public function question()
    {
        return $this->belongsTo(EmpQuestion::class, 'question_id');
    }

    public function responses()
    {
        return $this->hasMany(EmpSurveyResponse::class, 'option_id');
    }
}