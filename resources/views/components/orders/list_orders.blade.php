@extends('main.main')

@section('content')

<div class="container mx-auto px-6 py-10">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <h1 class="text-4xl font-bold text-gray-800 mb-4 sm:mb-0">My Orders</h1>

        <form method="GET" action="{{ route('orders.index') }}" class="relative w-full sm:w-auto">
            <select id="collection-filter" name="status_id" onchange="this.form.submit()"
                    class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-blue-500 shadow">
                <option value="">All statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}" {{ $selectedStatusId == $status->id ? 'selected' : '' }}>
                        {{ ucfirst($status->name) }}
                    </option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                </svg>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($orders as $order)
            <div class="bg-white rounded-xl shadow hover:shadow-lg transition duration-300 overflow-hidden flex flex-col">
                <div class="p-6 flex-grow flex flex-col gap-3">
                    <h2 class="text-xl font-bold text-gray-700">#{{ $order->id }}</h2>
                    <p class="text-gray-600 font-medium">{{ $order->users->email ?? 'Unknown user' }}</p>
                    <p class="text-gray-400 text-sm">{{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y') }}</p>

                    <div class="flex items-center gap-2 mt-1">
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-3 py-1 rounded-full border border-red-400 w-fit">
                            {{ ucfirst($order->statuses->name) }}
                        </span>
                        
                        @if($order->shippingMethods && $order->shippingMethods->name === 'Take Away')
                            <span class="bg-amber-100 text-amber-800 text-xs font-medium px-3 py-1 rounded-full border border-amber-400 w-fit">
                                Take Away
                            </span>
                        @else
                            <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-3 py-1 rounded-full border border-indigo-400 w-fit">
                                Delivery
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-4 pt-0">
                    <form action="{{ route('order.detail', ['orderId' => $order->id]) }}" method="GET">
                        <button type="submit"
                                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded transition focus:ring-2 ring-blue-500">
                            See Detail
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-500 col-span-full text-center">No orders found.</p>
        @endforelse
    </div>
</div>
@endsection