<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Instructor extends Model
{
    protected $table = 'instructors';

    protected $fillable = [
        'user_id',
        'employee_code',
        'department',
        'specialty',
        'academic_degree',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function evaluationResponses(): HasMany
    {
        return $this->hasMany(EvaluationResponse::class, 'instructor_id');
    }

    public function evaluationSummaries(): HasMany
    {
        return $this->hasMany(InstructorEvaluationSummary::class, 'instructor_id');
    }

    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class, 'instructor_id');
    }

public function courses()
{
    return $this->belongsToMany(Course::class, 'course_offerings', 'instructor_id', 'course_id')
                ->withPivot('academic_period', 'section', 'schedule')
                ->withTimestamps();
}
}