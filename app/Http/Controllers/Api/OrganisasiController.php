<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organisasi;
use Illuminate\Http\Request;

class OrganisasiController extends Controller
{
    // List semua organisasi
    public function index()
    {
        $organisasis = Organisasi::with('divisis')->get();

        return response()->json([
            'success' => true,
            'data' => $organisasis,
        ]);
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
