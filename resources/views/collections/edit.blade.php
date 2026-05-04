@extends('main.main')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6">Edit Collection</h1>

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

    <form id="collection-form" action="{{ route('collections.update', $collection->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Collection Name -->
        <div>
            <label for="name" class="block text-gray-700 font-semibold mb-2">Collection Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $collection->name) }}"
                class="w-full p-2 border rounded @error('name') border-red-500 @enderror" required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Current Image Display -->
        @if($collection->image_url)
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Current Image</label>
                <img src="{{ $collection->image_url }}" alt="{{ $collection->name }}" class="w-48 h-48 object-cover rounded border">
            </div>
        @endif

        <!-- Image Upload -->
        <div>
            <label for="image_upload" class="block text-gray-700 font-semibold mb-2">Upload New Image (Optional)</label>
            <input id="image_upload" type="file" name="image" accept="image/*"
                class="w-full p-2 border rounded @error('image') border-red-500 @enderror">
            <p class="text-gray-500 text-sm mt-1">Leave empty to keep current image</p>
            @error('image')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="flex justify-between">
            <a href="{{ route('collections.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</a>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update Collection</button>
        </div>
    </form>
</div>
@endsection
