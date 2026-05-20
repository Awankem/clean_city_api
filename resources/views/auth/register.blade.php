@extends('layouts.auth')

@section('title', 'Create Account')
@section('auth_width', 'max-w-xl')

@section('content')
<div class="admin-card p-8 md:p-10 shadow-lg">
    <h2 class="text-2xl font-bold text-on-surface font-heading">Create admin account</h2>
    <p class="text-sm text-on-surface-variant mt-1 mb-6">Register to manage reports and municipal operations</p>

    <form action="{{ route('register') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @csrf

        @if($errors->any())
            <div class="md:col-span-2 p-4 rounded-xl bg-tertiary/10 border border-tertiary/20 text-tertiary text-sm">
                <div class="flex gap-2 font-semibold mb-1">
                    <span class="material-symbols-outlined">warning</span>
                    Please fix the following:
                </div>
                <ul class="list-disc pl-5 text-xs space-y-0.5 opacity-90">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="md:col-span-2">
            <label for="name" class="admin-label">Full name</label>
            <div class="relative mt-1.5">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">badge</span>
                <input type="text" name="name" id="name" required value="{{ old('name') }}" class="admin-input pl-12" placeholder="Your name">
            </div>
        </div>

        <div>
            <label for="email" class="admin-label">Email</label>
            <div class="relative mt-1.5">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">mail</span>
                <input type="email" name="email" id="email" required value="{{ old('email') }}" class="admin-input pl-12" placeholder="you@city.gov">
            </div>
        </div>

        <div>
            <label for="phone_number" class="admin-label">Phone (optional)</label>
            <div class="relative mt-1.5">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">call</span>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" class="admin-input pl-12" placeholder="+237 …">
            </div>
        </div>

        <div>
            <label for="password" class="admin-label">Password</label>
            <div class="relative mt-1.5">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">lock</span>
                <input type="password" name="password" id="password" required class="admin-input pl-12">
            </div>
        </div>

        <div>
            <label for="password_confirmation" class="admin-label">Confirm password</label>
            <div class="relative mt-1.5">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">lock_reset</span>
                <input type="password" name="password_confirmation" id="password_confirmation" required class="admin-input pl-12">
            </div>
        </div>

        <div class="md:col-span-2 pt-2">
            <button type="submit" class="admin-btn-primary w-full py-3.5 text-base">Create account</button>
            <p class="text-center text-sm text-on-surface-variant mt-6">
                Already registered?
                <a href="{{ route('login') }}" class="font-bold text-primary hover:underline">Sign in</a>
            </p>
        </div>
    </form>
</div>
@endsection

@section('footer')
By creating an account, you agree to municipal terms of service.
@endsection
