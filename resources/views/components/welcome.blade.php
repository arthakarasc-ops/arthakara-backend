@extends('main.main')

@section('content')
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-bold mb-2">Halo 👋 Admin</h1>
            <p id="datetime" class="text-lg text-slate-500 font-medium"></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card: Total Order -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-center">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <h3 class="text-slate-500 font-semibold text-sm uppercase tracking-wider">Total Orders</h3>
            </div>
            <p class="text-3xl font-bold text-slate-800">{{ number_format($totalOrder) }}</p>
        </div>

        <!-- Card: Total Product -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-center">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <h3 class="text-slate-500 font-semibold text-sm uppercase tracking-wider">Total Products</h3>
            </div>
            <p class="text-3xl font-bold text-slate-800">{{ number_format($totalProduct) }}</p>
        </div>

        <!-- Card: Total Income -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-center">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-slate-500 font-semibold text-sm uppercase tracking-wider">Total Income</h3>
            </div>
            <p class="text-3xl font-bold text-slate-800">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
        </div>
    </div>

    <script>
        function updateDateTime() {
            const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
            const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                        "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

            const now = new Date();
            const namaHari = hari[now.getDay()];
            const namaBulan = bulan[now.getMonth()];
            const tanggal = now.getDate();
            const tahun = now.getFullYear();
            const jam = now.toLocaleTimeString('id-ID');

            document.getElementById('datetime').innerText =
                `${namaHari}, ${tanggal} ${namaBulan} ${tahun} • ${jam}`;
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>
@endsection
