<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Response extends Model
{
    use HasFactory;
    protected $table = 'satisfaction_responses';
    public $timestamps = false; // desactivamos timestamps automáticos
    protected $fillable = [
        'id_student',
        'id_question',
        'id_satisfaction_response',
        'id_opcion',
        'response_text',
        'response_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
    public function student()
{
    return $this->belongsTo(\App\Models\Student::class, 'id_student');
}
public function option()
{
    return $this->belongsTo(\App\Models\Option::class, 'id_satisfaction_option', 'id');
}


}
