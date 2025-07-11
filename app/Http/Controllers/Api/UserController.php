<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    // Ambil profil user yang sedang login
   public function profile(Request $request)
{
    $user = $request->user()->load('jurusan', 'organisasis');
    $organisasiData = $user->organisasis->map(function ($organisasi) {
        return [
            'id' => $organisasi->id,
            'nama' => $organisasi->nama,
            'pivot' => [
                'role' => $organisasi->pivot->role,
            ],
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Profil pengguna berhasil diambil',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'nim' => $user->nim,
            'email' => $user->email,
            'no_hp' => $user->no_hp,
            'semester' => $user->semester,
            'jurusan' => [
            'id' => $user->jurusan->id,
            'nama' => $user->jurusan->nama,
        ],
            'foto' => $user->foto,
            'organisasi' => $organisasiData,
        ]
    ]);
}


public function update(Request $request)
{
    $user = Auth::user();

    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'nim' => 'required|string|max:20',
        'jurusan_id' => 'required|integer|exists:jurusans,id',
        'no_hp' => 'nullable|string|max:20',
        'semester' => 'nullable|integer|min:1',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors()
        ], 422);
    }

    $user->update($request->only(['name', 'nim', 'jurusan_id', 'no_hp', 'semester']));

    return response()->json([
        'success' => true,
        'message' => 'Profil berhasil diperbarui',
        'data' => $user->load('jurusan'),
    ]);
}
public function updateAccount(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'no_hp' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        $data = [];
        if ($request->has('no_hp')) {
            $data['no_hp'] = $request->no_hp;
        }
        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if (!empty($data)) {
            $user->update($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil diperbarui',
            'data' => [
                'no_hp' => $user->no_hp,
            ]
        ]);
    }
public function uploadPhoto(Request $request)
{
    $user = Auth::user();

    if ($request->hasFile('foto')) {
        $file = $request->file('foto');
        $path = $file->store('foto', 'public');
        $filename = basename($path);

        $user->foto = 'storage/foto/' . $filename;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil diupload',
            'foto' => asset($user->foto),
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Tidak ada file foto',
    ], 400);

    
}
public function logout(Request $request)
    {
        // Hapus semua token user yang sedang login
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout',
        ], 200);
    }



}


