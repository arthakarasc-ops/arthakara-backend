@extends('main.main')

@section('content')
<div class="container mx-auto px-6 py-10">
    <div class="flex flex-col md:flex-row justify-between items-center mb-10">
        <h1 class="text-4xl font-bold text-gray-800 mb-4 md:mb-0">My Products</h1>

        <form method="GET" action="{{ route('products.index') }}" class="flex flex-col sm:flex-row items-center gap-4">
            <div class="relative w-full sm:w-auto">
                <select id="collection-filter" name="collection" onchange="this.form.submit()"
                        class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-blue-500 shadow">
                    <option value="">All Collections</option>
                    @foreach ($collections as $collection)
                        <option value="{{ $collection->id }}" {{ request('collection') == $collection->id ? 'selected' : '' }}>
                            {{ $collection->name }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>

            <div class="relative w-full sm:w-auto">
                <select id="type-filter" name="type" onchange="this.form.submit()"
                        class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-blue-500 shadow">
                    <option value="">All Types</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>

            <a href="{{ route('create-product') }}"
               class="w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow transition-all duration-200 text-center">
                + Add New Product
            </a>
        </form>
    </div>

    {{-- Product List --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($products as $product)
            <div class="bg-white rounded-xl shadow hover:shadow-lg transition duration-300 overflow-hidden flex flex-col">
                <div class="relative h-48 bg-gray-200">
                    <img src="{{ $product->productUsageImages->first()->image_url ?? 'https://via.placeholder.com/300' }}" alt="Product Image"
                         class="absolute inset-0 w-full h-full object-cover">
                </div>

                <div class="p-4 flex flex-col gap-2 flex-grow">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('product.detail', ['productId' => $product->id]) }}" class="text-lg font-semibold text-gray-800 hover:underline">{{ $product->name }}</a>
                        <span class="text-gray-500 font-medium">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex gap-2 flex-wrap">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full border border-blue-400">
                            {{ $product->collections->name ?? 'No Collection' }}
                        </span>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full border border-blue-400">
                            {{ $product->types->name ?? 'No Type' }}
                        </span>
                    </div>
                </div>

                <div class="p-4 pt-0 flex flex-col gap-2">
                    <a href="{{ route('products.edit', ['productId' => $product->id]) }}"
                       class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded transition focus:ring-2 ring-blue-500 text-center flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('products.destroy', ['productId' => $product->id]) }}" method="POST"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 rounded transition focus:ring-2 ring-red-500 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-500 col-span-full">Tidak ada produk ditemukan.</p>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    <div class="mt-10">
        {{ $products->links() }}
    </div>

</div>
@endsection