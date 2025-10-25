<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;


class Audit extends Model
{
    use HasFactory;

    protected $fillable = [
        'area', 'user_id', 'assigned_user_id', 'start_date', 'end_date', 'summary_results', 'type', 'state', 'objective', 'range'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function user() {
        return $this->belongsTo(User::class);
    }

    // Auditor responsable (asignado)
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function findings() {
        return $this->hasMany(Finding::class);
    }

    public function auditReports() {
        return $this->hasMany(AuditReport::class);
    }

     // Métodos para facilitar el acceso a estados de auditoría
    public function isInProgress()
    {
        return $this->state === 'in_progress';
    }

    public function isCompleted()
    {
        return $this->state === 'completed';
    }

    public function isPlanned()
    {
        return $this->state === 'planned';
    }


    /** Visibles por defecto: todo excepto eliminadas */
    public function scopeVisible($q)
    {
        return $q->where('state', '!=', 'cancelled');
    }

    /** Solo eliminadas (por si luego haces papelera/restaurar) */
    public function scopeOnlyDeleted($q)
    {
        return $q->where('state', 'cancelled');
    }

    /** No eliminadas */
    public function scopeNotDeleted($q)
    {
        return $q->where('state', '!=', 'cancelled');
    }
}
