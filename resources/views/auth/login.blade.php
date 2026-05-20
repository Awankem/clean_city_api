@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
<div class="admin-card p-8 md:p-10 shadow-lg">
    <h2 class="text-xl font-bold text-on-surface font-heading mb-1">Administrator sign in</h2>
    <p class="text-sm text-on-surface-variant mb-6">Access the CleanCity admin dashboard</p>

    <form action="{{ route('login') }}" method="POST" class="space-y-5">
        @csrf

        @if($errors->any())
            <div class="flex items-center gap-2 p-3 rounded-xl bg-tertiary/10 border border-tertiary/20 text-tertiary text-sm font-semibold" role="alert">
                <span class="material-symbols-outlined text-lg">error</span>
                {{ $errors->first() }}
            </div>
        @endif

        <div>
            <label for="email" class="admin-label">Email</label>
            <div class="relative mt-1.5">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">mail</span>
                <input type="email" name="email" id="email" required value="{{ old('email') }}"
                       class="admin-input pl-12" placeholder="admin@cleancity.gov">
            </div>
        </div>

        <div>
            <div class="flex justify-between items-center">
                <label for="password" class="admin-label">Password</label>
            </div>
            <div class="relative mt-1.5">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">lock</span>
                <input type="password" name="password" id="password" required class="admin-input pl-12" placeholder="••••••••">
            </div>
        </div>

        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="remember" class="rounded border-outline-variant/30 text-primary focus:ring-primary/30">
            <span class="text-sm text-on-surface-variant">Stay signed in</span>
        </label>

        <button type="submit" class="admin-btn-primary w-full py-3.5 text-base">Sign in</button>

        <p class="text-center text-sm text-on-surface-variant pt-4 border-t border-outline-variant/10">
            New administrator?
            <a href="{{ route('register') }}" class="font-bold text-primary hover:underline">Create account</a>
        </p>
    </form>
</div>
@endsection
