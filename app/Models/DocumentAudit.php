<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentAudit extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'document_audits';

    protected $fillable = [
        'audit_id',
        'version_document_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }

    public function documentVersion()
    {
        return $this->belongsTo(DocumentVersion::class, 'version_document_id');
    }
}
