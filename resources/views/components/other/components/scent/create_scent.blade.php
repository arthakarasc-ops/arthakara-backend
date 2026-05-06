@extends('main.main')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6">Create New Scent (Wangi)</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

        <!-- Error Message -->
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form id="scent-form" action="{{ route('scent.create') }}" method="POST">
        @csrf

        <!-- Scent Name -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Scent Name</label>
            <input type="text" name="name" value="{{ old('name') }}"
                class="w-full p-2 border rounded @error('name') border-red-500 @enderror" required placeholder="Contoh: Vanilla">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Extra Price -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Extra Price (Rp)</label>
            <input type="number" name="extra_price" value="{{ old('extra_price', 0) }}" min="0"
                class="w-full p-2 border rounded @error('extra_price') border-red-500 @enderror" required>
            <small class="text-gray-500">Biarkan 0 jika tidak ada harga tambahan untuk wangi ini.</small>
            @error('extra_price')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Create Scent</button>

    </form>
</div>
@endsection
