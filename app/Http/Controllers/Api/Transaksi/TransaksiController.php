<?php

namespace App\Http\Controllers\Api\Transaksi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Mail;
use App\Mail\NotifyMail;

// List model
use App\Models\cabang;
class TransaksiController extends Controller
{
    
    public function transaksiList(Request $request)
    {
        $authorization = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authorization);
        if($token != '4pb4tech'){
            return response()->json([
                'success' => false,
                'message' => 'You dont have authorization!',
                'data'    => ''
            ], 402);      
        }

        // Lanjutkan kode disini
        var_dump('transaksiList');
        



    }

    public function transaksiByStatusList(Request $request)
    {
        $authorization = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authorization);
        if($token != '4pb4tech'){
            return response()->json([
                'success' => false,
                'message' => 'You dont have authorization!',
                'data'    => ''
            ], 402);      
        }

        // Lanjutkan kode disini
        var_dump('transaksiByStatusList');
        



    }

    public function aktifitasHariIniList(Request $request)
    {
        $authorization = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authorization);
        if($token != '4pb4tech'){
            return response()->json([
                'success' => false,
                'message' => 'You dont have authorization!',
                'data'    => ''
            ], 402);      
        }

        // Lanjutkan kode disini
        var_dump('aktifitasHariIniList');
        



    }

    public function aktivitasDetail(Request $request)
    {
        $authorization = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authorization);
        if($token != '4pb4tech'){
            return response()->json([
                'success' => false,
                'message' => 'You dont have authorization!',
                'data'    => ''
            ], 402);      
        }

        // Lanjutkan kode disini
        var_dump('aktivitasDetail');
        



    }



    

}
