<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SatisfactionOption;

class Question extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'satisfaction_questions';
    protected $fillable = [
        'id_survey',
        'id_satisfaction_question',
        'type',
        'question_text',
            ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class, 'id_question', 'id');
    }
    public function options()
{
    return $this->hasMany(\App\Models\SatisfactionOption::class, 'id_question', 'id');
}


}
