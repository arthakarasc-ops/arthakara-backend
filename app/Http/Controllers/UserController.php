<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class UserController extends Controller
{
    /*
    |------------------------------------------
    | REGISTER USER
    |------------------------------------------
    */
    public function userRegister(UserRegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Gunakan full_name jika ada, fallback ke nickname
            $fullName = $data['full_name'] ?? $data['nickname'];

            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'full_name' => $fullName,
                'phone_number' => $data['phone_number'],
                'nickname' => $data['nickname'],
                'is_admin' => false
            ]);

            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'message' => 'Registrasi berhasil.',
                'token' => $token,
                'user' => new UserResource($user),
                'isSuccess' => true
            ], 201);

        } catch (Exception $e) {
            Log::error('Register Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Terjadi kesalahan.',
                'message' => $e->getMessage(),
                'isSuccess' => false
            ], 500);
        }
    }

    /*
    |------------------------------------------
    | LOGIN USER
    |------------------------------------------
    */
    public function userLogin(UserLoginRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $key = 'login:' . $data['email'];
            $maxAttempts = 5;
            $decaySeconds = 60 * 15;

            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $seconds = RateLimiter::availableIn($key);

                return response()->json([
                    'error' => 'Terlalu banyak percobaan. Coba lagi dalam ' . $seconds . ' detik.',
                    'isSuccess' => false
                ], 429);
            }

            $user = User::where('email', $data['email'])->first();

            if (!$user || !Hash::check($data['password'], $user->password)) {
                RateLimiter::hit($key, $decaySeconds);

                return response()->json([
                    'error' => 'Email atau password salah.',
                    'isSuccess' => false
                ], 401);
            }

            RateLimiter::clear($key);

            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'message' => 'Login berhasil.',
                'token' => $token,
                'user' => new UserResource($user),
                'isSuccess' => true
            ], 200);

        } catch (Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Terjadi kesalahan.',
                'message' => $e->getMessage(),
                'isSuccess' => false
            ], 500);
        }
    }

    /*
    |------------------------------------------
    | GET USER DATA
    |------------------------------------------
    */
    public function getUserData(): JsonResponse
    {
        return response()->json([
            'data' => new UserResource(Auth::user())
        ], 200);
    }

    /*
    |------------------------------------------
    | UPDATE USER
    |------------------------------------------
    */
    public function updateUser(UserUpdateRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $key = 'update-user:' . $user->email;
            $maxAttempts = 3;
            $decaySeconds = 60;

            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $seconds = RateLimiter::availableIn($key);

                throw new HttpResponseException(response()->json([
                    'error' => 'Terlalu banyak percobaan. Coba lagi dalam ' . $seconds . ' detik.',
                    'isSuccess' => false
                ], 429));
            }

            $data = $request->validated();

            $user->fill($data);
            $user->save();

            RateLimiter::clear($key);

            return response()->json([
                'message' => 'User berhasil diupdate.',
                'data' => new UserResource($user),
                'isSuccess' => true
            ], 200);

        } catch (Exception $e) {
            throw new HttpResponseException(response()->json([
                'error' => 'Terjadi kesalahan.',
                'message' => $e->getMessage(),
                'isSuccess' => false
            ], 500));
        }
    }

    /*
    |------------------------------------------
    | ADMIN LOGIN
    |------------------------------------------
    */
    public function adminLogin(AdminLoginRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $user = User::where('email', $data['email'])->first();

            if (!$user || !Hash::check($data['password'], $user->password)) {
                throw new HttpResponseException(response()->json([
                    'error' => 'Email atau password salah.',
                    'isSuccess' => false
                ], 401));
            }

            if (!$user->is_admin) {
                throw new HttpResponseException(response()->json([
                    'error' => 'Akses hanya untuk admin.',
                    'isSuccess' => false
                ], 403));
            }

            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'message' => 'Admin login berhasil.',
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'full_name' => $user->full_name,
                    'is_admin' => $user->is_admin,
                    'token' => $token
                ],
                'isSuccess' => true
            ], 200);

        } catch (Exception $e) {
            throw new HttpResponseException(response()->json([
                'error' => 'Terjadi kesalahan.',
                'message' => $e->getMessage(),
                'isSuccess' => false
            ], 500));
        }
    }

    /*
    |------------------------------------------
    | LOGOUT
    |------------------------------------------
    */
    public function logoutUser(): JsonResponse
    {
        try {
            Auth::user()->tokens()->delete();

            return response()->json([
                'message' => 'Logout berhasil.',
                'isSuccess' => true
            ], 200);

        } catch (Exception $e) {
            throw new HttpResponseException(response()->json([
                'error' => 'Terjadi kesalahan.',
                'message' => $e->getMessage(),
                'isSuccess' => false
            ], 500));
        }
    }
}