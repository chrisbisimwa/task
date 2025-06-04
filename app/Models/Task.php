<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'employee_id',
        'name',
        'description',
        'status',
        'due_week',
        'progress',
        'parent_task_id',
    ];

    protected $searchableFields = ['*'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function childTasks()
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function overdueWeeksCount()
    {
        $count = 0;
        $task = $this;
        while ($task->parentTask) {
            $count++;
            $task = $task->parentTask;
        }
        return $count;
    }
}
