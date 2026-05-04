<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Arthakara - Admin</title>
</head>
<body class="flex min-h-screen bg-gray-100">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md flex flex-col justify-between">
        <div>
            <div class="p-6 text-center border-b">
                <a href="{{ route('admin') }}">
                    <img src="{{ asset('assets/logo.png') }}" alt="">
                </a>
            </div>
            <nav class="flex flex-col p-4 space-y-2">
                <a href="{{ route('products.index') }}" class="px-4 py-2 rounded hover:bg-blue-100 text-gray-700">Products</a>
                <a href="{{ route('collections.index') }}" class="px-4 py-2 rounded hover:bg-blue-100 text-gray-700">Collections</a>
                <a href="{{ route('orders.index') }}" class="px-4 py-2 rounded hover:bg-blue-100 text-gray-700">Orders</a>
                <a href="{{ route('other.other') }}" class="px-4 py-2 rounded hover:bg-blue-100 text-gray-700">Others</a>
            </nav>
        </div>
        <div class="p-4 border-t">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Logout</button>
            </form>
        </div>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-10">
        @yield('content')
    </main>
</body>
</html>