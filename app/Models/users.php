<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class users extends Model
{

    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'nisn',
        'email', 
        'nama_lengkap', 
        'asal_sekolah', 
        'tanggal_bergabung', 
        'role_id',
        'usertype'
    ];
    public $timestamps = false;
    
    protected $primaryKey = 'id';
    public $incrementing = true;

    public function roles()
    {
        return $this->belongsTo(roles::class, 'role_id', 'id');
    }
}
