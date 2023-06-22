<?php

namespace App\Http\Controllers\Api\Berita;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Mail;
use App\Mail\NotifyMail;

// List model
use App\Models\berita;
class BeritaController extends Controller
{
    //
    
    public function GetAll(Request $request)
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

        $news = Berita::select("*")
                        ->orderBy('kdberita', 'DESC')
                        ->skip($skip)
                        ->take($take)
                        ->get()->toArray();
                                
        return response([
            'success' => true,
            'message' => 'List Semua Data Klinik',
            'data' => $news
        ], 200);
    }
    
    public function DetailBerita(Request $request){
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
        
        $news = Berita::select("*")
                        ->where("kdberita", $kode)
                        ->get()->toArray();
              
        return response([
            'success' => true,
            'message' => 'List Semua Data Klinik',
            'data' => $news
        ], 200);
    }

}
