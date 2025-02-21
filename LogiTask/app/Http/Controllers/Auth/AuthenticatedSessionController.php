<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\TaskDistributionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\Task;

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
    }

    /**
     * Handle logout and revoke the user's token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        $user->user_state = 1;
        $user->save();

        // Revoke the current user's token
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Logged out successfully']);
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
        $tasks = Task::where('worker',$user->id)->whereNot('state', 2);
        return !$tasks->isEmpty();
    }
}
