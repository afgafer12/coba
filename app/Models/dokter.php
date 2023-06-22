<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dokter extends Model
{
    public $table = "dokter";
    public $timestamps = false;
    use HasFactory;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'kddokter',
        'namdokter',
        'statusonline',
        'kdcabang',
        'kdklinik',
        'spesialis',
        'lulusan',
        'deskripsi',
        'total_rating',
        'gambar',
        'total_pengalaman',
    ];
}

