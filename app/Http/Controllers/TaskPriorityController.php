<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskPriorityResource;
use App\Models\TaskPriority;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskPriorityController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return TaskPriorityResource::collection(
            TaskPriority::orderBy('level', 'asc')->get()
        );
    }
}
