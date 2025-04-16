<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Task_type;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use DB;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

class Regger extends Controller
{
    public function store(Request $request)
    {
        $user = auth('sanctum')->user();
        if($user->role != 2){
            return response()->json(['success'=>false, 'error' => 'Access denied!'], 401);
        }

        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'password_confirmation' => 'required',
    
                'role' =>['required', 'int', 'exists:roles,id'],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success'=>false, 'error' => $e->getMessage()], 400);
        }
        

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        
        /*DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);*/

        return response()->json(['success'=>true, 'message' => 'User created!'], 200);
    }

    public function users(Request $request)
    {
        try {
            $validated = $request->validate([
                'task_type_id' => 'required|integer|exists:task_types,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $taskType = Task_type::find($validated['task_type_id']);

        if (!$taskType || !$taskType->assignable_role) {
            return response()->json(['message' => 'Assignable role not found for this task type.'], 404);
        }

        $users = User::where('role', $taskType->assignable_role)->get();

        return response()->json($users);
    }
}
