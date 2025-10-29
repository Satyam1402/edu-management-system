<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FranchiseMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access franchise area.');
        }

        $user = Auth::user();

        // Check if user has franchise role
        if ($user->role !== 'franchise') {
            abort(403, 'Access denied. This area is only for franchise users.');
        }

        // Check if user has a valid franchise_id
        if (!$user->franchise_id) {
            abort(403, 'Access denied. No franchise assigned to your account.');
        }

        return $next($request);
    }
}
