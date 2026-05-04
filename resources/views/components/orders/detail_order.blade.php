@extends('main.main')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Title -->
    <h1 class="text-center text-4xl font-extrabold text-gray-800 mb-10">
        Detail Order: <span class="text-blue-600">#orderId</span>
    </h1>

    <!-- Status Select -->
    <form action="{{ route('order.updateStatus', $order->id) }}" method="POST" class="mb-6">
        @csrf
        <label for="type_id" class="block text-sm font-medium text-gray-700 mb-2">Change Status</label>
        <select id="type_id" name="status_id" required
            class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            @foreach ($statuses as $status)
                <option value="{{ $status->id }}" {{ $order->status_id == $status->id ? 'selected' : '' }}>
                    {{ ucfirst($status->name) }}
                </option>
            @endforeach
        </select>
        <button type="submit"
            class="mt-3 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Update Status
        </button>
    </form>

    <!-- User Info -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">User Email</h3>
            <p class="text-gray-600">{{ $order->users->email }}</p>

            <h3 class="text-lg font-semibold text-gray-800 mt-4">Shipping</h3>
            <p class="text-gray-600">shipping1</p>
        </div>

        <!-- Billing Address -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">User Address</h3>
                <div class="mt-2 space-y-1 text-gray-600">
                    <p><strong>First Name:</strong> $order->shippingAddresses->first_name</p>
                    <p><strong>Last Name:</strong> $order->shippingAddresses->last_name</p>
                    <p><strong>Address:</strong> $order->shippingAddresses->address</p>
                    <p><strong>Suite:</strong> $order->shippingAddresses->suite</p>
                    <p><strong>City:</strong> $order->shippingAddresses->city</p>
                    <p><strong>Province:</strong> $order->shippingAddresses->province</p>
                    <p><strong>Postal Code:</strong> $order->shippingAddresses->postal_code</p>
                    <p><strong>Country:</strong> $order->shippingAddresses->country</p>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Billing Address</h3>
                <div class="mt-2 space-y-1 text-gray-600">
                    <p><strong>First Name:</strong> $order->billingAddresses->first_name</p>
                    <p><strong>Last Name:</strong> $order->billingAddresses->last_name</p>
                    <p><strong>Address:</strong> $order->billingAddresses->address</p>
                    <p><strong>Suite:</strong> $order->billingAddresses->suite</p>
                    <p><strong>City:</strong> $order->billingAddresses->city</p>
                    <p><strong>Province:</strong> $order->billingAddresses->province</p>
                    <p><strong>Postal Code:</strong> $order->billingAddresses->postal_code</p>
                    <p><strong>Country:</strong> $order->billingAddresses->country</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Shipping Method -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800">Shipping Method</h3>
        <p class="text-gray-600"><strong>Name:</strong> $order->shippingMethods->name</p>
        <p class="text-gray-600"><strong>Price:</strong> Rp{{ number_format($order->shippingMethods->price) }}</p>
    </div>

    <!-- Total + Status + Date -->
    <div class="mb-10 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Total Price</h3>
            <p class="text-xl font-bold text-green-600">Rp{{ number_format($order->total_price) }}</p>
        </div>

        <div>
            <span
                class="inline-flex items-center px-4 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700 border border-red-300">
                {{ ucfirst($order->statuses->name) }}
            </span>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-800">Order Date</h3>
            <p class="text-gray-600">09-07-2025</p>
        </div>
    </div>

    <!-- Order Items -->
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Order Items</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <!-- Item Card -->
        @foreach ($order->orderItems as $item)
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition duration-300 flex flex-col overflow-hidden">
                <div class="relative h-48 bg-gray-100">
                    <img src="{{ $item->productVariants->products->first()->image_url ?? 'https://via.placeholder.com/150' }}"
                        alt="Product Image"
                        class="absolute inset-0 w-full h-full object-cover">
                </div>

                <div class="p-4 flex-grow">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">
                        {{ $item->productVariants->products->first()->name ?? 'Unnamed Product' }}
                    </h3>
                    <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                    <p class="text-sm text-gray-500">Total Price: Rp{{ number_format($item->total_price) }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection