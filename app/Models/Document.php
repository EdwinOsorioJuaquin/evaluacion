<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    protected $fillable = [
        'title',
        'category',
        'entity_type',
        'entity_id',
        'version',
        'status',
        'file_path',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con el usuario que creó el documento
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relación con las versiones del documento
    public function documentVersions()
    {
        return $this->hasMany(DocumentVersion::class);
    }
}
