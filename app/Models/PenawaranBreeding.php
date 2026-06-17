<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenawaranBreeding extends Model
{
    protected $table = 'penawaran_breeding';
    protected $primaryKey = 'id_penawaran';

    protected $fillable = [
        'id_breeding',
        'id_penawar',
        'id_penerima_tawaran',
        'harga_ditawarkan',
        'harga_nego',
        'pakai_lisensi',
        'status_penawaran',
        'catatan',
    ];

    public function breeding()
    {
        return $this->belongsTo(KawinSilang::class, 'id_breeding', 'id_breeding');
    }

    public function penawar()
    {
        return $this->belongsTo(User::class, 'id_penawar', 'id_user');
    }

    public function penerimaTawaran()
    {
        return $this->belongsTo(User::class, 'id_penerima_tawaran', 'id_user');
    }
}
