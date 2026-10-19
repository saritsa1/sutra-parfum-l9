<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginApiRequest;
use App\Http\Requests\RegisterApiRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register user baru via API dengan validasi terisolasi
     */
    public function register(RegisterApiRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => bcrypt($validated['password']),
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Registrasi pengguna baru berhasil.',
            'user'    => $user,
        ], 201);
    }

    /**
     * Login via API dan dapatkan Sanctum Bearer token dengan validasi terisolasi
     */
    public function login(LoginApiRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial email atau password tidak valid.'],
            ]);
        }

        // Terbitkan Sanctum token personal access
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'     => 'success',
            'message'    => 'Login success',
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ], 200);
    }

    /**
     * Profile user yang sedang terotentikasi via Bearer Token
     */
    public function profile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'user'   => $request->user(),
        ], 200);
    }

    /**
     * Logout dan cabut (revoke) Bearer Token saat ini
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout success',
        ], 200);
    }
}
