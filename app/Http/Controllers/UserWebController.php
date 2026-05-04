<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class UserWebController extends Controller
{
    /*
    |------------------------------------------
    | LOGIN ADMIN (BLADE)
    |------------------------------------------
    */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            $email = $request->email;
            $password = $request->password;

            // 🔥 RATE LIMIT
            $key = 'admin-login:' . $email;

            if (RateLimiter::tooManyAttempts($key, 5)) {
                $seconds = RateLimiter::availableIn($key);

                return back()->withErrors([
                    'email' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik."
                ]);
            }

            // 🔍 CEK USER
            $user = User::where('email', $email)->first();

            if (!$user) {
                RateLimiter::hit($key);

                return back()->withErrors([
                    'email' => 'User tidak ditemukan'
                ])->withInput();
            }

            // 🔐 CEK PASSWORD
            if (!Hash::check($password, $user->password)) {
                RateLimiter::hit($key);

                return back()->withErrors([
                    'password' => 'Password salah'
                ])->withInput();
            }

            // 🚫 CEK ADMIN
            if (!$user->is_admin) {
                return back()->withErrors([
                    'email' => 'Akses hanya untuk admin'
                ]);
            }

            // ✅ CLEAR LIMIT
            RateLimiter::clear($key);

            // ✅ LOGIN SESSION
            Auth::login($user);

            // 🔥 PENTING: regenerate session
            $request->session()->regenerate();

            return redirect()->route('admin');

        } catch (Exception $e) {
            Log::error('Admin Login Error: ' . $e->getMessage());

            return back()->withErrors([
                'email' => 'Terjadi kesalahan server'
            ]);
        }
    }

    /*
    |------------------------------------------
    | LOGOUT
    |------------------------------------------
    */
    public function logout(Request $request)
    {
        try {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');

        } catch (Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());

            return redirect()->route('login')
                ->withErrors(['error' => 'Gagal logout']);
        }
    }
}