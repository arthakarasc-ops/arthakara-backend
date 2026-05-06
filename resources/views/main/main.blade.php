<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Arthakara - Admin</title>
    <style>
        /* Hide scrollbar for Chrome, Safari and Opera */
        .custom-scrollbar::-webkit-scrollbar {
            display: none;
        }
        /* Hide scrollbar for IE, Edge and Firefox */
        .custom-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
</head>
<body class="flex h-screen bg-slate-50 overflow-hidden text-slate-800">

    <!-- Sidebar -->
    @include('main.sidebar')

    <!-- Main content -->
    <main class="flex-1 p-10 overflow-y-auto">
        @yield('content')
    </main>
</body>
</html>