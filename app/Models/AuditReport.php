<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditReport extends Model
{
    use HasFactory;

    protected $table = 'audit_reports';

    protected $fillable = [
        'audit_id',
        'version_document_id',
        'resume',
        'recommendations',
        'indicators',
        'generation_date',
    ];

    protected $casts = [
        'generation_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con la auditoría
    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }

    // Relación con la versión del documento
    public function versionDocument()
    {
        return $this->belongsTo(DocumentVersion::class, 'version_document_id');
    }
}
