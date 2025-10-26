<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use App\Models\Graduate;
use App\Models\Response;
    
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'full_name',
        'dni',
        'document',
        'email',
        'email_verified_at',
        'phone_number',
        'address',
        'birth_date',
        'role',
        'password',
        'gender',
        'country',
        'country_location',
        'timezone',
        'profile_photo',
        'status',
        'synchronized',
        'last_access_ip',
        'last_access',
        'last_connection'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'role' => 'array',
        'synchronized' => 'boolean',
        'last_access' => 'datetime',
        'last_connection' => 'datetime',
    ];

    /* === Métodos helper de roles === */
public function hasRole($role)
{
    $roles = $this->role;

    // 🧩 1️⃣ Normalizar $roles a array
    if (is_null($roles)) {
        $roles = [];
    } elseif (is_string($roles)) {
        $decoded = json_decode($roles, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            // Ejemplo: '["admin"]' o '["auditor","student"]'
            $roles = $decoded;
        } else {
            // Ejemplo: 'admin'
            $roles = [$roles];
        }
    } elseif (is_object($roles)) {
        $roles = (array) $roles;
    } elseif (!is_array($roles)) {
        // En cualquier otro caso raro
        $roles = [$roles];
    }

    // 🧩 2️⃣ Normalizar el parámetro $role también a array
    $roleArray = is_array($role) ? $role : [$role];

    // 🧩 3️⃣ Convertir ambos a minúsculas y sin espacios
    $roles = array_map(fn($r) => strtolower(trim((string) $r)), $roles);
    $roleArray = array_map(fn($r) => strtolower(trim((string) $r)), $roleArray);

    // 🧩 4️⃣ Comparar
    return count(array_intersect($roles, $roleArray)) > 0;
}



    public function isAdminAuditor() { return $this->hasRole('adminAuditor'); }
    public function isAuditor() { return $this->hasRole('auditor'); }
    public function isAdminEvaluacion() { return $this->hasRole('adminEvaluacion'); }
    public function isAdminImpacto() { return $this->hasRole('adminImpacto'); }

    public function isInstructor()
    {
        $role = $this->getRawRole();
        return $role === 'instructor' || (is_array($role) && in_array('instructor', $role));
    }


     private function getRawRole()
    {
        // Acceder al atributo sin el accessor
        $role = $this->attributes['role'] ?? 'student';
        
        // Si es JSON, decodificarlo
        if (is_string($role) && strpos($role, '[') === 0) {
            $decoded = json_decode($role, true);
            return $decoded ?? 'student';
        }
        
        // Si es string con comillas
        if (is_string($role) && strpos($role, '"') === 0) {
            return trim($role, '"');
        }
        
        return $role;
    }
    
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function graduates()
    {
        return $this->hasMany(Graduate::class, 'user_id');
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

     protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->full_name)) {
                $user->full_name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            }
        });
    }

    public function isAdmin()
    {
        // Si el rol está en el array (["admin"])
        return is_array($this->role) && in_array('admin', $this->role);
    }

    public function isStudent()
    {
        // Si el rol está en el array (["student"])
        return is_array($this->role) && in_array('student', $this->role);
    }

    public function student()
    {
        return $this->hasOne(\App\Models\Student::class, 'user_id');
    }


}
