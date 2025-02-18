<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task_content extends Model
{
    /** @use HasFactory<\Database\Factories\TaskContentFactory> */
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'task_id',
        'link',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
