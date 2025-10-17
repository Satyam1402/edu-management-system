{{-- resources/views/auth/login.blade.php - IMPROVED SPACING VERSION --}}
@extends('layouts.auth')

@section('content')
<div class="login-wrapper" style="min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 15px;">
    <div class="login-container" style="width: 100%; max-width: 380px; padding: 10px;">
        <div class="login-card" style="background: white; border-radius: 16px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); overflow: hidden; animation: slideUp 0.5s ease-out;">

            <!-- Header Section -->
            <div class="login-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px 25px; text-align: center; position: relative;">
                <!-- Decorative elements -->
                <div style="position: absolute; top: -30px; left: -30px; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%; opacity: 0.5;"></div>
                <div style="position: absolute; bottom: -20px; right: -20px; width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 50%; opacity: 0.4;"></div>

                <!-- Logo and Title -->
                <div style="position: relative; z-index: 2;">
                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.15); border-radius: 50%; margin: 0 auto 18px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                        <i class="fas fa-graduation-cap" style="font-size: 26px; color: white;"></i>
                    </div>
                    <h1 style="color: white; font-size: 22px; font-weight: 600; margin: 0 0 8px 0; letter-spacing: -0.5px;">EduManagement</h1>
                    <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 14px; font-weight: 300;">Welcome back! Please sign in</p>
                </div>
            </div>

            <!-- Form Section with Better Spacing -->
            <div class="login-body" style="padding: 30px 25px 25px;">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Field with more space -->
                    <div class="form-group" style="margin-bottom: 22px;">
                        <label for="email" style="display: block; font-weight: 600; color: #2d3748; margin-bottom: 8px; font-size: 13px;">
                            <i class="fas fa-envelope" style="color: #667eea; margin-right: 8px;"></i>Email Address
                        </label>
                        <input
                            id="email"
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            autofocus
                            style="
                                width: 100%;
                                padding: 14px 16px;
                                border: 2px solid #e2e8f0;
                                border-radius: 10px;
                                font-size: 14px;
                                transition: all 0.3s ease;
                                background: #f8fafc;
                                color: #2d3748;
                                box-sizing: border-box;
                            "
                            placeholder="Enter your email"
                            onfocus="this.style.borderColor='#667eea'; this.style.background='white'; this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)'"
                            onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc'; this.style.boxShadow='none'"
                        >
                        @error('email')
                            <div style="color: #e53e3e; font-size: 12px; margin-top: 6px;">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password Field with more space -->
                    <div class="form-group" style="margin-bottom: 22px;">
                        <label for="password" style="display: block; font-weight: 600; color: #2d3748; margin-bottom: 8px; font-size: 13px;">
                            <i class="fas fa-lock" style="color: #667eea; margin-right: 8px;"></i>Password
                        </label>
                        <input
                            id="password"
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password"
                            required
                            autocomplete="current-password"
                            style="
                                width: 100%;
                                padding: 14px 16px;
                                border: 2px solid #e2e8f0;
                                border-radius: 10px;
                                font-size: 14px;
                                transition: all 0.3s ease;
                                background: #f8fafc;
                                color: #2d3748;
                                box-sizing: border-box;
                            "
                            placeholder="Enter your password"
                            onfocus="this.style.borderColor='#667eea'; this.style.background='white'; this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)'"
                            onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc'; this.style.boxShadow='none'"
                        >
                        @error('password')
                            <div style="color: #e53e3e; font-size: 12px; margin-top: 6px;">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password with more space -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; font-size: 13px;">
                        <div class="form-check" style="display: flex; align-items: center;">
                            <input
                                type="checkbox"
                                name="remember"
                                id="remember"
                                {{ old('remember') ? 'checked' : '' }}
                                style="margin-right: 10px; transform: scale(1.1); accent-color: #667eea;"
                            >
                            <label for="remember" style="color: #718096; user-select: none;">
                                Remember Me
                            </label>
                        </div>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" style="color: #667eea; text-decoration: none; font-weight: 500; transition: color 0.3s ease;">
                                Forgot Password?
                            </a>
                        @endif
                    </div>

                    <!-- Login Button with more space -->
                    <button
                        type="submit"
                        style="
                            width: 100%;
                            padding: 14px;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            border: none;
                            border-radius: 10px;
                            color: white;
                            font-size: 15px;
                            font-weight: 600;
                            cursor: pointer;
                            transition: all 0.3s ease;
                            margin-bottom: 8px;
                        "
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(102,126,234,0.3)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                    >
                        <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
                        Sign In to Dashboard
                    </button>
                </form>

                <!-- Divider with more space -->
                <div style="margin: 25px 0; text-align: center; position: relative;">
                    <div style="height: 1px; background: #e2e8f0;"></div>
                    <span style="background: white; padding: 0 18px; color: #718096; font-size: 12px; position: absolute; top: -16px; left: 50%; transform: translateX(-50%);">
                        New to EduManagement?
                    </span>
                </div>

                <!-- Register Link with more space -->
                <div style="text-align: center; margin-top: 20px;">
                    <a href="{{ route('register') }}" style="color: #667eea; text-decoration: none; font-weight: 500; font-size: 14px; padding: 12px 24px; border: 2px solid #667eea; border-radius: 10px; display: inline-block; transition: all 0.3s ease;" onmouseover="this.style.background='#667eea'; this.style.color='white'" onmouseout="this.style.background='transparent'; this.style.color='#667eea'">
                        <i class="fas fa-user-plus" style="margin-right: 8px;"></i>
                        Create Account
                    </a>
                </div>
            </div>

            <!-- Footer with consistent spacing -->
            <div style="background: #f8fafc; padding: 18px 25px; text-align: center; border-top: 1px solid #e2e8f0;">
                <p style="margin: 0; color: #718096; font-size: 11px;">
                    © 2025 EduManagement System
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-wrapper {
        background-attachment: fixed;
    }

    /* Mobile responsive */
    @media (max-width: 480px) {
        .login-container {
            padding: 10px !important;
            max-width: 340px !important;
        }

        .login-header {
            padding: 25px 20px !important;
        }

        .login-body {
            padding: 25px 20px 20px !important;
        }
    }

    input:focus {
        outline: none !important;
    }
</style>
@endsection
