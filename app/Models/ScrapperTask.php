<?php

namespace App\Models;

use App\Enums\ScrapperTaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapperTask extends Model
{
    use HasFactory;

    protected $primaryKey = 'task_id';

    public $timestamps = false;

    protected $casts = [
        'task_start' => 'datetime',
        'task_last_update' => 'datetime',
        'task_status' => ScrapperTaskStatus::class,
    ];

    protected $fillable = [
        'task_start',
        'task_last_update',
        'task_status',
        'task_type',
        'task_progress',
        'task_last_msg',
        'task_log_path',
    ];
}
