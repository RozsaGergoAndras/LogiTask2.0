<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiAccessLog;
use Illuminate\Support\Facades\Auth;
use App\Models\Api_access_log;

class LogApiAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Log API access
        //$user = Auth::user(); // Get the authenticated user
        //$user = $request->user();
        $user = auth('sanctum')->user();
        $userId = $user ? $user->id : null; // Get user ID if authenticated, else null

        // Log the access data
        Api_Access_Log::create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'route' => $request->path(),
            'method' => $request->method(),
            'request_data' => json_encode($request->all()), // Store the request data (if necessary)
            'user_id' => $userId,
        ]);

        // Proceed with the request
        return $next($request);
    }
}
