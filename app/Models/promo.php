<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class promo extends Model
{
    public $table = "promo";
    public $timestamps = false;
    use HasFactory;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'kdpromo',
        'judulpromo',
        'keterangan',
        'gambar',
        'kdcabang',
        'kdklinik',
        'aktif',
    ];
}

