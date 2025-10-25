<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class CorrectiveAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'finding_id', 'user_id', 'description', 'status', 'engagement_date', 'due_date', 'completion_date'
    ];

    public function finding() {
        return $this->belongsTo(Finding::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
