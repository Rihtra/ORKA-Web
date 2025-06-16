<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Ambil profil user yang sedang login
    public function profile()
    {
        return response()->json([
            'success' => true,
            'data' => Auth::user(),
        ]);
    }
  
    // Update profil user yang sedang login
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
             'name' => $request->name,
    'nim' => $request->nim,
    'jurusan_id' => $request->jurusan, // kalau memang 'jurusan' itu ID
    'no_hp' => $request->no_hp,
    'semester' => $request->semester,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        $user->update($request->only(['nama', 'nim', 'jurusan', 'nomor_hp', 'semester']));

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => $user,
        ]);
    }
}

