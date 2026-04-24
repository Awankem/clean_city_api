<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CleanCity - Sign Up</title>
    
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
    
    <div class="w-full max-w-xl">
        <!-- Logo & Branding -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-primary rounded-2xl mx-auto flex items-center justify-center shadow-lg mb-4">
                <span class="material-symbols-outlined text-white text-3xl" data-icon="person_add">person_add</span>
            </div>
            <h1 class="text-3xl font-black text-primary font-heading tracking-tight">CleanCity</h1>
            <p class="text-on-surface-variant text-sm font-medium">Join the Digital Waste Management Effort</p>
        </div>

        <!-- Register Card -->
        <div class="bg-surface-container-lowest p-8 md:p-12 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-outline-variant/10">
            <h2 class="text-2xl font-bold text-on-surface font-heading mb-2">Create Admin Account</h2>
            <p class="text-xs text-on-surface-variant mb-8 uppercase tracking-widest font-black opacity-60">Complete the form to gain administrative access</p>
            
            <form action="{{ route('register') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf
                
                @if($errors->any())
                    <div class="col-span-full p-4 bg-tertiary-container/10 border border-tertiary/20 text-tertiary rounded-2xl flex items-start gap-3 animate-pulse">
                        <span class="material-symbols-outlined text-lg" data-icon="warning">warning</span>
                        <div class="text-xs font-bold leading-relaxed">
                            <span class="block mb-1">Registration encountered an error:</span>
                            <ul class="list-disc pl-4 opacity-80">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Full Name -->
                <div class="col-span-full">
                    <label for="name" class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest block mb-1.5 ml-1">Full Identity Name</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm" data-icon="badge">badge</span>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}" 
                               class="w-full bg-surface-container border-outline-variant/10 rounded-2xl pl-11 pr-4 py-3.5 text-sm font-medium text-on-surface focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                               placeholder="Hon. John Doe">
                    </div>
                </div>

                <!-- Email -->
                <div class="md:col-span-1">
                    <label for="email" class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest block mb-1.5 ml-1">Official Email</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm" data-icon="mail">mail</span>
                        <input type="email" name="email" id="email" required value="{{ old('email') }}" 
                               class="w-full bg-surface-container border-outline-variant/10 rounded-2xl pl-11 pr-4 py-3.5 text-sm font-medium text-on-surface focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                               placeholder="john@city.gov">
                    </div>
                </div>

                <!-- Phone -->
                <div class="md:col-span-1">
                    <label for="phone_number" class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest block mb-1.5 ml-1">Admin Contact (Optional)</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm" data-icon="call">call</span>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" 
                               class="w-full bg-surface-container border-outline-variant/10 rounded-2xl pl-11 pr-4 py-3.5 text-sm font-medium text-on-surface focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                               placeholder="+237 ...">
                    </div>
                </div>

                <!-- Password -->
                <div class="md:col-span-1">
                    <label for="password" class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest block mb-1.5 ml-1">Security Key</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm" data-icon="lock_open">lock_open</span>
                        <input type="password" name="password" id="password" required 
                               class="w-full bg-surface-container border-outline-variant/10 rounded-2xl pl-11 pr-4 py-3.5 text-sm font-medium text-on-surface focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                               placeholder="••••••••">
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="md:col-span-1">
                    <label for="password_confirmation" class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest block mb-1.5 ml-1">Confirm Key</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm" data-icon="lock">lock</span>
                        <input type="password" name="password_confirmation" id="password_confirmation" required 
                               class="w-full bg-surface-container border-outline-variant/10 rounded-2xl pl-11 pr-4 py-3.5 text-sm font-medium text-on-surface focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="col-span-full pt-4">
                    <button type="submit" class="w-full bg-primary text-on-primary py-4 rounded-2xl text-base font-black tracking-tight shadow-lg shadow-primary/20 hover:opacity-90 active:scale-[0.98] transition-all">
                        INITIALIZE ADMIN SESSION
                    </button>
                    
                    <div class="mt-8 pt-6 border-t border-outline-variant/5 flex items-center justify-center gap-2">
                        <span class="text-sm text-on-surface-variant">Already an administrator?</span>
                        <a href="{{ route('login') }}" class="text-sm font-black text-primary hover:underline">Sign In Instead</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center mt-8 text-xs text-on-surface-variant font-medium">
            By creating an account, you agree to the Municipal Terms of Service. <br>
            Managed by Infrastructure & Environment Ministry
        </p>
    </div>

</body>
</html>
