@extends('main.main')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Scents & Aromas</h1>
            <p class="text-slate-500 text-sm">Manage scent options and extra pricing</p>
        </div>
        <a href="{{ route('create-scent') }}" class="w-full sm:w-auto bg-cyan-600 hover:bg-cyan-700 text-white px-5 py-2.5 rounded-xl font-semibold flex items-center justify-center transition-all duration-200 shadow-sm hover:shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add New Scent
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($scents as $scent)
            <div id="scent-{{ $scent->id }}" class="bg-white rounded-3xl border {{ $scent->is_active ? 'border-slate-100 shadow-sm' : 'border-slate-200 bg-slate-50/50 opacity-80' }} p-6 transition-all duration-300 hover:shadow-md group">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl {{ $scent->is_active ? 'bg-cyan-50 text-cyan-600' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.691.387a6 6 0 01-3.86.517l-2.387-.477a2 2 0 00-1.022.547l-1.162 1.162a2 2 0 000 2.828l1.162 1.162a2 2 0 002.828 0l1.162-1.162a2 2 0 00.547-1.022l.477-2.387a6 6 0 01.517-3.86l.387-.691a6 6 0 00.517-3.86l-.477-2.387a2 2 0 00-.547-1.022l-1.162-1.162a2 2 0 00-2.828 0L3.336 4.498a2 2 0 000 2.828l1.162 1.162z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-lg">{{ $scent->name }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-cyan-600 font-bold text-sm">Rp {{ number_format($scent->extra_price, 0, ',', '.') }}</span>
                                <span class="text-slate-300">|</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full uppercase font-bold {{ $scent->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $scent->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <a href="{{ route('scent.edit.form', ['scentId' => $scent->id]) }}" class="bg-slate-50 hover:bg-cyan-50 text-slate-600 hover:text-cyan-700 py-2.5 rounded-xl text-center text-sm font-bold transition-all flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit
                    </a>
                    
                    <form action="{{ route('scent.toggle', ['scentId' => $scent->id]) }}" method="POST" class="w-full">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="w-full py-2.5 rounded-xl text-center text-sm font-bold transition-all flex items-center justify-center gap-1 {{ $scent->is_active ? 'bg-slate-50 hover:bg-orange-50 text-slate-600 hover:text-orange-600' : 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm' }}">
                            @if($scent->is_active)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Off
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                On
                            @endif
                        </button>
                    </form>

                    <form action="{{ route('scent.delete', ['scentId' => $scent->id]) }}" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus wangi ini?');" class="w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-slate-50 hover:bg-red-50 text-slate-600 hover:text-red-700 py-2.5 rounded-xl text-center text-sm font-bold transition-all flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                <p class="text-slate-400 font-medium">No scents found.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

