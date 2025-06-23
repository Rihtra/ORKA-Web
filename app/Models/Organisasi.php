<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organisasi extends Model
{
    protected $fillable = [
        'nama', 'jurusan_id', 'admin_user_id',
        'deskripsi', 'logo', 'visi', 'misi', 'syarat','tipe',
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class)->withDefault([
        'nama' => 'Umum',
    ]);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function divisis()
    {
        return $this->hasMany(Divisi::class);
    }

    public function pendaftarans()
    {
        return $this->hasMany(Pendaftaran::class);
    }
    public function adminUser()
{
    return $this->belongsTo(\App\Models\User::class, 'admin_user_id');
}
}

