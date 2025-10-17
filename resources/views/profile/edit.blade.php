{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Profile Settings')

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Profile Settings</h2>

    @if (session('status') === 'profile-updated')
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            Profile updated successfully!
        </div>
    @endif

    <!-- Profile Information Form -->
    <div class="bg-white p-6 rounded-lg shadow mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h3>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', $user->name) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email', $user->email) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Update Profile
                </button>
            </div>
        </form>
    </div>

    <!-- Back to Dashboard -->
    <div class="text-center">
        @if(auth()->user()->hasRole('super_admin'))
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Admin Dashboard
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Dashboard
            </a>
        @endif
    </div>
</div>
@endsection
