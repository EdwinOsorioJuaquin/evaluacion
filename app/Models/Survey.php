<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Survey extends Model
{
    
    protected $table = 'satisfaction_surveys';
    public $timestamps = false; // desactivamos timestamps automáticos

    protected $fillable = [
        'title',
        'id_category',
        'description',
        'qualification',
        'state',
        'id_satisfaction_survey',
        'creation_date', // importante incluirlo si lo usas
    ];

   public function questions()
{
    return $this->hasMany(Question::class, 'id_survey', 'id');
}

public function category()
    {
        return $this->belongsTo(SurveyCategory::class, 'id_category');
    }
// App/Models/Survey.php
public function assigned()
{
    return $this->hasMany(\App\Models\SurveyAssigned::class, 'id_survey', 'id');
}


    public function responses()
    {
        return $this->hasMany(Response::class);
    }
}
