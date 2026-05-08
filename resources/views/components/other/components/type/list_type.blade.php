@extends('main.main')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Product Types</h1>
            <p class="text-slate-500 text-sm">Categorize your inventory by item type</p>
        </div>
        <a href="{{ route('create-type') }}" class="w-full sm:w-auto bg-cyan-600 hover:bg-cyan-700 text-white px-5 py-2.5 rounded-xl font-semibold flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add New Type
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($types as $type)
            <div id="type-{{ $type->id }}" class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900">{{ $type->name }}</h3>
                        <p class="text-xs text-slate-400">ID: #{{ $type->id }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 lg:opacity-0 group-hover:opacity-100 transition-opacity duration-200 lg:opacity-100">
                    <a href="{{ route('type.edit.form', ['typeId' => $type->id]) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </a>
                    <form action="{{ route('type.delete', ['typeId' => $type->id]) }}" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tipe ini?');">
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
                <p class="text-slate-400 font-medium">No types found.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection