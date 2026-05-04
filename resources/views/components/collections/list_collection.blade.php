@extends('main.main')

@section('content')
<div class="container mx-auto px-6 py-10">
    <div class="flex flex-col md:flex-row justify-between items-center mb-10">
        <h1 class="text-4xl font-bold text-gray-800 mb-4 md:mb-0">My Collections</h1>
        <a href="{{ route('create-collection') }}"
            class="w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow transition-all duration-200 text-center">
            + Add New Collection
        </a>
    </div>

    {{-- Collection List --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($collections as $collection)
            <div class="bg-white rounded-xl shadow hover:shadow-lg transition duration-300 overflow-hidden flex flex-col">
                <div class="relative h-48 bg-gray-200">
                    <img src="{{ $collection->image_url ?? 'https://via.placeholder.com/300' }}" alt="Collection Image"
                         class="absolute inset-0 w-full h-full object-cover">
                </div>

                <div class="p-4 flex flex-col gap-2 flex-grow">
                    <a href="{{ route('collections.products', ['collectionId' => $collection->id]) }}" class="text-lg font-semibold text-gray-800 hover:underline">{{ $collection->name }}</a>
                </div>

                <div class="p-4 pt-0 flex flex-col gap-2">
                    <a href="{{ route('collections.edit', ['collectionId' => $collection->id]) }}"
                       class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded transition focus:ring-2 ring-blue-500 text-center flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('collections.delete', $collection->id) }}" method="POST"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus koleksi ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 rounded transition focus:ring-2 ring-red-500 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-500 col-span-full">Tidak ada koleksi ditemukan.</p>
        @endforelse
    </div>
</div>
@endsection