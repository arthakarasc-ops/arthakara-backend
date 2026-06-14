@extends('main.main')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('products.index') }}" class="text-slate-500 hover:text-cyan-600 transition-colors flex items-center gap-2 mb-4 group">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Products
        </a>
        <h1 class="text-3xl font-bold text-slate-900">Create New Product</h1>
        <p class="text-slate-500">Fill in the details to add a new masterpiece to your collection.</p>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-red-700 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <form id="product-form" action="{{ route('products.create') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf

        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information Card -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Basic Information
                </h2>

                <div>
                    <label for="name" class="block text-slate-700 font-semibold mb-2">Product Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all outline-none @error('name') border-red-500 @enderror" 
                        placeholder="Ex: Arthakara Signature Candle" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-slate-700 font-semibold mb-2">Description</label>
                    <textarea id="description" name="description" rows="5" 
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all outline-none @error('description') border-red-500 @enderror" 
                        placeholder="Describe your product..." required>{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Pricing & Inventory Card -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Pricing & Inventory
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="price" class="block text-slate-700 font-semibold mb-2">Price (IDR)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">Rp</span>
                            <input type="number" id="price" name="price" value="{{ old('price') }}"
                                class="w-full p-3 pl-10 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all outline-none @error('price') border-red-500 @enderror" 
                                placeholder="0" required>
                        </div>
                        @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="stock" class="block text-slate-700 font-semibold mb-2">Total Stock</label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0"
                            class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all outline-none @error('stock') border-red-500 @enderror" required>
                        @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Organization Card -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <h2 class="text-lg font-bold text-slate-800">Organization</h2>

                <div class="space-y-4">
                    <div>
                        <label for="collection_id" class="block text-slate-700 text-sm font-semibold mb-1.5">Collection</label>
                        <select id="collection_id" name="collection_id" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-cyan-500 transition-all cursor-pointer @error('collection_id') border-red-500 @enderror" required>
                            <option value="" disabled selected>Select a collection</option>
                            @foreach(\App\Models\Collection::all() as $collection)
                                <option value="{{ $collection->id }}" {{ old('collection_id') == $collection->id ? 'selected' : '' }}>{{ $collection->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="type_ids" class="block text-slate-700 text-sm font-semibold mb-1.5">Product Types (Select at least 1)</label>
                        <select id="type_ids" name="type_ids[]" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-cyan-500 transition-all cursor-pointer @error('type_ids') border-red-500 @enderror" multiple required>
                            @foreach(\App\Models\Type::all() as $type)
                                <option value="{{ $type->id }}" {{ in_array($type->id, old('type_ids', [])) ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Hold CTRL/CMD to select multiple</p>
                    </div>
                </div>
            </div>

            {{-- Media Card --}}
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-800">Product Media</h2>
                    <span class="text-xs text-slate-400">Upload hingga 2 foto produk</span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Slot Gambar 1 (Wajib) --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Foto 1 <span class="text-rose-500">*</span>
                        </label>
                        <div id="preview-container-1" class="hidden relative aspect-square rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 group">
                            <img id="preview-img-1" src="#" alt="Preview" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button type="button" onclick="resetSlot(1)"
                                    class="bg-rose-500 text-white p-1.5 rounded-full shadow-lg hover:bg-rose-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                        <div id="placeholder-1" class="relative border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center hover:border-cyan-400 transition-colors cursor-pointer aspect-square flex flex-col items-center justify-center group">
                            <input id="image_upload_1" type="file" name="image" accept="image/*" required
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="handleSlotUpload(this, 1)">
                            <svg class="w-8 h-8 text-slate-300 mb-1 group-hover:text-cyan-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-[10px] font-semibold text-slate-400">Upload Foto 1</p>
                        </div>
                        @error('image') <p class="text-red-500 text-xs mt-1 text-center">{{ $message }}</p> @enderror
                        <p id="compress-status-1" class="text-xs text-center text-emerald-600 font-semibold mt-1"></p>
                    </div>

                    {{-- Slot Gambar 2 (Opsional) --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Foto 2 <span class="text-slate-400 font-normal">(opsional)</span>
                        </label>
                        <div id="preview-container-2" class="hidden relative aspect-square rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 group">
                            <img id="preview-img-2" src="#" alt="Preview" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button type="button" onclick="resetSlot(2)"
                                    class="bg-rose-500 text-white p-1.5 rounded-full shadow-lg hover:bg-rose-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                        <div id="placeholder-2" class="relative border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center hover:border-cyan-400 transition-colors cursor-pointer aspect-square flex flex-col items-center justify-center group">
                            <input id="image_upload_2" type="file" name="image_2" accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="handleSlotUpload(this, 2)">
                            <svg class="w-8 h-8 text-slate-300 mb-1 group-hover:text-cyan-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-[10px] font-semibold text-slate-400">Upload Foto 2</p>
                        </div>
                        @error('image_2') <p class="text-red-500 text-xs mt-1 text-center">{{ $message }}</p> @enderror
                        <p id="compress-status-2" class="text-xs text-center text-emerald-600 font-semibold mt-1"></p>
                    </div>
                </div>
            </div>

            {{-- Attributes Card --}}
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h2 class="text-lg font-bold text-slate-800">Attributes <span class="text-rose-500 text-xs font-normal">*Required</span></h2>
                
                <div>
                    <label for="color_ids" class="block text-slate-700 text-sm font-semibold mb-1.5">Color (Select at least 1)</label>
                    <select id="color_ids" name="color_ids[]" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-cyan-500 transition-all cursor-pointer @error('color_ids') border-red-500 @enderror" multiple required>
                        @foreach(\App\Models\Color::all() as $color)
                            <option value="{{ $color->id }}" {{ in_array($color->id, old('color_ids', [])) ? 'selected' : '' }}>{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="scent_ids" class="block text-slate-700 text-sm font-semibold mb-1.5">Aroma/Scents <span class="text-slate-400 text-xs font-normal">(Optional)</span></label>
                    <select id="scent_ids" name="scent_ids[]" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-cyan-500 transition-all cursor-pointer @error('scent_ids') border-red-500 @enderror" multiple>
                        @foreach(\App\Models\Scent::all() as $scent)
                            <option value="{{ $scent->id }}" {{ in_array($scent->id, old('scent_ids', [])) ? 'selected' : '' }}>{{ $scent->name }} (+Rp{{ number_format($scent->extra_price) }})</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">Hold CTRL/CMD to select multiple</p>
                </div>
            </div>

            <button type="submit" class="w-full bg-slate-900 hover:bg-cyan-600 text-white font-bold py-4 rounded-3xl shadow-lg hover:shadow-cyan-500/30 transition-all duration-300">
                Publish Product
            </button>
        </div>
    </form>
</div>

<script>
    // ============================================================
    // CLIENT-SIDE IMAGE COMPRESSION
    // Kompres gambar di browser sebelum upload — fix 413 error
    // Max dimensi: 1200x1200px, Kualitas JPEG: 80%
    // ============================================================

    // Simpan blob gambar yang sudah dikompres
    const compressedBlobs = { 1: null, 2: null };

    function compressImage(file, maxSize, quality, callback) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                let w = img.width;
                let h = img.height;

                // Resize agar tidak melebihi maxSize
                if (w > maxSize || h > maxSize) {
                    if (w > h) {
                        h = Math.round(h * maxSize / w);
                        w = maxSize;
                    } else {
                        w = Math.round(w * maxSize / h);
                        h = maxSize;
                    }
                }

                canvas.width  = w;
                canvas.height = h;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, w, h);

                // Konversi ke Blob JPEG
                canvas.toBlob(function(blob) {
                    callback(blob, canvas.toDataURL('image/jpeg', quality));
                }, 'image/jpeg', quality);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function handleSlotUpload(input, slot) {
        const previewContainer = document.getElementById('preview-container-' + slot);
        const previewImg       = document.getElementById('preview-img-' + slot);
        const placeholder      = document.getElementById('placeholder-' + slot);
        const statusEl         = document.getElementById('compress-status-' + slot);

        if (!input.files || !input.files[0]) return;

        const file = input.files[0];

        // Tampilkan loading
        if (statusEl) statusEl.textContent = 'Mengompres...';
        placeholder.classList.add('hidden');

        compressImage(file, 1200, 0.80, function(blob, dataUrl) {
            // Simpan blob untuk form submission
            compressedBlobs[slot] = blob;

            // Tampilkan preview
            previewImg.src = dataUrl;
            previewContainer.classList.remove('hidden');

            // Update status
            const originalKB = Math.round(file.size / 1024);
            const compressedKB = Math.round(blob.size / 1024);
            if (statusEl) statusEl.textContent = `${originalKB}KB → ${compressedKB}KB`;

            // Clear input file asli agar tidak dikirim dua kali
            input.value = '';
        });
    }

    function resetSlot(slot) {
        const previewContainer = document.getElementById('preview-container-' + slot);
        const previewImg       = document.getElementById('preview-img-' + slot);
        const placeholder      = document.getElementById('placeholder-' + slot);
        const input            = document.getElementById('image_upload_' + slot);
        const statusEl         = document.getElementById('compress-status-' + slot);

        previewImg.src = '#';
        input.value = '';
        compressedBlobs[slot] = null;
        if (statusEl) statusEl.textContent = '';
        previewContainer.classList.add('hidden');
        placeholder.classList.remove('hidden');
    }

    // ============================================================
    // INTERCEPT FORM SUBMIT — kirim gambar kompres via FormData
    // ============================================================
    document.getElementById('product-form').addEventListener('submit', function(e) {
        // Jika tidak ada gambar yang dikompres, biarkan submit normal
        const hasCompressed = compressedBlobs[1] || compressedBlobs[2];
        if (!hasCompressed) return; // submit normal jika tidak ada blob

        e.preventDefault();

        const form   = e.target;
        const action = form.action;
        const formData = new FormData(form);

        // Ganti field image dengan blob yang sudah dikompres
        if (compressedBlobs[1]) {
            formData.set('image', compressedBlobs[1], 'image_1.jpg');
        }
        if (compressedBlobs[2]) {
            formData.set('image_2', compressedBlobs[2], 'image_2.jpg');
        }

        // Tombol submit — tampilkan loading
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';
        }

        // Kirim via fetch sebagai form POST
        fetch(action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            } else if (response.ok) {
                return response.text().then(html => {
                    // Jika ada redirect dalam response body, ikuti
                    document.open();
                    document.write(html);
                    document.close();
                });
            } else {
                return response.text().then(html => {
                    document.open();
                    document.write(html);
                    document.close();
                });
            }
        })
        .catch(err => {
            console.error('Upload error:', err);
            alert('Terjadi kesalahan saat upload. Silakan coba lagi.');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Publish Product';
            }
        });
    });
</script>
</div>
@endsection