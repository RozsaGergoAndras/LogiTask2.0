<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Task_type;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'assigner',
        'worker',
        'state',
        'state0date',
        'state1date',
        'state2date',
        'task_type',
        'description',
    ];

    public function task_type()
    {
        return $this->belongsTo(Task_type::class, 'task_type');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'assigner');
    }

    public function user1()
    {
        return $this->belongsTo(User::class, 'worker');
    }
}
