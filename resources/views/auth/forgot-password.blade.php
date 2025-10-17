{{-- resources/views/auth/forgot-password.blade.php - COMPACT FORGOT PASSWORD --}}
@extends('layouts.auth')

@section('title', 'Forgot Password - EduManagement System')

@section('content')
<div class="forgot-wrapper" style="min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 15px;">
    <div class="forgot-container" style="width: 100%; max-width: 380px; padding: 10px;">
        <div class="forgot-card" style="background: white; border-radius: 16px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); overflow: hidden; animation: slideUp 0.5s ease-out;">

            <!-- Compact Header Section -->
            <div class="forgot-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px 20px; text-align: center; position: relative;">
                <!-- Decorative elements -->
                <div style="position: absolute; top: -30px; left: -30px; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 50%; opacity: 0.5;"></div>
                <div style="position: absolute; bottom: -20px; right: -20px; width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 50%; opacity: 0.4;"></div>

                <!-- Logo and Title -->
                <div style="position: relative; z-index: 2;">
                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.15); border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                        <i class="fas fa-key" style="font-size: 26px; color: white;"></i>
                    </div>
                    <h1 style="color: white; font-size: 22px; font-weight: 600; margin: 0 0 6px 0; letter-spacing: -0.5px;">Forgot Password?</h1>
                    <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 14px; font-weight: 300;">No worries, we'll send you reset instructions</p>
                </div>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div style="background: linear-gradient(45deg, #28a745, #20c997); color: white; padding: 15px 20px; text-align: center; margin: 0;">
                    <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                    <strong>Email Sent!</strong> {{ session('status') }}
                </div>
            @endif

            <!-- Compact Form Section -->
            <div class="forgot-body" style="padding: 25px 20px 20px;">

                @if (!session('status'))
                    <!-- Instructions -->
                    <div style="background: #f8f9fa; border-left: 4px solid #667eea; padding: 15px; margin-bottom: 20px; border-radius: 0 8px 8px 0;">
                        <p style="margin: 0; color: #495057; font-size: 13px; line-height: 1.5;">
                            <i class="fas fa-info-circle text-primary" style="margin-right: 6px; color: #667eea;"></i>
                            Enter your email address and we'll send you a link to reset your password.
                        </p>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email Field -->
                    <div class="form-group" style="margin-bottom: 20px;">
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
                            autofocus
                            style="
                                width: 100%;
                                padding: 12px 15px;
                                border: 2px solid #e2e8f0;
                                border-radius: 10px;
                                font-size: 14px;
                                transition: all 0.3s ease;
                                background: #f8fafc;
                                color: #2d3748;
                                box-sizing: border-box;
                            "
                            placeholder="Enter your email address"
                            onfocus="this.style.borderColor='#667eea'; this.style.background='white'; this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)'"
                            onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc'; this.style.boxShadow='none'"
                        >
                        @error('email')
                            <div style="color: #e53e3e; font-size: 12px; margin-top: 4px;">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    @if (session('status'))
                        <!-- Success State - Show different content -->
                        <div style="text-align: center; margin-bottom: 20px;">
                            <div style="width: 80px; height: 80px; background: linear-gradient(45deg, #28a745, #20c997); border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-paper-plane" style="font-size: 32px; color: white;"></i>
                            </div>
                            <h3 style="color: #2d3748; font-size: 18px; margin-bottom: 8px;">Check Your Email</h3>
                            <p style="color: #718096; font-size: 13px; line-height: 1.5; margin-bottom: 0;">
                                We've sent a password reset link to <strong>{{ old('email', request('email')) }}</strong>
                            </p>
                        </div>

                        <!-- Resend Button -->
                        <button
                            type="submit"
                            style="
                                width: 100%;
                                padding: 12px;
                                background: transparent;
                                border: 2px solid #667eea;
                                border-radius: 10px;
                                color: #667eea;
                                font-size: 14px;
                                font-weight: 600;
                                cursor: pointer;
                                transition: all 0.3s ease;
                            "
                            onmouseover="this.style.background='#667eea'; this.style.color='white'"
                            onmouseout="this.style.background='transparent'; this.style.color='#667eea'"
                        >
                            <i class="fas fa-redo" style="margin-right: 8px;"></i>
                            Resend Email
                        </button>
                    @else
                        <!-- Send Reset Link Button -->
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
                            <i class="fas fa-paper-plane" style="margin-right: 8px;"></i>
                            Send Reset Link
                        </button>
                    @endif
                </form>

                <!-- Divider -->
                <div style="margin: 20px 0; text-align: center; position: relative;">
                    <div style="height: 1px; background: #e2e8f0;"></div>
                    <span style="background: white; padding: 0 15px; color: #718096; font-size: 12px; position: absolute; top: -18px; left: 50%; transform: translateX(-50%);">
                        Remember your password?
                    </span>
                </div>

                <!-- Back to Login -->
                <div style="text-align: center;">
                    <a href="{{ route('login') }}" style="color: #667eea; text-decoration: none; font-weight: 500; font-size: 14px; padding: 10px 20px; border: 2px solid #667eea; border-radius: 10px; display: inline-block; transition: all 0.3s ease;" onmouseover="this.style.background='#667eea'; this.style.color='white'" onmouseout="this.style.background='transparent'; this.style.color='#667eea'">
                        <i class="fas fa-arrow-left" style="margin-right: 6px;"></i>
                        Back to Login
                    </a>
                </div>

                @if (!session('status'))
                    <!-- Help Section -->
                    <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; text-align: center;">
                        <p style="margin: 0 0 8px 0; color: #6c757d; font-size: 12px; font-weight: 600;">
                            <i class="fas fa-question-circle" style="margin-right: 4px;"></i>
                            NEED HELP?
                        </p>
                        <p style="margin: 0; color: #6c757d; font-size: 11px; line-height: 1.4;">
                            Contact your administrator or check your spam folder if you don't receive the email within 5 minutes.
                        </p>
                    </div>
                @endif
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

    .forgot-wrapper {
        background-attachment: fixed;
    }

    /* Mobile responsive */
    @media (max-width: 480px) {
        .forgot-container {
            padding: 10px !important;
            max-width: 340px !important;
        }

        .forgot-header {
            padding: 20px 15px !important;
        }

        .forgot-body {
            padding: 20px 15px 15px !important;
        }
    }

    input:focus {
        outline: none !important;
    }

    /* Success animation */
    .forgot-card:has([style*="Check Your Email"]) {
        animation: successPulse 0.6s ease-out;
    }

    @keyframes successPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }
</style>
@endsection
