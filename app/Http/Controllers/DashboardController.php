<?php
// app/Http/Controllers/DashboardController.php - FIXED VERSION
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Check if user exists and has roles
        if (!$user) {
            return redirect()->route('login');
        }

        // Get user's first role safely
        $userRole = $user->roles()->first();

        if (!$userRole) {
            // If user has no role, assign default role or redirect to error
            Auth::logout();
            return redirect()->route('login')->with('error', 'User has no assigned role. Please contact administrator.');
        }

        // Route based on role
        switch ($userRole->name) {
            case 'super_admin':
                return redirect()->route('admin.dashboard');

            case 'franchise':
                return redirect()->route('franchise.dashboard');

            default:
                // For any other role, redirect to a generic dashboard or login
                return redirect()->route('login')->with('error', 'Invalid user role.');
        }
    }
}
