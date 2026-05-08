@extends('main.main')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <a href="{{ route('products.index') }}" class="text-slate-500 hover:text-cyan-600 transition-colors flex items-center gap-2 mb-2 group text-sm">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Products
            </a>
            <h1 class="text-3xl font-bold text-slate-900">Product Detail</h1>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
            <a href="{{ route('products.edit', $product->id) }}" 
               class="flex-1 sm:flex-none bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold py-2.5 px-6 rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit
            </a>
            <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini?');" class="flex-1 sm:flex-none">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold py-2.5 px-6 rounded-xl transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left: Image Gallery -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white p-2 rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="aspect-square rounded-2xl overflow-hidden bg-slate-50">
                    <img src="{{ $product->productUsageImages->first()->image_url ?? 'https://via.placeholder.com/600' }}" 
                         alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>
            </div>
        </div>

        <!-- Right: Info -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-100 shadow-sm">
                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="px-3 py-1 bg-cyan-50 text-cyan-700 text-[10px] font-bold uppercase tracking-widest rounded-full border border-cyan-100">
                        {{ $product->collections->name ?? 'Collection' }}
                    </span>
                    <span class="px-3 py-1 bg-slate-50 text-slate-600 text-[10px] font-bold uppercase tracking-widest rounded-full border border-slate-100">
                        {{ $product->types->name ?? 'Type' }}
                    </span>
                </div>
                
                <h2 class="text-3xl font-extrabold text-slate-900 mb-2">{{ $product->name }}</h2>
                <p class="text-2xl font-bold text-cyan-600 mb-6">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
                
                <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed">
                    {!! $product->description !!}
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Total Stock</p>
                        <p class="text-xl font-extrabold text-slate-900">{{ $product->stock }} Units</p>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Aroma/Scents</p>
                        <p class="text-xl font-extrabold text-slate-900">{{ $product->scents->count() }} Variasi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Variants Section -->
    <div class="space-y-6 pt-4">
        <div class="flex justify-between items-center">
            <h3 class="text-xl font-bold text-slate-900">Color Variants</h3>
            <a href="{{ route('products.edit', $product->id) }}#variants-section" class="text-cyan-600 hover:text-cyan-700 font-bold text-sm flex items-center gap-1 group">
                Manage Variants & Images
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($variants as $variant)
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden flex flex-col group">
                    <div class="relative aspect-square overflow-hidden bg-slate-100">
                        <img src="{{ $variant->image_url ?? 'https://via.placeholder.com/300' }}" 
                             alt="" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $variant->color->name ?? 'No Color' }}</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Stock: {{ $variant->stock }} Units</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection