{{-- resources/views/auth/register.blade.php - COMPACT VERSION --}}
@extends('layouts.auth')

@section('title', 'Register - EduManagement System')

@section('content')
<div class="register-wrapper" style="min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 15px;">
    <div class="register-container" style="width: 100%; max-width: 420px; padding: 10px;">
        <div class="register-card" style="background: white; border-radius: 16px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); overflow: hidden; animation: slideUp 0.5s ease-out;">

            <!-- Compact Header Section -->
            <div class="register-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px 20px; text-align: center; position: relative;">
                <!-- Smaller decorative elements -->
                <div style="position: absolute; top: -30px; left: -30px; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%; opacity: 0.5;"></div>
                <div style="position: absolute; bottom: -20px; right: -20px; width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 50%; opacity: 0.4;"></div>

                <!-- Compact Logo and Title -->
                <div style="position: relative; z-index: 2;">
                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.15); border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                        <i class="fas fa-user-plus" style="font-size: 26px; color: white;"></i>
                    </div>
                    <h1 style="color: white; font-size: 22px; font-weight: 600; margin: 0 0 6px 0; letter-spacing: -0.5px;">Join EduManagement</h1>
                    <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 14px; font-weight: 300;">Create your account to get started</p>
                </div>
            </div>

            <!-- Compact Form Section -->
            <div class="register-body" style="padding: 25px 20px 20px;">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Compact Name Field -->
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label for="name" style="display: block; font-weight: 600; color: #2d3748; margin-bottom: 6px; font-size: 13px;">
                            <i class="fas fa-user" style="color: #667eea; margin-right: 6px;"></i>Full Name
                        </label>
                        <input
                            id="name"
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            style="
                                width: 100%;
                                padding: 10px 15px;
                                border: 2px solid #e2e8f0;
                                border-radius: 8px;
                                font-size: 14px;
                                transition: all 0.3s ease;
                                background: #f8fafc;
                                box-sizing: border-box;
                            "
                            placeholder="Enter your full name"
                            onfocus="this.style.borderColor='#667eea'; this.style.background='white'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                            onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc'; this.style.boxShadow='none'"
                        >
                        @error('name')
                            <div style="color: #e53e3e; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Compact Email Field -->
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label for="email" style="display: block; font-weight: 600; color: #2d3748; margin-bottom: 6px; font-size: 13px;">
                            <i class="fas fa-envelope" style="color: #667eea; margin-right: 6px;"></i>Email Address
                        </label>
                        <input
                            id="email"
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            style="
                                width: 100%;
                                padding: 10px 15px;
                                border: 2px solid #e2e8f0;
                                border-radius: 8px;
                                font-size: 14px;
                                transition: all 0.3s ease;
                                background: #f8fafc;
                                box-sizing: border-box;
                            "
                            placeholder="Enter your email"
                            onfocus="this.style.borderColor='#667eea'; this.style.background='white'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                            onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc'; this.style.boxShadow='none'"
                        >
                        @error('email')
                            <div style="color: #e53e3e; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Compact Password Fields Row -->
                    <div style="display: flex; gap: 10px; margin-bottom: 16px;">
                        <div style="flex: 1;">
                            <label for="password" style="display: block; font-weight: 600; color: #2d3748; margin-bottom: 6px; font-size: 13px;">
                                <i class="fas fa-lock" style="color: #667eea; margin-right: 6px;"></i>Password
                            </label>
                            <input
                                id="password"
                                type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                name="password"
                                required
                                style="
                                    width: 100%;
                                    padding: 10px 15px;
                                    border: 2px solid #e2e8f0;
                                    border-radius: 8px;
                                    font-size: 14px;
                                    transition: all 0.3s ease;
                                    background: #f8fafc;
                                    box-sizing: border-box;
                                "
                                placeholder="Password"
                                onfocus="this.style.borderColor='#667eea'; this.style.background='white'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                                onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc'; this.style.boxShadow='none'"
                            >
                        </div>

                        <div style="flex: 1;">
                            <label for="password_confirmation" style="display: block; font-weight: 600; color: #2d3748; margin-bottom: 6px; font-size: 13px;">
                                <i class="fas fa-key" style="color: #667eea; margin-right: 6px;"></i>Confirm
                            </label>
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                style="
                                    width: 100%;
                                    padding: 10px 15px;
                                    border: 2px solid #e2e8f0;
                                    border-radius: 8px;
                                    font-size: 14px;
                                    transition: all 0.3s ease;
                                    background: #f8fafc;
                                    box-sizing: border-box;
                                "
                                placeholder="Confirm"
                                onfocus="this.style.borderColor='#667eea'; this.style.background='white'; this.style.boxShadow='0 0 0 3px rgba(102, 126, 234, 0.1)'"
                                onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc'; this.style.boxShadow='none'"
                            >
                        </div>
                    </div>

                    @error('password')
                        <div style="color: #e53e3e; font-size: 12px; margin-bottom: 16px;">{{ $message }}</div>
                    @enderror

                    <!-- Compact Terms -->
                    <div style="margin-bottom: 18px;">
                        <div style="display: flex; align-items: flex-start;">
                            <input
                                type="checkbox"
                                name="terms"
                                id="terms"
                                required
                                style="margin-right: 10px; margin-top: 3px; transform: scale(1.1); accent-color: #667eea;"
                            >
                            <label for="terms" style="color: #718096; font-size: 13px; line-height: 1.4;">
                                I agree to the <a href="#" style="color: #667eea;">Terms</a> and <a href="#" style="color: #667eea;">Privacy Policy</a>
                            </label>
                        </div>
                    </div>

                    <!-- Compact Register Button -->
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
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(102, 126, 234, 0.3)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                    >
                        <i class="fas fa-user-plus" style="margin-right: 8px;"></i>
                        Create Account
                    </button>
                </form>

                <!-- Compact Divider -->
                <div style="margin: 18px 0; text-align: center; position: relative;">
                    <div style="height: 1px; background: #e2e8f0;"></div>
                    <span style="background: white; padding: 0 15px; color: #718096; font-size: 12px; position: absolute; top: -8px; left: 50%; transform: translateX(-50%);">
                        Already have an account?
                    </span>
                </div>

                <!-- Compact Login Link -->
                <div style="text-align: center;">
                    <a href="{{ route('login') }}" style="color: #667eea; text-decoration: none; font-weight: 500; font-size: 14px; padding: 10px 20px; border: 2px solid #667eea; border-radius: 10px; display: inline-block; transition: all 0.3s ease;" onmouseover="this.style.background='#667eea'; this.style.color='white'" onmouseout="this.style.background='transparent'; this.style.color='#667eea'">
                        <i class="fas fa-sign-in-alt" style="margin-right: 6px;"></i>
                        Sign In Instead
                    </a>
                </div>
            </div>

            <!-- Compact Footer -->
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
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mobile responsive */
    @media (max-width: 480px) {
        .register-container {
            padding: 10px !important;
            max-width: 360px !important;
        }

        .register-header {
            padding: 20px 15px !important;
        }

        .register-body {
            padding: 20px 15px 15px !important;
        }

        /* Stack password fields on mobile */
        div[style*="display: flex; gap: 10px"] {
            flex-direction: column !important;
        }

        div[style*="display: flex; gap: 10px"] > div {
            margin-bottom: 16px;
        }
    }

    input:focus {
        outline: none !important;
    }
</style>
@endsection
