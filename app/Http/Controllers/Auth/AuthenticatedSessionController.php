<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('welcome');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Retrieve the authenticated user
        $user = Auth::user();
        Log::info("login User Role: {$user->role}"); 

        // Check the user's role and redirect accordingly
        if ($user->role === 'admin') {
            // Redirect to the admin dashboard or admin-specific page
            Log::info("User Role: {$user->role}");  
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'staff') {
            // Redirect to the moderator dashboard or page
            return redirect()->route('staff.dashboard');
        } elseif ($user->role === 'user') {
            // Redirect to the general user dashboard
            Log::info("Redirecting to user dashboard");
            return redirect()->route('dashboard');
        }

        // Default redirect in case no specific role is found
        return redirect()->route('login')->with('error', 'Unauthorized');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

}
