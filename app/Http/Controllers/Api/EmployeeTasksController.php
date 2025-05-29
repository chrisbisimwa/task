<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Resources\TaskResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskCollection;

class EmployeeTasksController extends Controller
{
    public function index(Request $request, Employee $employee): TaskCollection
    {
        $this->authorize('view', $employee);

        $search = $request->get('search', '');

        $tasks = $employee
            ->tasks()
            ->search($search)
            ->latest()
            ->paginate();

        return new TaskCollection($tasks);
    }

    public function store(Request $request, Employee $employee): TaskResource
    {
        $this->authorize('create', Task::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'description' => ['nullable', 'max:255', 'string'],
            'status' => ['required', 'in:pending,in_progress,done'],
            'due_week' => ['required', 'max:255', 'string'],
        ]);

        $task = $employee->tasks()->create($validated);

        return new TaskResource($task);
    }
}
