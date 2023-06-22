<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ulasan extends Model
{
    public $table = "ulasan";
    public $timestamps = false;
    use HasFactory;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'dokter_id',
        'cabang_klinik_id',
        'deskripsi',
        'rating',
        'createdAt',
        'updatedAt',
    ];
}
