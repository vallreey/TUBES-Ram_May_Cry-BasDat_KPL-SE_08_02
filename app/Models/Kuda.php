<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kuda extends Model
{
    use HasFactory;

    protected $table = 'kuda';  // Nama tabel di database
    protected $primaryKey = 'id_kuda';  // Kolom primary key
    protected $fillable = ['nama_kuda', 'jenis_kuda', 'status_jual', 'harga_buka', 'id_peternakan', 'id_ibu', 'id_ayah'];
}