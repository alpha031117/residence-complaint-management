<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        // $userRole = Auth::user()->role;
        // Log::info("User Role: {$userRole}");  
        // Log::info("Role: {$role}");  

        // First, check if user is authenticated
        if (!Auth::check()) {
            return redirect('login');
        }

        // Then check user's role
        if (Auth::user()->role !== $role) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}