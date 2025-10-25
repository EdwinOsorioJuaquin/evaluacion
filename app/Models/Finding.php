<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finding extends Model
{
     use HasFactory;
     public $timestamps = false; 

    protected $fillable = [
        'audit_id', 'description', 'classification', 'severity', 'evidence', 'discovery_date'
    ];

    public function audit() {
        return $this->belongsTo(Audit::class);
    }

    public function correctiveActions() {
        return $this->hasMany(CorrectiveAction::class);
    }
}
