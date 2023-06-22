<?php

namespace App\Http\Controllers\Api\Promo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Mail;
use App\Mail\NotifyMail;

// List model
use App\Models\promo;
class PromoController extends Controller
{
    
    public function getAll(Request $request)
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
        
        $page = htmlentities($request->input('page'));
        $take = 15;
        $skip = ($page - 1) * $take;

        $promo = Promo::select("*")
                        ->orderBy('kdpromo', 'DESC')
                        ->skip($skip)
                        ->take($take)
                        ->get()->toArray();
                                
        return response([
            'success' => true,
            'message' => 'List Semua Data Promo',
            'data' => $promo
        ], 200);
    }

    public function promoCabang(Request $request)
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
        
        $page = htmlentities($request->input('page'));
        $kodeCabang = htmlentities($request->input('kodeCabang'));
        $take = 2;
        $skip = ($page - 1) * $take;

        $promo = Promo::select("*")
                        ->where("kdcabang", $kodeCabang)
                        ->orderBy('kdpromo', 'DESC')
                        ->skip($skip)
                        ->take($take)
                        ->get()->toArray();
                  
        return response([
            'success' => true,
            'message' => 'List Semua Data Promo',
            'data' => $promo
        ], 200);
    }

    public function detailPromo(Request $request)
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
        
        $kode = htmlentities($request->input('kode'));
        
        $promo = Promo::select("*")
                        ->where("kdpromo", $kode)
                        ->get()->toArray();
              
        return response([
            'success' => true,
            'message' => 'List Semua Data Klinik',
            'data' => $promo
        ], 200);
    }


}
