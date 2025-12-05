<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // --- SECURITY CHECK: BLOCK FRANCHISE USERS ---

        // Find the user trying to reset password
        $user = User::where('email', $request->email)->first();

        // If user exists AND their role is 'franchise', BLOCK THEM.
        // This assumes the value in your database 'role' column is strictly 'franchise'
        if ($user && ($user->role === 'franchise' || $user->hasRole('franchise'))) {
            return back()->withErrors([
                'email' => 'Franchise accounts cannot reset passwords. Please contact Admin.'
            ]);
        }

        // --- END SECURITY CHECK ---

        // If NOT a franchise, proceed as normal for Admin/other users
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }

}
