<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\TaskDistributionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\Task;
use Hash;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    protected $taskService;

    // Inject the service into the constructor
    public function __construct(TaskDistributionService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Handle login for API and return a token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success'=>false, 'error' => $e->getMessage()], 400);
        }
        

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if the user exists and the password matches
        if ($user && Hash::check($request->password, $user->password)) {
            // Create an API token for the user
            $token = $user->createToken('API Token')->plainTextToken;

            if($this->UserHasActiveTask($user)){
                $user->user_state = 3;  //beosztott
                $user->save();
            }
            else{
                $user->user_state = 2;  //szabad
                $user->save();
            }

            // Return the token in the response
            return response()->json(['success'=>true, 'token' => $token], 200);
        }
        return response()->json(['success'=>false, 'error' => 'Unauthorized'], 401);
    }
     
    /*public function store(Request $request)
    {
        // Validate
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check credentials
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            //generate API token
            $user = Auth::user();
            $token = $user->createToken('LogiTask')->plainTextToken;
            if($this->UserHasActiveTask($user)){
                $user->user_state = 3;  //beosztott
            }
            else{
                $user->user_state = 2;  //szabad
            }

            // Return token and user information
            return response()->json([
                'token' => $token,
                'user' => $user
            ], 200);
        }

        //authentication fails, return error
        return response()->json(['message' => 'Invalid credentials'], 401);
    }*/

    /**
     * Handle logout and revoke the user's token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        if($user == null){
            return response()->json(['success'=>false, 'message' => 'Invalid Token', 400]);
        }

        $user->user_state = 1;
        $user->save();

        // Revoke the current user's token
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['success'=>true, 'message' => 'Logged out successfully']);
    }
    /**
     * Display the login view.
     */
    /*public function create(): View
    {
        return view('auth.login');
    }*/

    /**
     * Handle an incoming authentication request.
     */
    /*public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }*/
    /**
     * Destroy an authenticated session.
     */
    /*public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }*/

    public function UserHasActiveTask($user){
        $tasks = Task::where('worker',$user->id)->whereNot('state', 2)->get();
        return !$tasks->isEmpty();
    }
}
