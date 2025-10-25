<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyCategory extends Model
{
    use HasFactory;

    protected $table = 'satisfaction_survey_categories'; // Nombre de tu tabla
    protected $fillable = ['category_name', 'description'];
}
