<?php

namespace App\Livewire\Employees;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Task;
use Carbon\Carbon;
use Livewire\WithPagination;


class Show extends Component
{
    use WithPagination;
    public $employee;
    public $tasks;
    public $weeklyProgress;
    public $history;
    public $summary;
    public $weeks= [];
    public $selectedWeek, $selectedStatus;

   


   

    public function mount($employeeId)
    {
        $this->employee = Employee::with('tasks')->findOrFail($employeeId);
        $this->loadTasks();
        $this->loadProgress();
        $this->loadHistory();
        $this->loadWeeks();
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

    public function loadWeeks()
    {

        $this->weeks = $this->tasks->groupBy(function ($task) {
            return Carbon::parse($task->due_date)->format('o-\WW'); // Format pour l'année et la semaine
        })->map(function ($group) {
            $firstDueDate = Carbon::parse($group->first()->due_date);
            return [
                'week' => $firstDueDate->format('o-\WW'),
                'start_date' => $firstDueDate->copy()->startOfWeek()->format('d-m-Y'),
                'end_date' => $firstDueDate->copy()->endOfWeek()->format('d-m-Y'),
                'tasks' => $group->count(),
            
            ];
        })->values();

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
        $this->tasks = Task::where('employee_id', $this->employee->id)
            ->where('due_week', $this->selectedWeek ?? now()->format('o-\WW'))
            ->when($this->selectedStatus, function ($query) {
                return $query->where('status', $this->selectedStatus);
            })
            ->orderBy('created_at', 'asc')
            ->get();
        return view('livewire.employees.show');
    }
}
