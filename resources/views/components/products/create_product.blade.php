@extends('main.main')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-6">Create New Product</h1>

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

    <form id="product-form" action="{{ route('products.create') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Product Name -->
        <div>
            <label for="name" class="block text-gray-700 font-semibold mb-2">Product Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
                class="w-full p-2 border rounded @error('name') border-red-500 @enderror" required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Price -->
        <div>
            <label for="price" class="block text-gray-700 font-semibold mb-2">Price</label>
            <input type="number" id="price" name="price" value="{{ old('price') }}"
                class="w-full p-2 border rounded @error('price') border-red-500 @enderror" required>
            @error('price')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-gray-700 font-semibold mb-2">Description</label>
            <textarea id="description" name="description" class="w-full p-2 border rounded @error('description') border-red-500 @enderror" required>{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Collection -->
        <div>
            <label for="collection_id" class="block text-gray-700 font-semibold mb-2">Collection</label>
            <select id="collection_id" name="collection_id" class="w-full p-2 border rounded @error('collection_id') border-red-500 @enderror" required>
                <option value="" disabled selected>Select a collection</option>
                @foreach(\App\Models\Collection::all() as $collection)
                    <option value="{{ $collection->id }}" {{ old('collection_id') == $collection->id ? 'selected' : '' }}>{{ $collection->name }}</option>
                @endforeach
            </select>
            @error('collection_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Type -->
        <div>
            <label for="type_id" class="block text-gray-700 font-semibold mb-2">Type</label>
            <select id="type_id" name="type_id" class="w-full p-2 border rounded @error('type_id') border-red-500 @enderror" required>
                <option value="" disabled selected>Select a type</option>
                @foreach(\App\Models\Type::all() as $type)
                    <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>
            @error('type_id')
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

        <!-- Colors -->
        <div>
            <label for="color_ids" class="block text-gray-700 font-semibold mb-2">Colors (Max 1)</label>
            <select id="color_ids" name="color_ids[]" class="w-full p-2 border rounded @error('color_ids') border-red-500 @enderror">
                <option value="" disabled>Select a color</option>
                @foreach(\App\Models\Color::all() as $color)
                    <option value="{{ $color->id }}" {{ in_array($color->id, old('color_ids', [])) ? 'selected' : '' }}>{{ $color->name }}</option>
                @endforeach
            </select>
            @error('color_ids')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Flavor Variants -->
        <div>
            <label for="flavor_variant_ids" class="block text-gray-700 font-semibold mb-2">Flavor Variants (Max 2)</label>
            <select id="flavor_variant_ids" name="flavor_variant_ids[]" class="w-full p-2 border rounded @error('flavor_variant_ids') border-red-500 @enderror" multiple>
                @foreach(\App\Models\Variant::all() as $variant)
                    <option value="{{ $variant->id }}" {{ in_array($variant->id, old('flavor_variant_ids', [])) ? 'selected' : '' }}>{{ $variant->name }} ({{ $variant->type }})</option>
                @endforeach
            </select>
            <small class="text-gray-500">Pilih maksimal 2 varian rasa/wangi</small>
            @error('flavor_variant_ids')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Create Product</button>
        </div>
    </form>
</div>
@endsection