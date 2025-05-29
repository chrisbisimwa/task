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
    ];

    protected $searchableFields = ['*'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
