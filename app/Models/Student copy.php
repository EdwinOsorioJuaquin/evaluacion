<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'student_code',
        'career',
        'semester',
        'status'
    ];

    protected $casts = [
        'semester' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function evaluationResponses(): HasMany
    {
        return $this->hasMany(EvaluationResponse::class, 'student_id');
    }

    public function courses()
    {
        return $this->belongsToMany(CourseOffering::class, 'student_courses', 'student_id', 'course_offering_id');
    }
}