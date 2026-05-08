@extends('main.main')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('types.index') }}" class="text-slate-500 hover:text-cyan-600 transition-colors flex items-center gap-2 mb-4 group">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Types
        </a>
        <h1 class="text-3xl font-bold text-slate-900">Edit Type</h1>
        <p class="text-slate-500">Modify details for category "{{ $type->name }}".</p>
    </div>

    <form action="{{ route('types.update', ['typeId' => $type->id]) }}" method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-slate-700 font-semibold mb-2">Type Name</label>
            <input type="text" name="name" value="{{ old('name', $type->name) }}"
                class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all outline-none @error('name') border-red-500 @enderror" required>
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-4">
            <a href="{{ route('types.index') }}" class="flex-1 bg-slate-100 text-slate-600 font-bold py-4 rounded-2xl text-center hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="flex-[2] bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-cyan-500/20 transition-all duration-300">
                Update Type
            </button>
        </div>
    </form>
</div>
@endsection

