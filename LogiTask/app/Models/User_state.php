<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class User_state extends Model
{
    /** @use HasFactory<\Database\Factories\UserStateFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'state_name',
    ];

    public function user()
    {
        return $this->hasMany(User::class, 'state');
    }
}
