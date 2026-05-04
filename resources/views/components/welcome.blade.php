@extends('main.main')

@section('content')
    <div class="flex items-center justify-center h-full">
        <div class="text-center">
            <h1 class="text-4xl font-bold mb-4">Halo 👋</h1>
            <p id="datetime" class="text-lg text-gray-600"></p>
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
