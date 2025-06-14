<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $fillable = ['organisasi_id', 'nama'];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class);
    }
}
