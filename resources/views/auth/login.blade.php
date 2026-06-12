@extends('layouts.app')

@section('title', 'Login - Aether LMS')

@section('content')
    <div class="max-w-[450px] mx-auto my-16">
        <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-8 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-1 transition-all duration-300">
            <h2 class="font-extrabold text-2xl text-text-primary text-center mb-8">Welcome Back</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-6">
                    <label class="block font-semibold mb-2 text-sm text-text-primary" for="email">Email Address</label>
                    <input class="w-full py-3 px-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" type="email" id="email" name="email" value="{{ old('email') }}"
                        required autofocus>
                    @error('email')
                        <span class="text-danger text-xs mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block font-semibold mb-2 text-sm text-text-primary" for="password">Password</label>
                    <input class="w-full py-3 px-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" type="password" id="password" name="password" required>
                    @error('password')
                        <span class="text-danger text-xs mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center gap-2 mt-4 mb-6">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 cursor-pointer accent-primary rounded border-gray-300">
                    <label for="remember" class="text-sm cursor-pointer text-text-secondary">Remember Me</label>
                </div>

                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300">
                    Log In
                </button>
            </form>

            <p class="text-center mt-6 text-text-secondary text-sm">
                New to AetherLMS? <a href="{{ route('register') }}" class="text-primary font-semibold hover:underline">Create an account</a>
            </p>
        </div>
    </div>
@endsection
