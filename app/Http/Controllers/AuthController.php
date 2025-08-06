<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Email atau password salah'], 401);
        }

        $user = auth()->user();

        // Generate refresh token
        $refreshToken = Str::random(64);

        // Simpan di DB (hapus yang lama dulu)
        RefreshToken::where('user_id', $user->id)->delete();

        RefreshToken::create([
            'user_id' => $user->id,
            'token' => $refreshToken,
        ]);

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
            'token' => $token,
            'refresh_token' => $refreshToken
        ]);
    }

    public function refresh(Request $request)
    {
        $refreshToken = $request->input('refresh_token');

        $record = RefreshToken::where('token', $refreshToken)->first();

        if (!$record) {
            return response()->json(['error' => 'Refresh token tidak valid'], 401);
        }

        $user = $record->user;

        $newAccessToken = JWTAuth::fromUser($user);
        $newRefreshToken = Str::random(64);

        // Update refresh token di DB
        $record->update([
            'token' => $newRefreshToken
        ]);

        return response()->json([
            'message' => 'Token diperbarui',
            'token' => $newAccessToken,
            'refresh_token' => $newRefreshToken
        ]);
    }

    public function verifyPassword(Request $request)
    {
        // Validasi input
        $this->validate($request, [
            'password' => 'required|string',
            'email' => 'sometimes|email' // Opsional jika ingin verifikasi spesifik user
        ]);

        try {
            // Dapatkan user yang sedang login
            $user = $request->user();
            
            // Jika perlu verifikasi untuk user tertentu (opsional)
            if ($request->has('email')) {
                $user = User::where('email', $request->email)->first();
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User tidak ditemukan'
                    ], 404);
                }
            }

            // Verifikasi password
            $isValid = Hash::check($request->password, $user->password);

            return response()->json([
                'success' => $isValid,
                'message' => $isValid ? 'Password valid' : 'Password tidak valid'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat verifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
