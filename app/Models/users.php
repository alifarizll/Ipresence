<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class users extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'nisn', 'username', 'email', 'nama_lengkap', 'asal_sekolah', 'tanggal_bergabung', 'password', 'roles', 'role_id'];
    public $timestamps = false;

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function roles()
    {
        return $this->belongsTo(roles::class, 'role_id', 'id');
    }
}
