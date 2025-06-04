<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Employee extends Model
{
    use HasFactory;
    use Searchable;
    use Notifiable;

    protected $fillable = ['name', 'phone', 'email', 'notification_channels'];

    protected $searchableFields = ['*'];

    protected $casts = [
        'notification_channels' => 'array',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function accessTokens()
    {
        return $this->hasMany(AccessToken::class);
    }

    
}
