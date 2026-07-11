<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'position',
        'phone', 'telegram_id', 'avatar', 'settings', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    // Проверка ролей
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isExecutor(): bool
    {
        return $this->role === 'executor';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    // Доступ ко всем пользователям (для админа)
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'creator_id');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assignee_id');
    }
}
