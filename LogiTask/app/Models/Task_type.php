<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Role;

class Task_type extends Model
{
    /** @use HasFactory<\Database\Factories\TaskTypeFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type_name',
        'assignable_role'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'assignable_role');
    }

    public function task()
    {
        return $this->hasMany(Task::class, 'task_type');
    }
}
