<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROSES = 'proses';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DIBATALKAN = 'dibatalkan';
    
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
    'status_transaksi',
    'tgl_transaksi',
    'harga_final',
    'id_kuda',
    'id_lisensi',
    'id_pembeli',
    'id_penjual',
    ];

    public function lisensi()
    {
    return $this->belongsTo(Lisensi::class, 'id_lisensi', 'id_lisensi');
    }

    public function kuda()
    {
        return $this->belongsTo(Kuda::class, 'id_kuda', 'id_kuda');
    }

    public function pembeli()
    {
        return $this->belongsTo(User::class, 'id_pembeli', 'id_user');
    }

    public function penjual()
    {
        return $this->belongsTo(User::class, 'id_penjual', 'id_user');
    }
}
