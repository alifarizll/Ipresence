<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;



class users extends Authenticatable implements JWTSubject
{

    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nisn',
        'email', 
        'username',
        'nama_lengkap', 
        'asal_sekolah', 
        'tanggal_bergabung', 
        'role_id',
        'usertype',
        'img',
    ];
    public $timestamps = false;
    
    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $hidden = [
        'remember_token',
    ];

    public function roles()
    {
        return $this->belongsTo(roles::class, 'role_id', 'id');
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => url('/storage/posts/' . $image),
        );
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
