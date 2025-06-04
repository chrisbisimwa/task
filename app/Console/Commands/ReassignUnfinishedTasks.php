<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class ReassignUnfinishedTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:reassign-unfinished';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconduit automatiquement les tâches non terminées de la semaine précédente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $thisWeek = now()->format('o-\WW');               // Exemple : 2025-W23
        $lastWeek = now()->subWeek()->format('o-\WW');    // Exemple : 2025-W22

        $tasks = Task::where('due_week', $lastWeek)
                     ->where('status', '!=', 'done')
                     ->get();

        $count = 0;

        DB::beginTransaction();
        foreach ($tasks as $task) {
            // Vérification : la tâche existe-t-elle déjà pour cette semaine ?
            $exists = Task::where('employee_id', $task->employee_id)
                          ->where('name', $task->name)
                          ->where('description', $task->description)
                          ->where('due_week', $thisWeek)
                          ->exists();

            if ($exists) {
                continue; // On évite la duplication
            }
            Task::create([
                'employee_id' => $task->employee_id,
                'name' => $task->name,
                'description' => $task->description,
                'status' => $task->status,
                'progress'=> $task->progress,
                'due_week' => $thisWeek,
                'parent_task_id' => $task->id,
            ]);
            $count++;
        }
        DB::commit();

        $this->info("✅ $count tâche(s) reconduite(s) pour la semaine $thisWeek.");
    }
}
