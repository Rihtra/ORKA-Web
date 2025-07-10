<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Exception;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'nim' => 'required|string|unique:users,nim',
                'no_hp' => 'required|string|max:20',
                'semester' => 'required|string|max:10',
                'jurusan_id' => 'required|exists:jurusans,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'mahasiswa', // paksa role jadi mahasiswa
                'nim' => $request->nim,
                'no_hp' => $request->no_hp,
                'semester' => $request->semester,
                'jurusan_id' => $request->jurusan_id,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Registrasi berhasil!',
                'data' => $user,
                'token' => $token,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat registrasi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
