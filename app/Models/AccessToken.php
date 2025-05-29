<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccessToken extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['employee_id', 'token', 'expires_at'];

    protected $searchableFields = ['*'];

    protected $table = 'access_tokens';

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function isValid()
    {
        return now()->lt($this->expires_at);
    }
}
