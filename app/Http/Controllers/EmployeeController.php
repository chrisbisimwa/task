<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\EmployeeStoreRequest;
use App\Http\Requests\EmployeeUpdateRequest;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', Employee::class);

        $search = $request->get('search', '');

        $employees = Employee::search($search)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('app.employees.index', compact('employees', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Employee::class);

        return view('app.employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Employee::class);

        $validated = $request->validated();

        $employee = Employee::create($validated);

        return redirect()
            ->route('employees.edit', $employee)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Employee $employee): View
    {
        $this->authorize('view', $employee);


        $week = $request->input('week');
        $status = $request->input('status');

        // Base query
        $tasksQuery = $employee->tasks()->orderBy('created_at', 'asc');

        // Filtre par semaine si sélectionnée
        if ($week !=='all' && $status == 'all') {
            $tasksQuery->where('due_week', $week);
        }
 
        // Filtre par statut si sélectionné
        if ($status !=='all' && $week == 'all') {
            $tasksQuery->where('status', $status);
        }

        if ($week !=='all' && $status !=='all') {
            // Filtre par semaine et statut si les deux sont sélectionnés
            $tasksQuery->where('due_week', $week)
                ->where('status', $status);
        }   

        $tasks = $tasksQuery->get();

        // Liste des semaines où il y a des tâches (pour le filtre)
        $allTasks = $employee->tasks()->get();
        $weeks = $allTasks->groupBy(function ($task) {
            return \Carbon\Carbon::parse($task->due_week)->format('o-\WW');
        })->map(function ($group) {
            $firstDueDate = \Carbon\Carbon::parse($group->first()->due_date);
            return [
                'semaine' => $firstDueDate->format('o-\WW'),
                'start_date' => $firstDueDate->copy()->startOfWeek()->format('d-m-Y'),
                'end_date' => $firstDueDate->copy()->endOfWeek()->format('d-m-Y'),
                'tasks' => $group->count(),
            ];
        })->values();


        return view('app.employees.show', compact(
            'employee',
            'tasks',
            'weeks'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Employee $employee): View
    {
        $this->authorize('update', $employee);

        return view('app.employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        EmployeeUpdateRequest $request,
        Employee $employee
    ): RedirectResponse {
        $this->authorize('update', $employee);

        $validated = $request->validated();

        $employee->update($validated);

        return redirect()
            ->route('employees.edit', $employee)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        Employee $employee
    ): RedirectResponse {
        $this->authorize('delete', $employee);

        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
