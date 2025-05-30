<?php

namespace App\Livewire\Employees;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Task;
use Carbon\Carbon;


class Show extends Component
{
    public $employee;
    public $tasks;
    public $weeklyProgress;
    public $history;
    public $summary;

    public function mount($employeeId)
    {
        $this->employee = Employee::with('tasks')->findOrFail($employeeId);
        $this->loadTasks();
        $this->loadProgress();
        $this->loadHistory();
    }

    public function loadSummary()
    {
        $this->summary = [
            'total' => $this->tasks->count(),
            'done' => $this->tasks->where('status', 'done')->count(),
            'in_progress' => $this->tasks->where('status', 'pending')->count(),
            'late' => $this->tasks->where('due_date', '<', now())->count(),
        ];
    }


    public function loadTasks()
    {
        $this->tasks = Task::where('employee_id', $this->employee->id)
            ->where('due_week', now()->format('o-\WW'))
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function loadProgress()
    {
        $total = $this->tasks->count();
        $done = $this->tasks->where('status', 'done')->count();
        $this->weeklyProgress = $total > 0 ? round(($done / $total) * 100) : 0;
    }

    public function loadHistory()
    {
        $this->history = Task::where('employee_id', $this->employee->id)
            ->where('due_week', '<', now()->format('o-\WW')) // Toutes les semaines passées
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get()
            ->groupBy(function ($task) {
                return Carbon::parse($task->week)->format('Y-m-d');
            });
    }


    public function render()
    {
        return view('livewire.employees.show');
    }
}
