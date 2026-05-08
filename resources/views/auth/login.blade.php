<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <title>Arthakara — Admin Access</title>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .input-focus-glow:focus {
            box-shadow: 0 0 20px rgba(34, 211, 238, 0.1);
        }
    </style>
</head>
<body class="bg-[#0c0c0e] text-slate-200 antialiased overflow-hidden">
    <!-- Abstract Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-cyan-500/5 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[50%] h-[50%] bg-blue-600/5 rounded-full blur-[120px]"></div>
    </div>

    <main class="relative min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-5xl grid lg:grid-cols-2 gap-16 items-center">
            
            <!-- Left Side: Branding -->
            <div class="hidden lg:block space-y-8 animate-in fade-in slide-in-from-left duration-1000">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-[10px] font-bold uppercase tracking-[0.2em] text-cyan-400">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-cyan-500"></span>
                    </span>
                    Administrative Portal
                </div>
                <h1 class="text-8xl font-extrabold tracking-tighter leading-none text-white">
                    Artha <br> <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500">Kara.</span>
                </h1>
                <p class="text-slate-400 text-lg max-w-md leading-relaxed font-light">
                    Elevating your storefront management with precision and elegance. Authenticate to proceed.
                </p>
                <div class="flex gap-4 pt-4">
                    <div class="w-12 h-[1px] bg-slate-800 self-center"></div>
                    <span class="text-slate-500 text-xs uppercase tracking-widest font-semibold italic">Since 2024</span>
                </div>
            </div>

            <!-- Right Side: Login Form -->
            <div class="w-full max-w-md mx-auto lg:ml-auto animate-in fade-in slide-in-from-right duration-1000">
                <div class="glass p-8 sm:p-12 rounded-[40px] shadow-2xl relative overflow-hidden group">
                    <!-- Subtle inner glow -->
                    <div class="absolute inset-0 bg-gradient-to-br from-white/[0.02] to-transparent pointer-events-none"></div>

                    <div class="relative z-10 space-y-8">
                        <div class="space-y-2">
                            <h2 class="text-2xl font-bold text-white tracking-tight">Sign In</h2>
                            <p class="text-slate-500 text-sm">Enter your credentials to access the terminal.</p>
                        </div>

                        @if($errors->any())
                            <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl">
                                @foreach($errors->all() as $error)
                                    <p class="text-rose-400 text-xs font-medium">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('login.process') }}" method="POST" class="space-y-6">
                            @csrf
                            <div class="space-y-2">
                                <label for="email" class="text-[10px] uppercase tracking-widest font-bold text-slate-500 ml-1">Identity (Email)</label>
                                <div class="relative group">
                                    <input type="email" name="email" id="email" required
                                        class="w-full bg-white/[0.03] border border-white/10 rounded-2xl py-4 px-6 text-white placeholder-slate-600 outline-none focus:border-cyan-500/50 focus:bg-white/[0.05] transition-all input-focus-glow"
                                        placeholder="admin@arthakara.com">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="password" class="text-[10px] uppercase tracking-widest font-bold text-slate-500 ml-1">Secret Key (Password)</label>
                                <input type="password" name="password" id="password" required
                                    class="w-full bg-white/[0.03] border border-white/10 rounded-2xl py-4 px-6 text-white placeholder-slate-600 outline-none focus:border-cyan-500/50 focus:bg-white/[0.05] transition-all input-focus-glow"
                                    placeholder="••••••••">
                            </div>

                            <button type="submit" 
                                class="w-full bg-white text-black font-bold py-5 rounded-2xl hover:bg-cyan-400 hover:text-black transition-all duration-300 transform active:scale-[0.98] shadow-lg shadow-white/5">
                                Authorize Access
                            </button>
                        </form>

                        <div class="pt-4 text-center">
                            <p class="text-[10px] text-slate-600 font-bold uppercase tracking-[0.2em]">Protected by Arthakara Security</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer Decoration -->
    <div class="fixed bottom-12 left-12 hidden lg:block">
        <div class="flex items-center gap-3">
            <div class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></div>
            <span class="text-[10px] font-bold text-slate-700 tracking-widest uppercase">System Operational</span>
        </div>
    </div>
</body>
</html>