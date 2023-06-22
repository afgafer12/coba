<?php

namespace App\Http\Controllers\Api\Cabang;

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
use App\Models\ulasan;
class KlinikController extends Controller
{
    //
    public function listCabang(Request $request)
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
        $search = htmlentities($request->input('search'));
        $rating = htmlentities($request->input('rating'));
        // $value_rating = htmlentities($request->input('value_rating'));
        
        $take = $per_page;
        $skip = ($page - 1) * $take;
        DB::beginTransaction();
        try{
            if($search){
                $klinik = Cabang::select("*")
                        ->where('nama', 'like', '%'.$search.'%')
                        ->orderBy('kdcabang', 'DESC')
                        ->skip($skip)
                        ->take($take)
                        ->get()->toArray();
            }

            if($rating){
                $klinik = Cabang::select("*")
                        ->where('total_rating', $rating)
                        ->orderBy('kdcabang', 'DESC')
                        ->skip($skip)
                        ->take($take)
                        ->get()->toArray();
            }

               

            DB::commit();
            // all good
            return response()->json([
                "status"=>"success",
                "message"=>"List Data of Klinik",
                'data' => $klinik
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

    public function detailCabang(Request $request)
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
        
        $id = htmlentities($request->input('id'));
        DB::beginTransaction();
        try{
            $klinik = Cabang::select("*")
                    ->where("kdcabang", $id)
                    ->get()->toArray();
                
            DB::commit();
            // all good
            return response()->json([
                "status"=>"success",
                "message"=>"List Data of Klinik",
                'data' => $klinik
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

    public function ratingCabang(Request $request)
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
        $cabang_id = htmlentities($request->input('cabang_id'));
        $user_id = htmlentities($request->input('user_id'));
        $current_date_time = \Carbon\Carbon::now()->toDateTimeString();
        
        DB::beginTransaction();
        try{
            $countKlinikQuery = ulasan::selectRaw('count(*) as total')
                                ->where('cabang_klinik_id', $cabang_id)
                                ->pluck('total');
            
            $sumRatingQuery = ulasan::select('*')
                            ->where('cabang_klinik_id', $cabang_id)
                            ->sum('rating');

                
            $countRow = $countKlinikQuery[0];
            $sumRating = $sumRatingQuery;
            
            if($countRow != 0){
                $averageRating = $sumRating / $countRow;
            } else {
                return response()->json([
                    "status"=>"error",
                    "message"=>"Something wrong, please check data."
                ]);
            }
            
            $dbUlasan = ulasan::create([
                'user_id'     => $user_id,
                'cabang_klinik_id'   => $cabang_id,
                'deskripsi'     => $ulasanString,
                'rating'   => $rating,
                'createdAt' => $current_date_time,
                'updatedAt' => $current_date_time,
            ]);
            
            if($averageRating){
                Cabang::where('kdcabang', $cabang_id)->update([
                    'total_rating'   => $averageRating,
                ]);
            } else {
                Cabang::where('kdcabang', $cabang_id)->update([
                    'total_rating'   => $rating,
                ]);    
            }

            DB::commit();
            // all good
            return response()->json([
                "status"=>"success",
                "message"=>"Rating Klinik"
                // 'data' => $klinik
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


    
    // module.exports.klinikRatingStoreService = async (req, res, next) => {
    //     const t = await sequelize.transaction();
    
    //     try {
    //     let countRow = await countKlinikQuery(req.body.cabang_id)
    //     let sumRating = await sumRatingKlinikQuery(req.body.cabang_id)
    //     let averageRating = sumRating?.total / countRow?.total
    
    //     let payload = {
    //         user_id: req.user.id,
    //         cabang_klinik_id: req.body.cabang_id,
    //         deskripsi: req.body.ulasan,
    //         rating: req.body.rating,
    //         createdAt: $appCurrentDate,
    //         updatedAt: $appCurrentDate,
    //     }
    
    //     await Ulasan.create(payload, { transaction: t })
    
    //     await CabangKlinik.update({
    //         total_rating: averageRating ? averageRating : req.body.rating
    //     }, {
    //         where: {
    //         id: req.body.cabang_id
    //         }
    //     }, { transaction: t })
    
    //     await t.commit();
    //     return res.json({
    //         // // status: 200,
    //         message: 'success insert rating',
    //         // rating: averageRating,
    //     })
    //     } catch (err) {
    //     await t.rollback();
    //     return next(err);
    //     }
    // }





}
