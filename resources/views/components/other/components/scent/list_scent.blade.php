@extends('main.main')

@section('content')
<div class="container mx-auto px-6 py-10">
        <div class="w-full flex justify-between items-center mb-5">
            <h1 class="text-4xl font-bold text-gray-800">My Scents (Wangi)</h1>
            <a href="{{ route('create-scent') }}" class="bg-green-600 hover:bg-green-700 transition duration-300 rounded-md px-6 py-2 text-white font-semibold flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create New Scent
            </a>
        </div>

        @if(session('success'))
            <div class="w-full bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="w-full bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @forelse($scents as $scent)
            <div id="scent-{{ $scent->id }}" class="w-full flex justify-between items-center border-2 border-blue-500 p-2 rounded-lg {{ $scent->is_active ? 'bg-blue-200' : 'bg-gray-200' }} mb-4">
                
                <div class="flex-grow flex items-center gap-4">
                    <div class="flex flex-col w-1/3">
                        <span class="p-1 px-2 font-medium">{{ $scent->name }}</span>
                    </div>
                    
                    <div class="flex flex-col w-1/4">
                        <span class="text-sm font-mono text-gray-600">Rp {{ number_format($scent->extra_price, 0, ',', '.') }}</span>
                    </div>

                    <a href="{{ route('scent.edit.form', ['scentId' => $scent->id]) }}" class="bg-yellow-500 hover:bg-yellow-600 transition duration-300 rounded-md px-5 py-1.5 text-white font-semibold">Edit</a>
                </div>

                <div class="flex items-center gap-2 ml-4">
                    <!-- TOGGLE ACTIVE FORM -->
                    <form action="{{ route('scent.toggle', ['scentId' => $scent->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="transition duration-300 rounded-md px-5 py-1 text-white font-semibold {{ $scent->is_active ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-600 hover:bg-green-700' }}">
                            {{ $scent->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>

                    <!-- DELETE FORM -->
                    <form action="{{ route('scent.delete', ['scentId' => $scent->id]) }}" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus wangi ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 transition duration-300 rounded-md px-5 py-1 text-white font-semibold">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-500 col-span-full mt-4">Tidak ada wangi ditemukan.</p>
        @endforelse
    </div>
</div>
@endsection
