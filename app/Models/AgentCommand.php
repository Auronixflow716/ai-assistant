<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentCommand extends Model
{
    protected $fillable = [
        'target_device',
        'command',
        'args',
        'status',
        'result',
        'user_id',
    ];

    protected $casts = [
        'args' => 'array',
    ];
}
