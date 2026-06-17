<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peternakan extends Model
{
    protected $table = 'peternakan';
    protected $primaryKey = 'id_peternakan';

    protected $fillable = [
        'nama_peternakan',
        'kapasitas_kandang',
        'lokasi_map',
        'alamat_lengkap',
        'id_user',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function kuda()
    {
        return $this->hasMany(Kuda::class, 'id_peternakan', 'id_peternakan');
    }
}
