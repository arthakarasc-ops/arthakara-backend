@extends('main.main')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6">Create New Product Variant</h1>

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

    <form id="variant-form" action="{{ route('variant.create', ['productId' => $productId]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Color -->
        <div>
            <label for="color_id" class="block text-gray-700 font-semibold mb-2">Color</label>
            <select id="color_id" name="color_id" class="w-full p-2 border rounded @error('color_id') border-red-500 @enderror">
                <option value="" {{ old('color_id') == null ? 'selected' : '' }}>Select a color (optional)</option>
                @foreach(\App\Models\Color::all() as $color)
                    <option value="{{ $color->id }}" {{ old('color_id') == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                @endforeach
            </select>
            @error('color_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Size -->
        <div>
            <label for="size_id" class="block text-gray-700 font-semibold mb-2">Size</label>
            <select id="size_id" name="size_id" class="w-full p-2 border rounded @error('size_id') border-red-500 @enderror">
                <option value="" {{ old('size_id') == null ? 'selected' : '' }}>Select a size (optional)</option>
                @foreach(\App\Models\Size::all() as $size)
                    <option value="{{ $size->id }}" {{ old('size_id') == $size->id ? 'selected' : '' }}>{{ $size->name }}</option>
                @endforeach
            </select>
            @error('size_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Fabric -->
        <div>
            <label for="fabric_id" class="block text-gray-700 font-semibold mb-2">Fabric</label>
            <select id="fabric_id" name="fabric_id" class="w-full p-2 border rounded @error('fabric_id') border-red-500 @enderror">
                <option value="" {{ old('fabric_id') == null ? 'selected' : '' }}>Select a fabric (optional)</option>
                @foreach(\App\Models\Fabric::all() as $fabric)
                    <option value="{{ $fabric->id }}" {{ old('fabric_id') == $fabric->id ? 'selected' : '' }}>{{ $fabric->name }}</option>
                @endforeach
            </select>
            @error('fabric_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Stock -->
        <div>
            <label for="stock" class="block text-gray-700 font-semibold mb-2">Stock</label>
            <input id="stock" name="stock" type="number" value="{{ old('stock') }}"
                   class="w-full p-2 border rounded @error('stock') border-red-500 @enderror" required>
            @error('stock')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Image Upload -->
        <div>
            <label for="image_upload" class="block text-gray-700 font-semibold mb-2">Upload Image</label>
            <input id="image_upload" type="file" name="image" accept="image/*"
                   class="w-full p-2 border rounded @error('image') border-red-500 @enderror" required>
            @error('image')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Create Variant</button>
        </div>
    </form>
</div>
@endsection