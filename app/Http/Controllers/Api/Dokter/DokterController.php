<?php

namespace App\Http\Controllers\Api\Dokter;

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
use App\Models\dokter;
use App\Models\ulasan;
class DokterController extends Controller
{
    
    public function listDokterPerCabang(Request $request)
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
        $per_page = htmlentities($request->input('per_page'));
        $cabang = htmlentities($request->input('cabang'));
        
        // $search = htmlentities($request->input('search'));
        // $pengalaman = htmlentities($request->input('pengalaman'));
        // $spesialis = htmlentities($request->input('spesialis'));
        
        $take = $per_page;
        $skip = ($page - 1) * $take;
        
        $dokter = Dokter::select("*")
                        ->where("kdcabang", $cabang)
                        ->orderBy('kddokter', 'DESC')
                        ->skip($skip)
                        ->take($take)
                        ->get()->toArray();
                  
        return response([
            'success' => true,
            'message' => 'List Semua Data Dokter per Cabang',
            'data' => $dokter
        ], 200);
    }

    public function dokterFavorit(Request $request)
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
        
        $take = 5;
        $skip = (1 - 1) * $take;
        
        $dokter = Dokter::select("*")
                        ->orderBy('total_rating', 'DESC')
                        ->skip($skip)
                        ->take($take)
                        ->get()->toArray();
                    
        return response([
            'success' => true,
            'message' => 'List Dokter Favorit',
            'data' => $dokter
        ], 200);
    }

    public function detailDokter(Request $request)
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
        
        $kode = htmlentities($request->input('id'));
        
        $dokter = DOKTER::select("*")
                        ->where("kddokter", $kode)
                        ->get()->toArray();
              
        return response([
            'success' => true,
            'message' => 'Detail Dokter',
            'data' => $dokter
        ], 200);
    }

    
    public function ratingDokter(Request $request)
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
        
        $ulasanString = htmlentities($request->input('ulasan'));
        $rating = htmlentities($request->input('rating'));
        $dokter_id = htmlentities($request->input('dokter_id'));
        $user_id = htmlentities($request->input('user_id'));
        $current_date_time = \Carbon\Carbon::now()->toDateTimeString();

        $countDokterQuery = ulasan::selectRaw('count(*) as total')
                                    ->where('dokter_id', $dokter_id)
                                    ->pluck('total');
                                    
        $sumRatingQuery = ulasan::select('*')
                                    ->where('dokter_id', $dokter_id)
                                    ->sum('rating');
                                    
        $countRow = $countDokterQuery[0];
        $sumRating = $sumRatingQuery;
        if($countRow != 0){
            $averageRating = $sumRating / $countRow;
        } else {
            return response()->json([
                "status"=>"error",
                "message"=>"Something wrong, please check data."
            ]);
        }
        
        DB::beginTransaction();
        try{
            $dbUlasan = ulasan::create([
                'user_id'     => $user_id,
                'dokter_id'   => $dokter_id,
                'deskripsi'     => $ulasanString,
                'rating'   => $rating,
                'createdAt' => $current_date_time,
                'updatedAt' => $current_date_time,
            ]);

            if($averageRating){
                Dokter::where('kddokter', $dokter_id)->update([
                    'total_rating'   => $averageRating,
                ]);
            } else {
                Dokter::where('kddokter', $dokter_id)->update([
                    'total_rating'   => $rating,
                ]);    
            }
            DB::commit();
            // all good
            return response()->json([
                "status"=>"success",
                "message"=>"The data has been successfully saved."
            ]);
        } catch (Throwable $e) {
            DB::rollback();
            report($e);
            return response()->json([
                "status"=>"error",
                "message"=>"Something wrong, please try again."
            ]);
        }
    }




    
    //
}
