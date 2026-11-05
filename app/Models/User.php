<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'account_status', 'gender', 'birth_date', 'is_student', 'nearest_station', 'postal_code', 'address', 'building', 'phone', 'secondary_email', 'notification_method', 'line_user_id', 'line_linked_at', 'has_car', 'car_number', 'base_fee', 'bank_details', 'contract_path', 'last_login_at', 'invited_at', 'suspended_at'])]
#[Hidden(['password', 'remember_token', 'bank_details'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function qualifications(): HasMany
    {
        return $this->hasMany(Qualification::class);
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function canManageAssignments(): bool
    {
        return in_array($this->role, ['employee', 'semi_employee'], true);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'is_student' => 'boolean',
            'line_linked_at' => 'datetime',
            'has_car' => 'boolean',
            'last_login_at' => 'datetime',
            'invited_at' => 'datetime',
            'suspended_at' => 'datetime',
            'bank_details' => 'encrypted:array',
        ];
    }
}
