<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organisasi;
use Illuminate\Http\Request;

class OrganisasiController extends Controller
{
    // List semua organisasi
   public function index(Request $request)
{
    $user = $request->user(); // atau Auth::user()

    $organisasi = Organisasi::query();

    // Kalau user role-nya mahasiswa → filter berdasarkan jurusan_id
    if ($user->role === 'mahasiswa') {
        $organisasi->where(function ($query) use ($user) {
            $query->where('jurusan_id', $user->jurusan_id)
                  ->orWhereNull('jurusan_id'); // Organisasi umum (UKM)
        });
    }

    return response()->json($organisasi->get());
}


    // Detail satu organisasi
    public function show($id)
    {
        $organisasi = Organisasi::with('divisis')->find($id);

        if (!$organisasi) {
            return response()->json([
                'success' => false,
                'message' => 'Organisasi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $organisasi,
        ]);
    }
    public function divisi($id)
{
    $organisasi = Organisasi::findOrFail($id);
    return response()->json([
        'success' => true,
        'data' => $organisasi->divisis()->select('id', 'nama')->get(),
    ]);
}

}
