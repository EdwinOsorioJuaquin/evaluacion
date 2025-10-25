<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $table = 'courses';

    protected $fillable = [
        'course_id',
        'title',
        'name',
        'description',
        'level',
        'course_image',
        'video_url',
        'duration',
        'sessions',
        'selling_price',
        'discount_price',
        'prerequisites',
        'certificate_name',
        'certificate_issuer',
        'bestseller',
        'featured',
        'highest_rated',
        'status'
    ];

    protected $casts = [
        'duration' => 'decimal:2',
        'sessions' => 'integer',
        'selling_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'certificate_name' => 'boolean',
        'bestseller' => 'boolean',
        'featured' => 'boolean',
        'highest_rated' => 'boolean',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relación con las ofertas de curso (course_offerings)
    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class, 'course_id');
    }

    // Scope para cursos activos
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Scope para cursos destacados
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    // Scope para bestsellers
    public function scopeBestsellers($query)
    {
        return $query->where('bestseller', true);
    }

    // Scope para cursos de un nivel específico
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    // Método para obtener el precio actual (con descuento si aplica)
    public function getCurrentPriceAttribute()
    {
        return $this->discount_price > 0 ? $this->discount_price : $this->selling_price;
    }

    // Método para verificar si tiene descuento
    public function getHasDiscountAttribute()
    {
        return $this->discount_price > 0 && $this->discount_price < $this->selling_price;
    }

    // Método para calcular el porcentaje de descuento
    public function getDiscountPercentageAttribute()
    {
        if (!$this->has_discount) {
            return 0;
        }
        
        return round((($this->selling_price - $this->discount_price) / $this->selling_price) * 100);
    }

    // Método para obtener el nombre completo del nivel
    public function getLevelNameAttribute()
    {
        return match($this->level) {
            'basic' => 'Básico',
            'intermediate' => 'Intermedio',
            'advanced' => 'Avanzado',
            default => $this->level
        };
    }

    // Método para obtener la duración formateada
    public function getFormattedDurationAttribute()
    {
        if ($this->duration >= 1) {
            return $this->duration . ' horas';
        }
        
        return ($this->duration * 60) . ' minutos';
    }

    // Método para obtener el nombre del curso (usa title o name)
    public function getDisplayNameAttribute()
    {
        return $this->title ?: $this->name;
    }

    // Relación con evaluaciones a través de course_offerings
    public function evaluationResponses()
    {
        return $this->hasManyThrough(
            EvaluationResponse::class,
            CourseOffering::class,
            'course_id', // Foreign key on course_offerings table
            'course_offering_id', // Foreign key on evaluation_responses table
            'id', // Local key on courses table
            'id' // Local key on course_offerings table
        );
    }

    // Contar estudiantes inscritos a través de course_offerings
    public function getTotalEnrolledStudentsAttribute()
    {
        return $this->courseOfferings->sum('enrolled_students');
    }

    // Obtener instructores que imparten este curso
    public function instructors()
    {
        return $this->hasManyThrough(
            Instructor::class,
            CourseOffering::class,
            'course_id', // Foreign key on course_offerings table
            'id', // Foreign key on instructors table
            'id', // Local key on courses table
            'instructor_id' // Local key on course_offerings table
        )->distinct();
    }
}