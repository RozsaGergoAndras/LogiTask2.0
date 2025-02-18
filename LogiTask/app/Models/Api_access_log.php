<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Api_access_log extends Model
{
    /** @use HasFactory<\Database\Factories\ApiAccessLogFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'ip_address',
        'user_agent',
        'route',
        'method',
        'request_data',
        'user_id'
    ];

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
