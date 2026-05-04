@extends('main.main')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6">Create New Status</h1>

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

    <form id="collection-form" action="{{ route('status.create') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Collection Name -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Status Name</label>
            <input type="text" name="name" value="{{ old('name') }}"
                class="w-full p-2 border rounded @error('name') border-red-500 @enderror" required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Create Status</button>

    </form>
</div>
@endsection