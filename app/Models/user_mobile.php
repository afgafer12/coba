<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_mobile extends Model
{
    public $table = "user_mobile";
    public $timestamps = false;
    use HasFactory;
    
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'handphone',
        'email',
        'password',
        'is_verified',
        'token',
        'otp',
        'otp_expire',
        'is_active',
        'createdAt',
        'updatedAt',
        'is_loggin',
        'token_oauth',
        'jenis_oauth',
    ];
}





