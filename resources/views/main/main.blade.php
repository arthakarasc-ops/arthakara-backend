<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Arthakara - Admin</title>
    <style>
        .custom-scrollbar::-webkit-scrollbar { display: none; }
        .custom-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="{ sidebarOpen: false }">

    <!-- Mobile Header -->
    <header class="lg:hidden bg-white border-b border-slate-200 px-4 py-3 sticky top-0 z-30 flex items-center justify-between">
        <a href="{{ route('admin') }}" class="flex items-center gap-2">
            <img src="{{ asset('assets/Logo.png') }}" alt="Logo" class="h-8 w-auto">
            <span class="font-bold text-slate-900">Arthakara</span>
        </a>
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-slate-100 text-slate-600 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>
    </header>

    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <div 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-40 w-64 transition-transform duration-300 ease-in-out transform lg:relative lg:translate-x-0 flex-shrink-0"
        >
            @include('main.sidebar')
        </div>

        <!-- Overlay for mobile sidebar -->
        <div 
            x-show="sidebarOpen" 
            @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden"
            x-cloak
        ></div>

        <!-- Main content -->
        <main class="flex-1 overflow-y-auto custom-scrollbar relative">
            <div class="p-4 md:p-8 lg:p-10 max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Alpine.js for minimal interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>