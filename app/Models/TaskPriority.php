<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskPriority extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'level',
        'color',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'priority_id');
    }

    public function color(): string
    {
        return $this->color ?? '#6b7280';
    }
}
