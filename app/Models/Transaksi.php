<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'status_transaksi',
        'tgl_transaksi',
        'harga_final',
        'id_kuda',
        'id_pembeli',
        'id_penjual',
    ];

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
