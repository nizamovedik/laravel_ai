<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskLabel extends Model
{
    protected $fillable = ['name'];

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_label_task');
    }
}
