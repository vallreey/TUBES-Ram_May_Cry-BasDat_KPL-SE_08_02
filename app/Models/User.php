<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    protected $fillable = [
    'nama_lengkap',
    'email',
    'no_telp',
    'alamat',
    'role',
    'password',
    ];

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
