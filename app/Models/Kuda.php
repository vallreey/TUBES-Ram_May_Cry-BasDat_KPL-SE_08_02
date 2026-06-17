<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kuda extends Model
{
    use HasFactory;

    protected $table = 'kuda';
    protected $primaryKey = 'id_kuda';

    protected $fillable = [
        'nama_kuda',
        'jenis_kuda',
        'gender',
        'status_jual',
        'harga_buka',
        'id_peternakan',
        'id_ibu',
        'id_ayah',
    ];

    public const STATUS_TERSEDIA = 'tersedia';
    public const STATUS_TERJUAL = 'terjual';
    public const STATUS_BREEDING = 'breeding';

    public const GENDER_JANTAN = 'jantan';
    public const GENDER_BETINA = 'betina';

    public function peternakan()
    {
        return $this->belongsTo(
            Peternakan::class,
            'id_peternakan',
            'id_peternakan'
        );
    }

    public function ibu()
    {
        return $this->belongsTo(
            Kuda::class,
            'id_ibu',
            'id_kuda'
        );
    }

    public function ayah()
    {
        return $this->belongsTo(
            Kuda::class,
            'id_ayah',
            'id_kuda'
        );
    }

    public function lisensi()
    {
        return $this->hasOne(
            Lisensi::class,
            'id_kuda',
            'id_kuda'
        );
    }

    public function transaksi()
    {
        return $this->hasMany(
            Transaksi::class,
            'id_kuda',
            'id_kuda'
        );
    }
}