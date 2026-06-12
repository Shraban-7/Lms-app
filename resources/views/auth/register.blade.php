@extends('layouts.app')

@section('title', 'Sign Up - Aether LMS')

@section('content')
    <div class="max-w-[480px] mx-auto my-12">
        <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-8 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300">
            <h2 class="font-extrabold text-2xl text-text-primary text-center mb-8">Get Started</h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-6">
                    <label class="block font-semibold mb-2 text-sm text-text-primary" for="name">Full Name</label>
                    <input class="w-full py-3 px-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" type="text" id="name" name="name" value="{{ old('name') }}"
                        required autofocus>
                    @error('name')
                        <span class="text-danger text-xs mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block font-semibold mb-2 text-sm text-text-primary" for="email">Email Address</label>
                    <input class="w-full py-3 px-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" type="email" id="email" name="email" value="{{ old('email') }}"
                        required>
                    @error('email')
                        <span class="text-danger text-xs mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block font-semibold mb-2 text-sm text-text-primary" for="role">Account Role</label>
                    <select class="w-full py-3 px-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)] cursor-pointer" name="role" id="role" required>
                        <option value="student">Student (Explore courses & earn certificates)</option>
                        <option value="instructor">Instructor (Build courses & request payouts)</option>
                    </select>
                    @error('role')
                        <span class="text-danger text-xs mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block font-semibold mb-2 text-sm text-text-primary" for="password">Password</label>
                        <input class="w-full py-3 px-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" type="password" id="password" name="password" required>
                        @error('password')
                            <span class="text-danger text-xs mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-semibold mb-2 text-sm text-text-primary" for="password_confirmation">Confirm Password</label>
                        <input class="w-full py-3 px-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" type="password" id="password_confirmation" name="password_confirmation"
                            required>
                    </div>
                </div>

                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300">
                    Create Account
                </button>
            </form>

            <p class="text-center mt-6 text-text-secondary text-sm">
                Already have an account? <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline">Log In</a>
            </p>
        </div>
    </div>
@endsection
