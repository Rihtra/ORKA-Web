<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    // Ambil profil user yang sedang login
    public function profile(Request $request)
{
    $user = $request->user()->load('jurusan');

    return response()->json([
        'success' => true,
        'data' => $user,
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

}

