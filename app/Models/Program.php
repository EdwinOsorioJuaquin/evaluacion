<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'duration_weeks', 'max_capacity',
        'start_date', 'end_date', 'price', 'currency', 'image_url',
        'modality', 'required_devices', 'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
    ];

    public function graduates()
    {
        return $this->hasMany(Graduate::class, 'program_id');
    }
}