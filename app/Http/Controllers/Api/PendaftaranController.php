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
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'organisasi_id' => 'required|exists:organisasis,id',
            'divisi_id' => 'nullable|exists:divisis,id',
            'alasan' => 'required|string',
            'cv' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|max:100',
            'prodi' => 'required|string|max:255',
            'nomor_wa' => 'required|string|max:20',
            'semester' => 'required|string|max:10',
        ]);

        try {
            $cvPath = $request->file('cv')->store('cv', 'public');
            $validated['cv'] = $cvPath;
            $validated['status'] = 'pending';

            $pendaftaran = Pendaftaran::create($validated);

            return response()->json([
                'message' => 'Pendaftaran berhasil dikirim!',
                'data' => $pendaftaran,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan pendaftaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showByUserId($user_id)
    {
        $pendaftaran = Pendaftaran::with(['organisasi', 'divisi'])
            ->where('user_id', $user_id)
            ->get();

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
            'status' => 'required|in:pending,wawancara,diterima,ditolak', // Updated status options
            'jadwal_wawancara' => 'nullable|date',
        ]);

        if ($request->hasFile('cv')) {
            $validated['cv'] = $request->file('cv')->store('cv', 'public');
        }

        $pendaftaran->update($validated);

        // Only attach to organization if status is diterima
        if ($validated['status'] === 'diterima' && !$pendaftaran->user->organisasis->contains($pendaftaran->organisasi_id)) {
            $pendaftaran->user->organisasis()->attach($pendaftaran->organisasi_id, ['role' => 'Anggota']);
        }

        return response()->json([
            'message' => 'Pendaftaran berhasil diperbarui',
            'data' => $pendaftaran->load('user.organisasis'),
        ]);
    }

    public function seleksi(Request $request, $id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,wawancara,diterima,ditolak', // Updated status options
            'jadwal_wawancara' => 'nullable|date',
        ]);

        $pendaftaran->update($validated);

        // Only attach to organization if status is diterima
        if ($validated['status'] === 'diterima' && !$pendaftaran->user->organisasis->contains($pendaftaran->organisasi_id)) {
            $pendaftaran->user->organisasis()->attach($pendaftaran->organisasi_id, ['role' => 'Anggota']);
        }

        return response()->json([
            'message' => 'Status seleksi diperbarui',
            'data' => $pendaftaran,
        ]);
    }
}