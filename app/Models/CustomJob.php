<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomJob extends Model
{
    protected $fillable = [
        'name', 'class_name', 'method', 'parameters', 'status', 
        'priority', 'retry_count', 'max_retries', 'scheduled_at', 'error'
    ];

    protected $casts = [
        'parameters' => 'array', 
        'scheduled_at' => 'datetime',
    ];
}
