<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Roccia - Verifikasi OTP</title>
</head>
<body>
    <div class="flex justify-center items-center min-h-screen bg-gray-100">
        <div class="w-full max-w-md py-14 px-11 bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-shadow duration-300 ease-in-out">
            <h2 class="text-3xl font-bold text-gray-800 mb-11 text-center">Verifikasi Kode OTP</h2>
            
            @if(session('success'))
                <div class="mb-4 text-green-600">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="mb-4 text-red-600">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('auth-verify-otp') }}" method="POST">
                @csrf
                <label for="otp" class="block mb-2 text-sm font-medium text-gray-900">Kode OTP</label>
                <input type="text" name="otp" id="otp" placeholder="Masukkan kode OTP yang dikirim ke email Anda" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 mb-4">
                <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm py-2.5 text-center transition-colors duration-200">
                    Verifikasi
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600 text-sm">Belum menerima kode? <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Coba lagi</a></p>
            </div>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Roccia Admin - Verification OTP</title>
</head>
<body>
    <div class="flex justify-center items-center min-h-screen bg-gray-100">
        <div class="w-full max-w-md py-14 px-11 bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-shadow duration-300 ease-in-out">
            <h2 class="text-3xl font-bold text-gray-800 mb-11 text-center">Masukkan Kode OTP</h2>
            
            @if(session('success'))
                <div class="mb-4 text-green-600">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="mb-4 text-red-600">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('auth-verify-otp') }}" method="POST">
                @csrf
                <label for="otp" class="block mb-2 text-sm font-medium text-gray-900">Kode OTP</label>
                <input type="text" name="otp" id="otp" placeholder="Masukkan kode OTP yang dikirim ke email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 mb-4">
                <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm py-2.5 text-center transition-colors duration-200">
                    Verifikasi
                </button>
            </form>
        </div>
    </div>
</body>
</html>