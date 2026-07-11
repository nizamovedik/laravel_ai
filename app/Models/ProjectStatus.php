<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectStatus extends Model
{
    protected $fillable = ['name', 'code', 'sort', 'is_default'];

    // Связь: один статус может быть у многих проектов
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
