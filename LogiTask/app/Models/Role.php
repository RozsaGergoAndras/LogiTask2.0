<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Task_type;
use App\Models\User;

class Role extends Model
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'role_name',
    ];

    public function task_type()
    {
        return $this->hasMany(Task_type::class, 'assignable_role');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'role');
    }

}
