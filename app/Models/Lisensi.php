<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lisensi extends Model
{
    protected $table = 'lisensi';
    protected $primaryKey = 'id_lisensi';

    protected $fillable = [
        'nomor_sertifikat',
        'penerbit',
        'tgl_terbit',
        'masa_berlaku',
        'keaslian_ras',
        'riwayat_kesehatan',
        'id_kuda',
    ];

    public function kuda()
    {
        return $this->belongsTo(Kuda::class, 'id_kuda', 'id_kuda');
    }
}
