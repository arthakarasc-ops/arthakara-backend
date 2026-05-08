@extends('main.main')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Edit Color</h1>
        <a href="{{ route('colors.get') }}" class="text-slate-500 hover:underline flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to List
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('color.update', ['colorId' => $color->id]) }}" method="POST" class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Color Name</label>
            <input type="text" name="name" value="{{ old('name', $color->name) }}" class="w-full p-2 border rounded focus:outline-none focus:border-blue-500 @error('name') border-red-500 @enderror" required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>



        <div class="flex justify-end gap-3">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-medium transition duration-200">Save Changes</button>
        </div>
    </form>
</div>
@endsection
