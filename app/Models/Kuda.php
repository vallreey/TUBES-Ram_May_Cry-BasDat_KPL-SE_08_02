<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kuda extends Model
{
    protected $table = 'kuda';
    protected $primaryKey = 'id_kuda';

    protected $fillable = [
        'nama_kuda',
        'jenis_kuda',
        'status_jual',
        'harga_buka',
        'id_peternakan',
        'id_ibu',
        'id_ayah',
    ];

    public function peternakan()
    {
        return $this->belongsTo(Peternakan::class, 'id_peternakan', 'id_peternakan');
    }

    public function ibu()
    {
        return $this->belongsTo(Kuda::class, 'id_ibu', 'id_kuda');
    }

    public function ayah()
    {
        return $this->belongsTo(Kuda::class, 'id_ayah', 'id_kuda');
    }

    public function lisensi()
    {
        return $this->hasOne(Lisensi::class, 'id_kuda', 'id_kuda');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_kuda', 'id_kuda');
    }
}
