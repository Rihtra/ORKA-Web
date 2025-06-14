<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $fillable = ['nama'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function organisasis()
    {
        return $this->hasMany(Organisasi::class);
    }
}

