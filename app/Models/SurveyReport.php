<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyReport extends Model
{
    use HasFactory;

    protected $table = 'survey_reports';

    protected $fillable = [
        'id_report',
        'id_survey',
        'report_type',
        'file_path',
        'creation_date',
    ];

    public $timestamps = false;
}
