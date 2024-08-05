<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $fillable = [
        'nama', 
        'deskripsi'
    ];

    public $timestamps = false;

    protected $primaryKey = 'id';
    public $incrementing = true;


    public function activities(): HasMany
    {
        return $this->hasMany(Activities::class, 'tasks_id', 'id');
    }
}
