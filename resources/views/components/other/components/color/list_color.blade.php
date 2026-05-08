@extends('main.main')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Colors</h1>
            <p class="text-slate-500 text-sm">Manage product color attributes</p>
        </div>
        <a href="{{ route('create-color') }}" class="w-full sm:w-auto bg-cyan-600 hover:bg-cyan-700 text-white px-5 py-2.5 rounded-xl font-semibold flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add New Color
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($colors as $color)
            <div id="color-{{ $color->id }}" class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900">{{ $color->name }}</h3>
                        <p class="text-xs text-slate-400">ID: #{{ $color->id }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 lg:opacity-100">
                    <a href="{{ route('color.edit.form', ['colorId' => $color->id]) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </a>
                    <form action="{{ route('color.delete', ['colorId' => $color->id]) }}" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus warna ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                <p class="text-slate-400 font-medium">No colors found.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection