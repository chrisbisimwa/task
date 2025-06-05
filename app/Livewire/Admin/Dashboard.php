<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Employee;
use App\Models\AccessToken;
use App\Models\NotificationLog;
use App\Models\Feedback;
use App\Models\Task;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $week;

    public function mount()
    {
        $this->week = now()->format('o-\WW');
    }

    public function render()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $tokensThisWeek = AccessToken::where('created_at', '>=', $startOfWeek)->get();

        $employeesWithAccess = $tokensThisWeek->whereNotNull('used_at')->pluck('employee_id')->unique();
        $employeesWithoutAccess = $tokensThisWeek->whereNull('used_at')->pluck('employee_id')->unique();

        $notifThisWeek = NotificationLog::where('created_at', '>=', $startOfWeek)->get();
        $notifSuccess = $notifThisWeek->where('status', 'success')->count();
        $notifFailed = $notifThisWeek->where('status', 'failed')->count();

       /*  $feedbacks = Feedback::with('employee')
            ->orderByDesc('created_at')
            ->take(5)
            ->get(); */

        $urgentTasks = Task::where('status', '!=', 'done')
            ->with('employee')
            ->take(5)
            ->get();

        return view('livewire.admin.dashboard', [
            'countWithAccess' => $employeesWithAccess->count(),
            'countWithoutAccess' => $employeesWithoutAccess->count(),
            'notifSuccess' => $notifSuccess,
            'notifFailed' => $notifFailed,
            /* 'feedbacks' => $feedbacks, */
            'urgentTasks' => $urgentTasks,
        ]);
    }
}
