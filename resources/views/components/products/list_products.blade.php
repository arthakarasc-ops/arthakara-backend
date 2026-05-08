@extends('main.main')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Products</h1>
            <p class="text-slate-500 text-sm">Manage your storefront inventory</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto">
            <form method="GET" action="{{ route('products.index') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <select name="collection" onchange="this.form.submit()"
                        class="w-full sm:w-48 bg-white border border-slate-200 text-slate-700 py-2.5 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 shadow-sm transition-all cursor-pointer">
                    <option value="">All Collections</option>
                    @foreach ($collections as $collection)
                        <option value="{{ $collection->id }}" {{ request('collection') == $collection->id ? 'selected' : '' }}>
                            {{ $collection->name }}
                        </option>
                    @endforeach
                </select>

                <select name="type" onchange="this.form.submit()"
                        class="w-full sm:w-48 bg-white border border-slate-200 text-slate-700 py-2.5 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 shadow-sm transition-all cursor-pointer">
                    <option value="">All Types</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </form>

            <a href="{{ route('create-product') }}"
               class="w-full sm:w-auto bg-cyan-600 hover:bg-cyan-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 text-center flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Product
            </a>
        </div>
    </div>

    {{-- Product List --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($products as $product)
            <div class="bg-white rounded-3xl shadow-sm hover:shadow-xl transition-all duration-500 overflow-hidden flex flex-col group border border-slate-100/50">
                <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                    <img src="{{ $product->productUsageImages->first()->image_url ?? 'https://via.placeholder.com/300' }}" alt="Product Image"
                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                </div>

                <div class="p-5 flex flex-col flex-grow">
                    <div class="flex justify-between items-start mb-3">
                        <a href="{{ route('product.detail', ['productId' => $product->id]) }}" class="text-lg font-bold text-slate-800 hover:text-cyan-600 transition-colors line-clamp-1">{{ $product->name }}</a>
                    </div>
                    
                    <div class="flex flex-col gap-2 mt-auto">
                        <span class="text-cyan-700 font-bold text-lg">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                        <div class="flex flex-wrap gap-1">
                            <span class="bg-cyan-50 text-cyan-700 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider border border-cyan-100">
                                {{ $product->collections->name ?? 'Uncategorized' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-4 pt-0 grid grid-cols-2 gap-2">
                    <a href="{{ route('products.edit', ['productId' => $product->id]) }}"
                       class="bg-slate-50 hover:bg-cyan-50 text-slate-600 hover:text-cyan-700 font-semibold py-2.5 rounded-xl transition-all duration-200 text-center flex items-center justify-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit
                    </a>
                    <form action="{{ route('products.destroy', ['productId' => $product->id]) }}" method="POST"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');" class="w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-slate-50 hover:bg-red-50 text-slate-600 hover:text-red-700 font-semibold py-2.5 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                <p class="text-slate-400 font-medium">No products found in this category.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>
@endsection