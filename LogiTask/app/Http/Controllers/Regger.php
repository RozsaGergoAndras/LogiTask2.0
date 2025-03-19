<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
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
}
