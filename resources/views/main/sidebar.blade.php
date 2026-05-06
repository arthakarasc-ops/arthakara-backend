<aside class="w-64 bg-white shadow-md flex flex-col justify-between h-full flex-shrink-0 border-r border-slate-100 relative z-20">
    <div class="overflow-y-auto flex-1 custom-scrollbar">
        <div class="p-6 text-center">
            <a href="{{ route('admin') }}">
                <img src="{{ asset('assets/Logo.png') }}" alt="Arthakara Logo" class="mx-auto object-contain">
            </a>
        </div>
        <nav class="flex flex-col p-4 space-y-2 font-medium">
            <a href="{{ route('admin') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-600 hover:bg-slate-100 hover:text-cyan-600' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>
            <a href="{{ route('products.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('products.*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-600 hover:bg-slate-100 hover:text-cyan-600' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                Products
            </a>
            <a href="{{ route('collections.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('collections.*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-600 hover:bg-slate-100 hover:text-cyan-600' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Collections
            </a>
            <a href="{{ route('orders.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('orders.*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-600 hover:bg-slate-100 hover:text-cyan-600' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                Orders
            </a>
            
            <div class="pt-4 pb-2">
                <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Attributes</p>
            </div>

            <a href="{{ route('colors.get') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('colors.*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-600 hover:bg-slate-100 hover:text-cyan-600' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                Colors
            </a>
            <a href="{{ route('scent.get') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('scent.*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-600 hover:bg-slate-100 hover:text-cyan-600' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Scents
            </a>
            <a href="{{ route('type.get') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('type.*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-600 hover:bg-slate-100 hover:text-cyan-600' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                Types
            </a>
            <a href="{{ route('status.get') }}" class="flex items-center px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('status.*') ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'text-slate-600 hover:bg-slate-100 hover:text-cyan-600' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Statuses
            </a>
        </nav>
    </div>
    <div class="p-6">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors duration-200 group">
                <svg class="w-5 h-5 mr-2 transition-transform duration-200 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Logout
            </button>
        </form>
    </div>
</aside>
