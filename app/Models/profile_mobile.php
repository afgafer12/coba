<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class profile_mobile extends Model
{
    public $table = "profile_mobile";
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
        'nik',
        'first_name',
        'last_name',
        'phone',
        'image',
        'gender',
        'address',
        'tanggal_lahir',
        'blood_type',
        'is_parent',
        'anggota_keluarga',
        'age',
        'norm',
        'kd_cust',
    ];
}

