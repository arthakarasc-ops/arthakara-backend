<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Mail\SendOtpMail;
use App\Models\OtpCode;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
                }
                
                $path = $request->file('avatar')->store('avatars', 'public');
                $data['avatar'] = $path;
            }

            $user->fill($data);
            $user->save();

            RateLimiter::clear($key);

            return response()->json([
                'message' => 'User berhasil diupdate.',
                'data' => new UserResource($user),
                'isSuccess' => true
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'isSuccess' => false
            ], 500);
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

    /*
    |------------------------------------------
    | FORGOT PASSWORD — SEND OTP
    |------------------------------------------
    */
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email|max:100',
            ]);

            $email = $request->input('email');

            // Rate limiter: max 5 attempts per 15 minutes
            $key = 'forgot-password:' . $email;
            $maxAttempts = 5;
            $decaySeconds = 60 * 15;

            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                $seconds = RateLimiter::availableIn($key);

                return response()->json([
                    'error' => 'Terlalu banyak percobaan. Coba lagi dalam ' . ceil($seconds / 60) . ' menit.',
                    'isSuccess' => false
                ], 429);
            }

            // Check if the email is registered
            $user = User::where('email', $email)->first();

            if (!$user) {
                RateLimiter::hit($key, $decaySeconds);

                return response()->json([
                    'error' => 'Email tidak terdaftar.',
                    'isSuccess' => false
                ], 404);
            }

            // Delete any existing OTPs for this email
            OtpCode::where('email', $email)->delete();

            // Generate a cryptographically secure 6-digit OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Store hashed OTP with 5-minute expiry
            OtpCode::create([
                'email' => $email,
                'otp' => Hash::make($otp),
                'expires_at' => Carbon::now()->addMinutes(5),
            ]);

            // Send OTP email
            Mail::to($email)->send(new SendOtpMail($otp));

            RateLimiter::hit($key, $decaySeconds);

            return response()->json([
                'message' => 'Kode OTP telah dikirim ke email Anda.',
                'isSuccess' => true
            ], 200);

        } catch (Exception $e) {
            Log::error('Forgot Password Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Terjadi kesalahan saat mengirim OTP.',
                'message' => $e->getMessage(),
                'isSuccess' => false
            ], 500);
        }
    }

    /*
    |------------------------------------------
    | VERIFY OTP
    |------------------------------------------
    */
    public function verifyOtp(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email|max:100',
                'otp' => 'required|string|size:6',
            ]);

            $email = $request->input('email');
            $otp = $request->input('otp');

            $otpRecord = OtpCode::where('email', $email)->first();

            if (!$otpRecord) {
                return response()->json([
                    'error' => 'Kode OTP tidak ditemukan. Silakan minta ulang.',
                    'isSuccess' => false
                ], 404);
            }

            // Check expiry
            if (Carbon::now()->greaterThan($otpRecord->expires_at)) {
                $otpRecord->delete();

                return response()->json([
                    'error' => 'Kode OTP sudah kedaluwarsa. Silakan minta ulang.',
                    'isSuccess' => false
                ], 410);
            }

            // Verify hashed OTP
            if (!Hash::check($otp, $otpRecord->otp)) {
                return response()->json([
                    'error' => 'Kode OTP salah.',
                    'isSuccess' => false
                ], 401);
            }

            return response()->json([
                'message' => 'Kode OTP valid.',
                'isSuccess' => true
            ], 200);

        } catch (Exception $e) {
            Log::error('Verify OTP Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Terjadi kesalahan saat memverifikasi OTP.',
                'message' => $e->getMessage(),
                'isSuccess' => false
            ], 500);
        }
    }

    /*
    |------------------------------------------
    | RESET PASSWORD
    |------------------------------------------
    */
    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email|max:100',
                'otp' => 'required|string|size:6',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $email = $request->input('email');
            $otp = $request->input('otp');

            $otpRecord = OtpCode::where('email', $email)->first();

            if (!$otpRecord) {
                return response()->json([
                    'error' => 'Kode OTP tidak ditemukan. Silakan minta ulang.',
                    'isSuccess' => false
                ], 404);
            }

            // Check expiry
            if (Carbon::now()->greaterThan($otpRecord->expires_at)) {
                $otpRecord->delete();

                return response()->json([
                    'error' => 'Kode OTP sudah kedaluwarsa. Silakan minta ulang.',
                    'isSuccess' => false
                ], 410);
            }

            // Verify hashed OTP
            if (!Hash::check($otp, $otpRecord->otp)) {
                return response()->json([
                    'error' => 'Kode OTP salah.',
                    'isSuccess' => false
                ], 401);
            }

            // Update the user's password
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json([
                    'error' => 'User tidak ditemukan.',
                    'isSuccess' => false
                ], 404);
            }

            $user->password = Hash::make($request->input('password'));
            $user->save();

            // Revoke all existing tokens for security
            $user->tokens()->delete();

            // Delete the used OTP
            $otpRecord->delete();

            // Clear the rate limiter
            RateLimiter::clear('forgot-password:' . $email);

            return response()->json([
                'message' => 'Password berhasil diubah. Silakan login kembali.',
                'isSuccess' => true
            ], 200);

        } catch (Exception $e) {
            Log::error('Reset Password Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Terjadi kesalahan saat mereset password.',
                'message' => $e->getMessage(),
                'isSuccess' => false
            ], 500);
        }
    }
}