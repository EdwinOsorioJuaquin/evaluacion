<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'document_versions';

    protected $fillable = [
        'document_id',
        'version_number',
        'file_name',
        'storage_path',
        'mime_type',
        'file_size',
        'uploaded_by_user_id',
        'uploaded_at',
        'checksum',
        'notes',
        'linked_type',
        'linked_id',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con el documento principal
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    // Relación con el usuario que subió esta versión
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
