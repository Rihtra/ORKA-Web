<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PendaftaranController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'organisasi_id' => 'required|exists:organisasis,id',
            'divisi_id' => 'required|exists:divisis,id',
            'alasan' => 'required|string',
            'cv' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|max:100',
            'prodi' => 'required|string|max:255',
            'nomor_wa' => 'required|string|max:20',
            'semester' => 'required|string|max:10',
        ]);

        // Simpan file CV
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cv', 'public');
        } else {
            throw ValidationException::withMessages(['cv' => 'File CV wajib diunggah.']);
        }
         $validated['status'] = 'pending'; // default status

        // Simpan data pendaftaran
        $pendaftaran = Pendaftaran::create([
            'user_id' => $validated['user_id'],
            'nama' => $validated['nama'],
            'nim' => $validated['nim'],
            'prodi' => $validated['prodi'],
            'nomor_wa' => $validated['nomor_wa'],
            'semester' => $validated['semester'],
            'alasan' => $validated['alasan'],
            'cv' => $cvPath,
            'status' => 'pending',
            'divisi_id' => $validated['divisi_id'],
            'organisasi_id' => $validated['organisasi_id'],
        ]);

        return response()->json([
            'message' => 'Pendaftaran berhasil dikirim!',
            'data' => $pendaftaran,
        ], 201);
    }
    public function showByUserId($user_id)
{
    $pendaftaran = Pendaftaran::with(['organisasi', 'divisi'])
        ->where('user_id', $user_id)
        ->first();

    if (!$pendaftaran) {
        return response()->json(['message' => 'Pendaftaran tidak ditemukan'], 404);
    }

    return response()->json($pendaftaran);
}
public function update(Request $request, $id)
{
    $pendaftaran = Pendaftaran::findOrFail($id);

    $validated = $request->validate([
        'organisasi_id' => 'required|exists:organisasis,id',
        'divisi_id' => 'required|exists:divisis,id',
        'alasan' => 'required|string',
        'cv' => 'nullable|file|mimes:pdf,doc,docx,jpg,png,jpeg',
        'nama' => 'required|string',
        'nim' => 'required|string',
        'prodi' => 'required|string',
        'nomor_wa' => 'required|string',
        'semester' => 'required|string',
    ]);
    if ($request->hasFile('cv')) {
        $validated['cv'] = $request->file('cv')->store('cv');
    }

    $pendaftaran->update($validated);

    return response()->json([
        'message' => 'Pendaftaran berhasil diperbarui',
        'data' => $pendaftaran,
    ]);
}
public function seleksi(Request $request, $id)
{
    $pendaftaran = Pendaftaran::findOrFail($id);

    $validated = $request->validate([
        'status' => 'required|in:diterima,ditolak,pending',
        'jadwal_wawancara' => 'nullable|date',
    ]);

    $pendaftaran->update($validated);

    return response()->json([
        'message' => 'Status seleksi diperbarui',
        'data' => $pendaftaran,
    ]);
}

}
