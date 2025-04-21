<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    /*public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }*/

    public function update(Request $request)
    {
        try {
            $validated = $request->validateWithBag('updatePassword', [
                'current_password' => ['required'/*, 'current_password'*/],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success'=> false,'error'=> $e->errors()],status: 200);
        }
        
        if(!Hash::check($request->current_password, auth('sanctum')->user()->password)){
            return response()->json(['success'=> false,'error'=> 'Password incorrect!'],status: 200);
        }

        auth('sanctum')->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        //return response()->json(['success'=> true,'message'=> 'Password Updated!'],status: 200);
        return response()->json(
            ['success' => true, 'message' => 'Password Updated!'],
            200
        )->header('Access-Control-Allow-Origin', '*');
    }
}
