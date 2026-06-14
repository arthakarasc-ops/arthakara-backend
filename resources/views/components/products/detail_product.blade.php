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
            <form action="{{ route('products.delete', $product->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini?');" class="flex-1 sm:flex-none">
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
        <!-- Left: Image Gallery — Slider -->
        <div class="lg:col-span-5 space-y-4">
            @php
                $detailImages = $product->productUsageImages()->orderBy('id', 'asc')->get();
                $detailImageCount = $detailImages->count();
            @endphp
            <div class="bg-white p-2 rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="relative aspect-square rounded-2xl overflow-hidden bg-slate-50 group" id="detail-slider">

                    {{-- Slides wrapper --}}
                    <div id="detail-slides" class="flex h-full transition-transform duration-500 ease-out"
                         style="width: {{ max($detailImageCount, 1) * 100 }}%">
                        @forelse($detailImages as $img)
                            <div style="width: {{ 100 / max($detailImageCount, 1) }}%" class="h-full shrink-0">
                                <img src="{{ $img->image_url }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover">
                            </div>
                        @empty
                            <div class="w-full h-full shrink-0">
                                <img src="https://via.placeholder.com/600" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover">
                            </div>
                        @endforelse
                    </div>

                    @if($detailImageCount > 1)
                        {{-- Tombol Kiri --}}
                        <button onclick="detailPrev()" id="detail-btn-prev"
                            class="absolute left-3 top-1/2 -translate-y-1/2 bg-white/80 backdrop-blur-sm hover:bg-white text-slate-700 p-2.5 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 z-10">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        {{-- Tombol Kanan --}}
                        <button onclick="detailNext()" id="detail-btn-next"
                            class="absolute right-3 top-1/2 -translate-y-1/2 bg-white/80 backdrop-blur-sm hover:bg-white text-slate-700 p-2.5 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 z-10">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>

                        {{-- Dot Navigasi --}}
                        <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-2 z-10 pointer-events-none">
                            @for($di = 0; $di < $detailImageCount; $di++)
                                <button class="detail-dot pointer-events-auto rounded-full transition-all duration-300 {{ $di === 0 ? 'w-6 h-2.5 bg-cyan-500' : 'w-2.5 h-2.5 bg-white/60' }}"
                                        onclick="detailGoTo({{ $di }})"
                                        id="detail-dot-{{ $di }}">
                                </button>
                            @endfor
                        </div>
                    @endif
                </div>
            </div>

            {{-- Thumbnail Strip (hanya jika > 1 gambar) --}}
            @if($detailImageCount > 1)
                <div class="grid grid-cols-2 gap-2">
                    @foreach($detailImages as $thumbIdx => $img)
                        <button onclick="detailGoTo({{ $thumbIdx }})"
                                id="detail-thumb-{{ $thumbIdx }}"
                                class="aspect-square rounded-2xl overflow-hidden border-2 transition-all duration-300 {{ $thumbIdx === 0 ? 'border-cyan-500 opacity-100' : 'border-slate-200 opacity-60 hover:opacity-100' }}">
                            <img src="{{ $img->image_url }}" alt="Thumbnail {{ $thumbIdx + 1 }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right: Info -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-100 shadow-sm">
                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="px-3 py-1 bg-cyan-50 text-cyan-700 text-[10px] font-bold uppercase tracking-widest rounded-full border border-cyan-100">
                        {{ $product->collections->name ?? 'Collection' }}
                    </span>
                    @foreach($product->types as $type)
                    <span class="px-3 py-1 bg-slate-50 text-slate-600 text-[10px] font-bold uppercase tracking-widest rounded-full border border-slate-100">
                        {{ $type->name }}
                    </span>
                    @endforeach
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

<script>
(function () {
    const totalDetailImages = {{ max($detailImageCount, 1) }};
    if (totalDetailImages <= 1) return;

    const slidesWrapper = document.getElementById('detail-slides');
    const dots          = document.querySelectorAll('.detail-dot');
    let currentDetail   = 0;
    let autoTimer;

    function detailUpdateUI() {
        // Slide gambar
        const percent = (100 / totalDetailImages) * currentDetail;
        slidesWrapper.style.transform = 'translateX(-' + percent + '%)';

        // Update dots
        dots.forEach(function(dot, i) {
            if (i === currentDetail) {
                dot.classList.remove('w-2.5', 'h-2.5', 'bg-white/60');
                dot.classList.add('w-6', 'h-2.5', 'bg-cyan-500');
            } else {
                dot.classList.remove('w-6', 'bg-cyan-500');
                dot.classList.add('w-2.5', 'h-2.5', 'bg-white/60');
            }
        });

        // Update thumbnail border
        for (let t = 0; t < totalDetailImages; t++) {
            const thumb = document.getElementById('detail-thumb-' + t);
            if (!thumb) continue;
            if (t === currentDetail) {
                thumb.classList.remove('border-slate-200', 'opacity-60');
                thumb.classList.add('border-cyan-500', 'opacity-100');
            } else {
                thumb.classList.remove('border-cyan-500', 'opacity-100');
                thumb.classList.add('border-slate-200', 'opacity-60');
            }
        }
    }

    window.detailGoTo = function(index) {
        currentDetail = index;
        detailUpdateUI();
        resetAutoSlide();
    };

    window.detailNext = function() {
        currentDetail = (currentDetail + 1) % totalDetailImages;
        detailUpdateUI();
        resetAutoSlide();
    };

    window.detailPrev = function() {
        currentDetail = (currentDetail - 1 + totalDetailImages) % totalDetailImages;
        detailUpdateUI();
        resetAutoSlide();
    };

    function startAutoSlide() {
        autoTimer = setInterval(function () {
            currentDetail = (currentDetail + 1) % totalDetailImages;
            detailUpdateUI();
        }, 4000);
    }

    function resetAutoSlide() {
        clearInterval(autoTimer);
        startAutoSlide();
    }

    startAutoSlide();
})();
</script>
@endsection