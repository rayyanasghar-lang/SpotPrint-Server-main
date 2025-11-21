<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasApiTokens, Notifiable, HasRoles;

    protected $appends = ['full_name', 'role'];

    protected $fillable = [
        'name', // JSON: first_name, middle_name, last_name, etc.
        'username',
        'email',
        'phone',
        'otp',
        'email_verified_at',
        'phone_verified_at',
        'password',

        'otp',  // will hold multipal OTP objects for email, phone
        // 'role', ['User', 'Admin']

        'profile', // JSON: date_of_birth, gender, picture, addresses
        'status',  // enum: Active, inActive, Suspended
        'status_logs', // JSON: logs of status changes
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
        'roles',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'name' => 'array',
        'profile' => 'array',
        'password' => 'hashed',
        'otp' => 'array',
        'status_logs' => 'array',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];


    protected $attributes = [
        'name' => '{"first_name": "", "middle_name": "", "last_name": ""}',
        'profile' => '{"date_of_birth": "", "gender": "", "picture": "", addresses: []}',    
        'otp' => '[]',        
        'status_logs' => '[]',  
    ];

    public function getFullNameAttribute()
    {
        $name = $this->name;

        // Check if 'full_name' is already set, typically from the frontend
        if (!empty($name['full_name'])) {
            return trim($name['full_name']);
        }

        // Otherwise, construct the full name from individual components
        $firstName = $name['first_name'] ?? '';
        $middleName = $name['middle_name'] ?? '';
        $lastName = $name['last_name'] ?? '';

        // Concatenate only non-empty parts to avoid extra spaces
        return trim(collect([$firstName, $middleName, $lastName])->filter()->join(' '));
    }

    public function getRoleAttribute()
    {
        $role = $this->roles->first();
        return $role ? $role->name : null;
    }
}
