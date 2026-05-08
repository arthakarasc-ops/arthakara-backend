@extends('main.main')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Orders</h1>
            <p class="text-slate-500 text-sm">Monitor and manage your store transactions</p>
        </div>

        <form method="GET" action="{{ route('orders.index') }}" class="w-full sm:w-auto">
            <select name="status_id" onchange="this.form.submit()"
                    class="w-full sm:w-56 bg-white border border-slate-200 text-slate-700 py-2.5 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 shadow-sm transition-all cursor-pointer">
                <option value="">All Statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}" {{ $selectedStatusId == $status->id ? 'selected' : '' }}>
                        {{ ucfirst($status->name) }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($orders as $order)
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500 overflow-hidden flex flex-col group">
                <div class="p-6 flex-grow flex flex-col">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-cyan-50 group-hover:text-cyan-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400">#{{ $order->id }}</span>
                    </div>

                    <h3 class="font-bold text-slate-900 line-clamp-1 mb-1">{{ $order->users->email ?? 'Guest Customer' }}</h3>
                    <p class="text-xs text-slate-400 mb-4">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</p>

                    <div class="flex flex-wrap gap-2 mt-auto">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-50 text-cyan-700 border border-cyan-100">
                            {{ $order->statuses->name ?? 'Pending' }}
                        </span>
                        
                        @if($order->shippingMethods && $order->shippingMethods->name === 'Take Away')
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-100">
                                Take Away
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-100">
                                Delivery
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-4 bg-slate-50/50 border-t border-slate-50">
                    <a href="{{ route('order.detail', ['orderId' => $order->id]) }}"
                       class="w-full bg-white hover:bg-slate-900 hover:text-white text-slate-600 font-bold py-2.5 rounded-xl transition-all duration-300 text-center flex items-center justify-center gap-2 text-sm shadow-sm">
                        View Detail
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                <p class="text-slate-400 font-medium">No orders found.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection