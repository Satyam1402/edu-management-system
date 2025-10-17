{{-- resources/views/auth/reset-password.blade.php - COMPACT RESET PASSWORD --}}
@extends('layouts.auth')

@section('title', 'Reset Password - EduManagement System')

@section('content')
<div class="reset-wrapper" style="min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 15px;">
    <div class="reset-container" style="width: 100%; max-width: 400px; padding: 10px;">
        <div class="reset-card" style="background: white; border-radius: 16px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); overflow: hidden; animation: slideUp 0.5s ease-out;">

            <!-- Header Section -->
            <div class="reset-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px 20px; text-align: center; position: relative;">
                <div style="position: relative; z-index: 2;">
                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.15); border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-lock" style="font-size: 26px; color: white;"></i>
                    </div>
                    <h1 style="color: white; font-size: 22px; font-weight: 600; margin: 0 0 6px 0;">Reset Password</h1>
                    <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 14px; font-weight: 300;">Enter your new password below</p>
                </div>
            </div>

            <!-- Form Section -->
            <div class="reset-body" style="padding: 25px 20px 20px;">
                <form method="POST" action="{{ route('password.store') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Field (Hidden) -->
                    <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

                    <!-- New Password -->
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label for="password" style="display: block; font-weight: 600; color: #2d3748; margin-bottom: 6px; font-size: 13px;">
                            <i class="fas fa-key" style="color: #667eea; margin-right: 6px;"></i>New Password
                        </label>
                        <input
                            id="password"
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password"
                            required
                            style="
                                width: 100%;
                                padding: 12px 15px;
                                border: 2px solid #e2e8f0;
                                border-radius: 10px;
                                font-size: 14px;
                                transition: all 0.3s ease;
                                background: #f8fafc;
                                box-sizing: border-box;
                            "
                            placeholder="Enter new password"
                            onfocus="this.style.borderColor='#667eea'; this.style.background='white'"
                            onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc'"
                        >
                        @error('password')
                            <div style="color: #e53e3e; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="password_confirmation" style="display: block; font-weight: 600; color: #2d3748; margin-bottom: 6px; font-size: 13px;">
                            <i class="fas fa-lock" style="color: #667eea; margin-right: 6px;"></i>Confirm New Password
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            style="
                                width: 100%;
                                padding: 12px 15px;
                                border: 2px solid #e2e8f0;
                                border-radius: 10px;
                                font-size: 14px;
                                transition: all 0.3s ease;
                                background: #f8fafc;
                                box-sizing: border-box;
                            "
                            placeholder="Confirm new password"
                            onfocus="this.style.borderColor='#667eea'; this.style.background='white'"
                            onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc'"
                        >
                    </div>

                    <!-- Reset Button -->
                    <button
                        type="submit"
                        style="
                            width: 100%;
                            padding: 12px;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            border: none;
                            border-radius: 10px;
                            color: white;
                            font-size: 15px;
                            font-weight: 600;
                            cursor: pointer;
                            transition: all 0.3s ease;
                        "
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(102,126,234,0.3)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                    >
                        <i class="fas fa-check" style="margin-right: 8px;"></i>
                        Reset Password
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div style="background: #f8fafc; padding: 15px 20px; text-align: center; border-top: 1px solid #e2e8f0;">
                <p style="margin: 0; color: #718096; font-size: 11px;">
                    © 2025 EduManagement System
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    input:focus { outline: none !important; }
    @media (max-width: 480px) {
        .reset-container { padding: 10px !important; max-width: 340px !important; }
        .reset-header { padding: 20px 15px !important; }
        .reset-body { padding: 20px 15px 15px !important; }
    }
</style>
@endsection
