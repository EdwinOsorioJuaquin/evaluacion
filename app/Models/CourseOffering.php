<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourseOffering extends Model
{
    protected $table = 'course_offerings';

    protected $fillable = [
        'course_id',
        'instructor_id',
        'academic_period',
        'section',
        'schedule',
        'classroom',
        'capacity',
        'enrolled_students',
        'status'
    ];

    protected $casts = [
        'capacity' => 'integer',
        'enrolled_students' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function evaluationResponses(): HasMany
    {
        return $this->hasMany(EvaluationResponse::class, 'course_offering_id');
    }

    public function evaluationSummaries(): HasMany
    {
        return $this->hasMany(InstructorEvaluationSummary::class, 'course_offering_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_courses', 'course_offering_id', 'student_id')
                    ->withTimestamps();
    }

    // Método para verificar si hay cupos disponibles
    public function getAvailableSpotsAttribute()
    {
        return $this->capacity - $this->enrolled_students;
    }

    // Método para verificar si está lleno
    public function getIsFullAttribute()
    {
        return $this->enrolled_students >= $this->capacity;
    }

    // Scope para ofertas activas
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}