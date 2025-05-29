<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\TaskResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskCollection;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;

class TaskController extends Controller
{
    public function index(Request $request): TaskCollection
    {
        $this->authorize('view-any', Task::class);

        $search = $request->get('search', '');

        $tasks = Task::search($search)
            ->latest()
            ->paginate();

        return new TaskCollection($tasks);
    }

    public function store(TaskStoreRequest $request): TaskResource
    {
        $this->authorize('create', Task::class);

        $validated = $request->validated();

        $task = Task::create($validated);

        return new TaskResource($task);
    }

    public function show(Request $request, Task $task): TaskResource
    {
        $this->authorize('view', $task);

        return new TaskResource($task);
    }

    public function update(TaskUpdateRequest $request, Task $task): TaskResource
    {
        $this->authorize('update', $task);

        $validated = $request->validated();

        $task->update($validated);

        return new TaskResource($task);
    }

    public function destroy(Request $request, Task $task): Response
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->noContent();
    }
}
