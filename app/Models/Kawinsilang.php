<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KawinSilang extends Model
{
    protected $table = 'kawin_silang';
    protected $primaryKey = 'id_breeding';

    protected $fillable = [
        'tgl_pengajuan',
        'tgl_breeding',
        'status_hasil',
        'perkiraan_kelahiran',
        'id_pengaju',
        'pengajuan_sebagai',
        'id_pemilik_betina',
        'id_pemilik_jantan',
        'id_betina',
        'id_jantan',
    ];

    public function kudaBetina()
    {
        return $this->belongsTo(Kuda::class, 'id_betina', 'id_kuda');
    }

    public function kudaJantan()
    {
        return $this->belongsTo(Kuda::class, 'id_jantan', 'id_kuda');
    }

    public function pemilikBetina()
    {
        return $this->belongsTo(User::class, 'id_pemilik_betina', 'id_user');
    }

    public function pemilikJantan()
    {
        return $this->belongsTo(User::class, 'id_pemilik_jantan', 'id_user');
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'id_pengaju', 'id_user');
    }
}
