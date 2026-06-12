@extends('layouts.app')

@section('title', 'Create Course - Aether LMS')

@section('content')
<div class="max-w-[650px] mx-auto my-8">
    <div class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-6 md:p-8 shadow-sm dark:shadow-card hover:border-primary hover:shadow-glow dark:hover:shadow-glow hover:-translate-y-0.5 transition-all duration-300">
        <h2 class="text-2xl font-extrabold text-text-primary mb-1">Create a New Course</h2>
        <p class="text-text-secondary text-sm mb-6">Fill in basic details first, then add modules, video lessons, and quizzes in the curriculum builder.</p>
        
        <form method="POST" action="{{ route('builder.store') }}">
            @csrf
            
            <div class="mb-6">
                <label class="block font-semibold mb-2 text-sm text-text-primary" for="title">Course Title</label>
                <input class="w-full py-3 px-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" type="text" id="title" name="title" value="{{ old('title') }}" placeholder="e.g. Master React in 30 Days" required>
                @error('title')
                    <span class="text-danger text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block font-semibold mb-2 text-sm text-text-primary" for="description">Course Description</label>
                <textarea class="w-full py-3 px-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" id="description" name="description" rows="5" placeholder="Provide a detailed, high-level summary of what students will learn..." required>{{ old('description') }}</textarea>
                @error('description')
                    <span class="text-danger text-xs mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block font-semibold mb-2 text-sm text-text-primary" for="price">Price (USD)</label>
                    <input class="w-full py-3 px-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" type="number" step="0.01" id="price" name="price" value="{{ old('price', '0.00') }}" required>
                    @error('price')
                        <span class="text-danger text-xs mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block font-semibold mb-2 text-sm text-text-primary" for="thumbnail">Thumbnail Image URL</label>
                    <input class="w-full py-3 px-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-sm text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]" type="url" id="thumbnail" name="thumbnail" value="{{ old('thumbnail') }}" placeholder="https://unsplash.com/...">
                    @error('thumbnail')
                        <span class="text-danger text-xs mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <a href="{{ route('builder.index') }}" class="inline-flex items-center justify-center gap-1.5 px-6 py-3 bg-surface-solid border border-gray-200 dark:border-border text-text-primary rounded-lg font-semibold hover:bg-gray-100 dark:hover:bg-border hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">Cancel</a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300">Create & Proceed</button>
            </div>
        </form>
    </div>
</div>
@endsection
