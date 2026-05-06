@extends('main.main')

@section('content')
<div class="container mx-auto px-6 py-10">
        <div class="w-full flex justify-between items-center mb-5">
            <h1 class="text-4xl font-bold text-gray-800">My Statuses</h1>
            <a href="{{ route('create-status') }}" class="bg-green-600 hover:bg-green-700 transition duration-300 rounded-md px-6 py-2 text-white font-semibold flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create New Status
            </a>
        </div>
        @forelse($statuses as $status)
            <div id="color" class="w-full flex justify-between border-2 border-blue-500 p-2 rounded-lg bg-blue-200 mb-7">
                <div class="w-1/3 flex justify-between">
                    <h1 class="text-xl, font-medium">{{ $status->name }}</h1>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('status.edit.form', ['statusId' => $status->id]) }}" class="bg-yellow-500 hover:bg-yellow-600 transition duration-300 rounded-md px-7 py-1 text-white font-semibold">Edit</a>
                    <form action="{{ route('status.delete', ['statusId' => $status->id]) }}" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus status ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 transition duration-300 rounded-md px-7 py-1 text-white font-semibold">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-500 col-span-full">Tidak ada status ditemukan.</p>
        @endforelse
    </div>
</div>
@endsection