@extends('main.main')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Title -->
    <h1 class="text-center text-4xl font-extrabold text-slate-800 mb-10">
        Detail Order: <span class="text-cyan-600">#{{ $order->id }}</span>
    </h1>

    <!-- Alerts -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-emerald-700 text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-rose-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-rose-700 text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Status & Tracking Form -->
    <div class="mb-10 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-100">
            <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Update Status & Pengiriman
            </h2>
        </div>
        <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="p-6">
            @csrf
            @method('PATCH')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="type_id" class="block text-sm font-semibold text-slate-700 mb-2">Order Status</label>
                    <div class="relative">
                        <select id="type_id" name="status_id" required
                            class="appearance-none block w-full bg-white border border-slate-200 text-slate-700 py-3 px-4 pr-8 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-shadow">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}" {{ $order->status_id == $status->id ? 'selected' : '' }}>
                                    {{ ucfirst($status->name) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label for="tracking_number" class="block text-sm font-semibold text-slate-700 mb-2">Nomor Resi (Tracking Number)</label>
                    <div class="flex gap-2">
                        <input type="text" id="tracking_number" name="tracking_number" 
                            value="{{ $order->tracking_number }}" 
                            placeholder="e.g., JNE1234567890"
                            class="flex-1 bg-white border border-slate-200 text-slate-700 py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-shadow placeholder-slate-400">
                        @if($order->tracking_number)
                        <button type="button" 
                            onclick="event.preventDefault(); document.getElementById('resend-wa-form').submit();"
                            class="bg-slate-100 text-slate-600 px-4 py-3 rounded-lg hover:bg-slate-200 transition-colors flex items-center gap-2 font-medium text-sm whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Resend WA
                        </button>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-2 flex items-start gap-1">
                        <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Isi resi baru & set status "Shipped" untuk otomatis mengirimkan notifikasi WhatsApp ke customer.</span>
                    </p>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" id="submitBtn"
                    class="bg-cyan-600 text-white px-6 py-2.5 rounded-lg hover:bg-cyan-700 font-medium transition-colors duration-200 shadow-md shadow-cyan-500/30 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span id="btnText">Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

    @if($order->tracking_number)
    <form id="resend-wa-form" action="{{ route('orders.resendWA', $order->id) }}" method="POST" class="hidden">
        @csrf
    </form>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        <!-- Order Info Summary Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden lg:col-span-1">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-100">
                <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Informasi Pesanan
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <p class="text-sm text-slate-500 font-medium mb-1">Tanggal Pesanan</p>
                    <p class="text-slate-800 font-semibold">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium mb-1">Status</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-cyan-50 text-cyan-700 border border-cyan-100">
                        {{ ucfirst($order->statuses->name) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium mb-1">Metode Pengiriman</p>
                    @if($order->courier_code)
                        <p class="text-slate-800 font-semibold">{{ strtoupper($order->courier_code) }} — {{ $order->courier_service ?? '-' }}</p>
                        <p class="text-slate-500 text-sm mt-1">Ongkir: <span class="font-semibold text-slate-700">Rp{{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</span></p>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-amber-50 text-amber-700 border border-amber-100">
                            Ambil Langsung (Take Away)
                        </span>
                        <p class="text-slate-500 text-sm mt-1">Ongkir: <span class="font-semibold text-slate-700">Rp0</span></p>
                    @endif
                </div>
                <div class="pt-4 border-t border-slate-100">
                    <p class="text-sm text-slate-500 font-medium mb-1">Total Belanja</p>
                    <p class="text-2xl font-bold text-emerald-600">Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Customer & Addresses Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden lg:col-span-2">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-100">
                <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Informasi Pelanggan
                </h2>
            </div>
            <div class="p-6">
                <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <p class="text-sm text-slate-500 font-medium mb-1">Email Pelanggan</p>
                        <p class="text-slate-800 font-semibold">{{ $order->users?->email ?? 'Guest' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium mb-1">No. HP Akun</p>
                        <p class="text-slate-800 font-semibold">{{ $order->users?->phone_number ?? '-' }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 gap-8">
                    <!-- Shipping Address -->
                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4 border-b border-slate-200 pb-2">Alamat Pengiriman</h3>
                        <div class="space-y-2 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-700">Nama:</span> {{ $order->shippingAddresses->first_name ?? '-' }} {{ $order->shippingAddresses->last_name ?? '-' }}</p>
                            <p><span class="font-semibold text-slate-700">No. HP:</span> {{ $order->shippingAddresses->phone_number ?? '-' }}</p>
                            <p><span class="font-semibold text-slate-700">Alamat:</span> {{ $order->shippingAddresses->address ?? '-' }}</p>
                            <p><span class="font-semibold text-slate-700">Suite/Apt:</span> {{ $order->shippingAddresses->appartment_suite ?? '-' }}</p>
                            <p><span class="font-semibold text-slate-700">Kota:</span> {{ $order->shippingAddresses->city ?? '-' }}</p>
                            <p><span class="font-semibold text-slate-700">Provinsi:</span> {{ $order->shippingAddresses->province ?? '-' }}</p>
                            <p><span class="font-semibold text-slate-700">Kode Pos:</span> {{ $order->shippingAddresses->postal_code ?? '-' }}</p>
                            <p><span class="font-semibold text-slate-700">Negara:</span> {{ $order->shippingAddresses->country ?? '-' }}</p>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center gap-2">
        <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
        Item Pesanan
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach ($order->orderItems as $item)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-md hover:border-cyan-200 transition duration-300 flex flex-col overflow-hidden group">
                <div class="relative h-56 bg-slate-100 overflow-hidden">
                    <img src="{{ $item->productVariants->image_url ?? 'https://via.placeholder.com/150' }}"
                        alt="Product Image"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>

                <div class="p-5 flex-grow flex flex-col justify-between bg-white">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 mb-2 leading-tight">
                            {{ $item->productVariants->product->name ?? 'Unnamed Product' }}
                        </h3>

                        {{-- Warna / Color --}}
                        @if($item->productVariants && $item->productVariants->color)
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-xs text-slate-500 font-medium">Warna:</span>
                                <span class="text-xs font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-full">
                                    {{ $item->productVariants->color->name }}
                                </span>
                            </div>
                        @endif

                        {{-- Wangi / Scent --}}
                        <div class="mb-3">
                            <p class="text-xs text-slate-500 font-medium mb-1.5 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                                Wangi:
                            </p>
                            @if($item->resolved_scent_names && $item->resolved_scent_names->isNotEmpty())
                                <div class="flex flex-wrap gap-1">
                                    @foreach($item->resolved_scent_names as $scentName)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-100">
                                            {{ $scentName }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-slate-400 italic">Tidak ada pilihan wangi</span>
                            @endif
                        </div>

                        <div class="inline-block bg-slate-100 px-3 py-1 rounded-full text-xs font-semibold text-slate-600 mb-3">
                            Qty: {{ $item->quantity }}
                        </div>

                        {{-- Harga satuan --}}
                        <p class="text-xs text-slate-400">
                            Harga satuan: <span class="font-semibold text-slate-600">Rp{{ number_format($item->price_at_purchase, 0, ',', '.') }}</span>
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                        <span class="text-sm font-medium text-slate-500">Subtotal</span>
                        <span class="text-lg font-bold text-cyan-600">Rp{{ number_format($item->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('type_id');
        const trackingInput = document.getElementById('tracking_number');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');

        // Simpan nilai awal dari database saat halaman pertama kali diload
        const initialStatus = statusSelect.value;
        const initialTracking = trackingInput.value;

        function checkChanges() {
            const currentStatus = statusSelect.value;
            const currentTracking = trackingInput.value;
            
            const isStatusChanged = currentStatus !== initialStatus;
            const isTrackingChanged = currentTracking !== initialTracking;
            
            // Jika ada perubahan apapun
            if (isStatusChanged || isTrackingChanged) {
                submitBtn.disabled = false;
                
                // Jika tracking berubah dan tidak kosong, ganti teks tombol
                if (isTrackingChanged && currentTracking.trim() !== '') {
                    btnText.textContent = "Simpan & Kirim WA";
                } else {
                    btnText.textContent = "Simpan Perubahan";
                }
            } else {
                // Tidak ada perubahan sama sekali, disable button
                submitBtn.disabled = true;
                btnText.textContent = "Simpan Perubahan";
            }
        }

        // Listen for changes
        statusSelect.addEventListener('change', checkChanges);
        trackingInput.addEventListener('input', checkChanges);
        
        // Initial check on load
        checkChanges();
    });
</script>
@endsection