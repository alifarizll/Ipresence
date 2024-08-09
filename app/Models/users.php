<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\users as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Casts\Attribute;



class users extends Model implements JWTSubject
{

    use HasFactory;

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
