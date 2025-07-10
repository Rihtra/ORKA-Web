<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    protected $fillable = [
    'user_id',
    'organisasi_id',
    'divisi_id',
    'alasan',
    'cv',
    'status',
    'jadwal_wawancara',
    'nama',
    'nim',
    'prodi',
    'nomor_wa',
    'semester',
];


    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class);
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }
    public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}

}

