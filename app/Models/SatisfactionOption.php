<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SatisfactionOption extends Model
{
    use HasFactory;

    protected $table = 'satisfaction_options';
    public $timestamps = false;

    protected $fillable = [
        'id_question',
        'id_satisfaction_option',
        'option_text', // el nombre real de tu columna
    ];

    // Relación con Question
    public function question()
    {
        return $this->belongsTo(\App\Models\Question::class, 'id_question', 'id');
    }

    // Relación con Response
    public function responses()
{
    return $this->hasMany(Response::class, 'id_opcion', 'id');
}

}


