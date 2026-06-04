@extends('main.main')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('products.index') }}" class="text-slate-500 hover:text-cyan-600 transition-colors flex items-center gap-2 mb-4 group">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Products
        </a>
        <h1 class="text-3xl font-bold text-slate-900">Edit Product</h1>
        <p class="text-slate-500">Update your product details and maintain your storefront's quality.</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-red-700 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <form id="product-form" action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf
        @method('PUT')

        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info Card -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    General Information
                </h2>

                <div>
                    <label for="name" class="block text-slate-700 font-semibold mb-2">Product Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all outline-none @error('name') border-red-500 @enderror" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-slate-700 font-semibold mb-2">Description</label>
                    <textarea id="description" name="description" rows="5"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all outline-none @error('description') border-red-500 @enderror" required>{{ old('description', $product->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Inventory Card -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    Pricing & Inventory
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="price" class="block text-slate-700 font-semibold mb-2">Price (IDR)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">Rp</span>
                            <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}"
                                class="w-full p-3 pl-10 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all outline-none @error('price') border-red-500 @enderror" required step="0.01">
                        </div>
                        @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="stock" class="block text-slate-700 font-semibold mb-2">Current Stock</label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" min="0"
                            class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all outline-none @error('stock') border-red-500 @enderror" required>
                        @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Media Card -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4 text-center">
                <h2 class="text-lg font-bold text-slate-800 text-left">Product Image</h2>
                
                <div id="main-image-preview-container" class="relative aspect-square rounded-2xl overflow-hidden border border-slate-100 mb-4 group {{ $product->productUsageImages->count() > 0 ? '' : 'hidden' }}">
                    <img id="main-image-preview" src="{{ $product->productUsageImages->first()->image_url ?? '#' }}" alt="Preview" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <p class="text-white text-xs font-bold">New Preview</p>
                    </div>
                </div>

                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center hover:border-cyan-400 transition-colors group cursor-pointer relative">
                    <input id="image_upload" type="file" name="image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewMainImage(this)">
                    <div class="py-2">
                        <svg class="w-8 h-8 text-slate-400 mx-auto mb-1 group-hover:text-cyan-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-xs font-semibold text-slate-600">Replace Main Image</p>
                    </div>
                </div>
                @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Categories Card -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h2 class="text-lg font-bold text-slate-800">Organization</h2>
                
                <div>
                    <label for="collection_id" class="block text-slate-700 text-sm font-semibold mb-1.5">Collection</label>
                    <select id="collection_id" name="collection_id" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-cyan-500 transition-all cursor-pointer @error('collection_id') border-red-500 @enderror" required>
                        @foreach($collections as $collection)
                            <option value="{{ $collection->id }}" {{ old('collection_id', $product->collection_id) == $collection->id ? 'selected' : '' }}>{{ $collection->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="type_ids" class="block text-slate-700 text-sm font-semibold mb-1.5">Product Types (Select at least 1)</label>
                    <select id="type_ids" name="type_ids[]" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-cyan-500 transition-all cursor-pointer @error('type_ids') border-red-500 @enderror" multiple required>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ in_array($type->id, old('type_ids', $product->types ? $product->types->pluck('id')->toArray() : [])) ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">Hold CTRL/CMD to select multiple</p>
                </div>
            </div>

            <!-- Attributes Card -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h2 class="text-lg font-bold text-slate-800">Attributes <span class="text-rose-500 text-xs font-normal">*Required</span></h2>
                
                <div>
                    <label for="color_ids" class="block text-slate-700 text-sm font-semibold mb-1.5">Colors (Select at least 1)</label>
                    <select id="color_ids" name="color_ids[]" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-cyan-500 transition-all cursor-pointer @error('color_ids') border-red-500 @enderror" multiple required>
                        @foreach(\App\Models\Color::all() as $color)
                            <option value="{{ $color->id }}" {{ in_array($color->id, old('color_ids', $product->variants ? $product->variants->pluck('color_id')->toArray() : [])) ? 'selected' : '' }}>{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="scent_ids" class="block text-slate-700 text-sm font-semibold mb-1.5">Aroma/Scents (Select at least 1)</label>
                    <select id="scent_ids" name="scent_ids[]" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-cyan-500 transition-all cursor-pointer @error('scent_ids') border-red-500 @enderror" multiple required>
                        @foreach(\App\Models\Scent::all() as $scent)
                            <option value="{{ $scent->id }}" {{ in_array($scent->id, old('scent_ids', $product->scents ? $product->scents->pluck('id')->toArray() : [])) ? 'selected' : '' }}>{{ $scent->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Variant Management Card -->
            <div id="variants-section" class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h2 class="text-lg font-bold text-slate-800">Variant Management</h2>
                <p class="text-[10px] text-slate-400">Manage specific stock and images for each color.</p>
                
                <div class="space-y-4">
                    @foreach($product->variants as $variant)
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <div class="w-16 h-16 rounded-xl overflow-hidden bg-white border border-slate-200 shrink-0 relative group">
                                <img id="variant-preview-{{ $variant->color_id }}" src="{{ $variant->image_url }}" alt="" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                    <p class="text-white text-[8px] font-bold">Preview</p>
                                </div>
                            </div>
                            <div class="flex-grow space-y-3 w-full">
                                <div class="flex justify-between items-center">
                                    <p class="text-sm font-bold text-slate-700">{{ $variant->color->name ?? 'Unknown Color' }}</p>
                                    <span class="text-[10px] px-2 py-0.5 bg-slate-200 text-slate-600 rounded-full font-bold">ID: #{{ $variant->id }}</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Variant Stock</label>
                                        <input type="number" name="variant_stocks[{{ $variant->color_id }}]" value="{{ $variant->stock }}"
                                               class="w-full p-2 bg-white border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-cyan-500/20 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Variant Image</label>
                                        <input type="file" name="variant_images[{{ $variant->color_id }}]" accept="image/*" 
                                               onchange="previewVariantImage(this, '{{ $variant->color_id }}')"
                                               class="w-full text-[10px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-[10px] file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 cursor-pointer">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('products.index') }}" class="flex-1 bg-slate-100 text-slate-600 font-bold py-4 rounded-3xl text-center hover:bg-slate-200 transition-all">Cancel</a>
                <button type="submit" class="flex-[2] bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-4 rounded-3xl shadow-lg hover:shadow-cyan-500/30 transition-all duration-300">
                    Update Product
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function previewMainImage(input) {
        const preview = document.getElementById('main-image-preview');
        const container = document.getElementById('main-image-preview-container');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewVariantImage(input, colorId) {
        const preview = document.getElementById('variant-preview-' + colorId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
