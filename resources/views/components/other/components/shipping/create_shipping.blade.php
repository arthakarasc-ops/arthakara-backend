@extends('main.main')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8 text-center sm:text-left">
        <a href="{{ route('shippings.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-cyan-600 transition-colors mb-4 group text-sm">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Shipping Methods
        </a>
        <h1 class="text-3xl font-bold text-slate-900">Add New Shipping</h1>
        <p class="text-slate-500">Configure a new delivery service for your customers.</p>
    </div>

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-100 text-rose-700 rounded-2xl mb-6">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('shippings.create') }}" method="POST" class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-sm space-y-6">
        @csrf
        
        <div class="space-y-2">
            <label for="name" class="block text-sm font-bold text-slate-700 ml-1">Service Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. JNE Regular, J&T Express"
                   class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 transition-all @error('name') border-rose-500 @enderror" required>
            @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label for="price" class="block text-sm font-bold text-slate-700 ml-1">Fixed Shipping Price (Rp)</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">Rp</span>
                <input type="number" name="price" id="price" value="{{ old('price') }}" placeholder="10000"
                       class="w-full p-4 pl-12 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 transition-all @error('price') border-rose-500 @enderror" required>
            </div>
            @error('price') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-2">
            <label for="description" class="block text-sm font-bold text-slate-700 ml-1">Description (Optional)</label>
            <textarea name="description" id="description" rows="4" placeholder="Estimation, terms, or notes..."
                      class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 transition-all">{{ old('description') }}</textarea>
        </div>

        <div class="pt-4 flex flex-col sm:flex-row gap-4">
            <a href="{{ route('shippings.index') }}" class="flex-1 text-center py-4 rounded-2xl font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="flex-[2] bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-cyan-500/20 transition-all transform active:scale-[0.98]">
                Create Shipping Method
            </button>
        </div>
    </form>
</div>
@endsection
