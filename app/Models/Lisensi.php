<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lisensi extends Model
{
    protected $table = 'lisensi';
    protected $primaryKey = 'id_lisensi';

    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_DECLINED = 'declined';

    protected $fillable = [
        'nomor_sertifikat',
        'penerbit',
        'tgl_terbit',
        'masa_berlaku',
        'keaslian_ras',
        'riwayat_kesehatan',
        'id_kuda',
        'status',
        'id_pengaju',
        'catatan_admin',
    ];

    public function kuda()
    {
        return $this->belongsTo(Kuda::class, 'id_kuda', 'id_kuda');
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'id_pengaju', 'id_user');
    }
}
