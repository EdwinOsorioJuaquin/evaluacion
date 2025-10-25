<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Graduate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'program_id', 'graduation_date', 'final_note',
        'state', 'employability', 'feedback'
    ];

    protected $casts = [
        'graduation_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function graduateSurveys()
    {
        return $this->hasMany(EmpGraduateSurvey::class, 'graduate_id');
    }
}