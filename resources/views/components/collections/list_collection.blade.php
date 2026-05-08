@extends('main.main')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Collections</h1>
            <p class="text-slate-500 text-sm">Organize your products into curated groups</p>
        </div>
        <a href="{{ route('create-collection') }}" class="w-full sm:w-auto bg-cyan-600 hover:bg-cyan-700 text-white px-5 py-2.5 rounded-xl font-semibold flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add New Collection
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($collections as $collection)
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500 overflow-hidden flex flex-col group">
                <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                    <img src="{{ $collection->image_url ?? 'https://via.placeholder.com/300' }}" alt="{{ $collection->name }}"
                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                </div>

                <div class="p-5 flex flex-col flex-grow">
                    <a href="{{ route('collections.products', ['collectionId' => $collection->id]) }}" class="text-lg font-bold text-slate-800 hover:text-cyan-600 transition-colors line-clamp-1 mb-4">{{ $collection->name }}</a>
                    
                    <div class="mt-auto grid grid-cols-2 gap-2">
                        <a href="{{ route('collections.edit', ['collectionId' => $collection->id]) }}"
                           class="bg-slate-50 hover:bg-cyan-50 text-slate-600 hover:text-cyan-700 font-semibold py-2.5 rounded-xl transition-all duration-200 text-center flex items-center justify-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit
                        </a>
                        <form action="{{ route('collections.delete', $collection->id) }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus koleksi ini?');" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full bg-slate-50 hover:bg-red-50 text-slate-600 hover:text-red-700 font-semibold py-2.5 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                <p class="text-slate-400 font-medium">No collections found.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection