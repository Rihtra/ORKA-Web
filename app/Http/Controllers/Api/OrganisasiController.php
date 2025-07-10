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
    $user = $request->user();
    $organisasi = Organisasi::with(['users' => function ($query) {
        $query->addSelect('users.id', 'users.name')
              ->withPivot('role');
    }, 'divisis']);

    if ($user->role === 'mahasiswa') {
        $organisasi->where(function ($query) use ($user) {
            $query->where('jurusan_id', $user->jurusan_id)
                  ->orWhereNull('jurusan_id');
        });
    }

    $organisasiList = $organisasi->get()->map(function ($organisasi) {
        $roleMap = [
            'ketua' => 'ketua',
            'wakil ketua' => 'wakil',
            'wakil' => 'wakil',
            'sekretaris' => 'sekretaris',
            'bendahara' => 'bendahara',
        ];
        $struktur = [];
        foreach ($organisasi->users as $user) {
            $normalized = strtolower(trim($user->pivot->role));
            if (isset($roleMap[$normalized])) {
                $struktur[$roleMap[$normalized]] = $user->name;
            }
        }

        return [
            'id' => $organisasi->id,
            'nama' => $organisasi->nama,
            'deskripsi' => $organisasi->deskripsi,
            'logo_url' => $organisasi->logo ? asset('storage/' . $organisasi->logo) : null,
            'struktur' => $struktur,
            'jumlah_anggota' => $organisasi->users->count(),
            'divisis' => $organisasi->divisis->map(function ($divisi) {
                return [
                    'id' => $divisi->id,
                    'nama' => $divisi->nama,
                ];
            }),
            'jurusan_id' => $organisasi->jurusan_id,
            'admin_user_id' => $organisasi->admin_user_id,
            'visi' => $organisasi->visi,
            'misi' => $organisasi->misi,
            'syarat' => $organisasi->syarat,
            'tipe' => $organisasi->tipe,
            'created_at' => $organisasi->created_at,
            'updated_at' => $organisasi->updated_at,
        ];
    });

    return response()->json(['success' => true, 'data' => $organisasiList]);
}


    // Detail satu organisasi
    public function show($id)
{
    $organisasi = Organisasi::with(['users' => function ($query) {
        $query->addSelect('users.id', 'users.name')
              ->withPivot('role');
    }, 'divisis'])->find($id);

    if (!$organisasi) {
        return response()->json([
            'success' => false,
            'message' => 'Organisasi tidak ditemukan',
        ], 404);
    }

    $roleMap = [
    'ketua' => 'ketua',
    'wakil ketua' => 'wakil',
    'wakil' => 'wakil',
    'sekretaris' => 'sekretaris',
    'bendahara' => 'bendahara',
];

foreach ($organisasi->users as $user) {
    $normalized = strtolower(trim($user->pivot->role));
    if (isset($roleMap[$normalized])) {
        $struktur[$roleMap[$normalized]] = $user->name;
    }
}



    return response()->json([
        'success' => true,
        'data' => [
            'id' => $organisasi->id,
            'nama' => $organisasi->nama,
            'deskripsi' => $organisasi->deskripsi,
            'logo_url' => $organisasi->logo ? asset('storage/' . $organisasi->logo) : null,
            'struktur' => $struktur,
            'jumlah_anggota' => $organisasi->users->count(),
            'divisis' => $organisasi->divisis->map(function ($divisi) {
                return [
                    'id' => $divisi->id,
                    'nama' => $divisi->nama,
                ];
            }),
        ],
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
