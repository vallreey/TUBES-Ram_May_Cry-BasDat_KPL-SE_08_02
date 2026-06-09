<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'nama_lengkap',
    'email',
    'password',
    'no_telp',
    'alamat',
    'role'
])]
#[Hidden([
    'password',
    'remember_token'
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    public const ROLE_ADMIN = 'admin';
    public const ROLE_PETERNAK = 'peternak';
    public const ROLE_PEMBELI = 'pembeli';

    use HasFactory, Notifiable;
    
    protected $fillable = [
        'nama_lengkap',
        'email',
        'no_telp',
        'alamat',
        'role',
        'password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function peternakan()
    {
        return $this->hasOne(Peternakan::class, 'id_user', 'id_user');
    }

    public function kudaDibeli()
    {
        return $this->hasMany(Transaksi::class, 'id_pembeli', 'id_user');
    }

    public function kudaDijual()
    {
        return $this->hasMany(Transaksi::class, 'id_penjual', 'id_user');
    }
}