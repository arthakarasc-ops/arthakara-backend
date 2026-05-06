@extends('main.main')

@section('content')
<div class="container mx-auto px-6 py-10">
        <div class="w-full flex justify-between items-center mb-5">
            <h1 class="text-4xl font-bold text-gray-800">My Colors</h1>
            <a href="{{ route('create-color') }}" class="bg-green-600 hover:bg-green-700 transition duration-300 rounded-md px-6 py-2 text-white font-semibold flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create New Color
            </a>
        </div>
        @forelse($colors as $color)
            <div id="color-{{ $color->id }}" class="w-full flex justify-between items-center border-2 border-blue-500 p-2 rounded-lg bg-blue-200 mb-4">
                
                <div class="flex-grow flex items-center gap-4">
                    <div class="flex flex-col w-1/3">
                        <span class="p-1 px-2 font-medium">{{ $color->name }}</span>
                    </div>
                    
                    <div class="flex flex-col w-1/4">
                        <div class="flex items-center gap-2">
                            <input type="color" value="{{ $color->hex_code ?? '#000000' }}" class="h-8 w-8 cursor-pointer rounded border-0" disabled>
                            <span class="text-sm font-mono text-gray-600">{{ $color->hex_code ?? 'No hex' }}</span>
                        </div>
                    </div>

                    <a href="{{ route('color.edit.form', ['colorId' => $color->id]) }}" class="bg-yellow-500 hover:bg-yellow-600 transition duration-300 rounded-md px-5 py-1.5 text-white font-semibold">Edit</a>
                </div>

                <!-- DELETE FORM -->
                <form action="{{ route('color.delete', ['colorId' => $color->id]) }}" method="post" class="ml-4" onsubmit="return confirm('Apakah Anda yakin ingin menghapus warna ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 transition duration-300 rounded-md px-5 py-1 text-white font-semibold">Delete</button>
                </form>
            </div>
        @empty
            <p class="text-gray-500 col-span-full">Tidak ada warna ditemukan.</p>
        @endforelse
    </div>
</div>
@endsection