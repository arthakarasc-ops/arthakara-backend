@extends('main.main')

@section('content')
<div class="container mx-auto px-6 py-10">
    <div class="flex flex-col justify-between items-center mb-10">
        <h1 class="text-4xl font-bold text-gray-800 mb-5">My Statuses</h1>
        @forelse($types as $type)
            <div id="color" class="w-full flex justify-between border-2 border-blue-500 p-2 rounded-lg bg-blue-200 mb-7">
                <div class="w-1/3 flex justify-between">
                    <h1 class="text-xl, font-medium">{{ $type->name }}</h1>
                </div>
                <form action="{{ route('type.delete', ['typeId' => $type->id]) }}" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tipe ini?');">
                    @csrf
                    @method('DELETE')
                    <button href="" class="bg-red-600 hover:bg-red-700 transition duration-300 rounded-md px-7 py-1 text-white font-semibold">Delete</button>
                </form>
            </div>
        @empty
            <p class="text-gray-500 col-span-full">Tidak ada tipe ditemukan.</p>
        @endforelse
    </div>
</div>
@endsection