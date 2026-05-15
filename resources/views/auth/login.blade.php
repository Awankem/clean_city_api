<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CleanCity - Login</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Public+Sans:ital,wght@0,700;0,900;1,700&display=swap" rel="stylesheet">
    
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-surface-container-low min-h-screen flex items-center justify-center p-6 font-body">
    
    <div class="w-full max-w-md">
        <!-- Logo & Branding -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-white rounded-3xl mx-auto flex items-center justify-center shadow-lg mb-4 border border-outline-variant/10 p-2">
                <img src="{{ asset('img/logo.png') }}" alt="CleanCity Logo" class="w-full h-full object-contain">
            </div>
            <h1 class="text-3xl font-black text-primary font-heading tracking-tight">CleanCity</h1>
            <p class="text-on-surface-variant text-sm font-medium">Digital Waste Management Platform</p>
        </div>

        <!-- Login Card -->
        <div class="bg-surface-container-lowest p-8 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-outline-variant/10">
            <h2 class="text-xl font-bold text-on-surface font-heading mb-6">Administrator Sign In</h2>
            
            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                
                @if($errors->any())
                    <div class="p-3 bg-tertiary-container/10 border border-tertiary/20 text-tertiary rounded-xl flex items-center gap-2 animate-pulse">
                        <span class="material-symbols-outlined text-sm" data-icon="error">error</span>
                        <p class="text-xs font-bold">{{ $errors->first() }}</p>
                    </div>
                @endif

                <div>
                    <label for="email" class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest block mb-1.5 ml-1">Email Address</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm" data-icon="mail">mail</span>
                        <input type="email" name="email" id="email" required value="{{ old('email') }}" 
                               class="w-full bg-surface-container border-outline-variant/10 rounded-2xl pl-11 pr-4 py-3.5 text-sm font-medium text-on-surface focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                               placeholder="admin@cleancity.gov">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5 ml-1">
                        <label for="password" class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest block">Password</label>
                        <a href="#" class="text-[10px] font-bold text-primary uppercase tracking-widest hover:underline">Forgot?</a>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm" data-icon="lock">lock</span>
                        <input type="password" name="password" id="password" required 
                               class="w-full bg-surface-container border-outline-variant/10 rounded-2xl pl-11 pr-4 py-3.5 text-sm font-medium text-on-surface focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center gap-2 ml-1">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-outline-variant/30 text-primary focus:ring-primary">
                    <label for="remember" class="text-xs font-medium text-on-surface-variant">Stay signed in for 30 days</label>
                </div>

                <button type="submit" class="w-full bg-primary text-on-primary py-4 rounded-2xl text-sm font-black tracking-tight shadow-md shadow-primary/10 hover:opacity-90 active:scale-[0.98] transition-all">
                    SIGN INTO DASHBOARD
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center mt-8 text-xs text-on-surface-variant font-medium">
            Authorized Personnel Only. <br>
            © {{ date('Y') }} Municipal Waste Authority
        </p>
    </div>

</body>
</html>
