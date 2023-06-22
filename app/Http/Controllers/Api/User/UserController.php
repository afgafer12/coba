<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Mail;
use App\Mail\NotifyMail;


// List model
use App\Models\user_mobile;
use App\Models\profile_mobile;

class UserController extends Controller
{
    public function Login(Request $request){
        $username = $request->handphone;
        $user = user_mobile::from('user_mobile as a')
            ->selectRaw("a.*,
            b.*")
            ->leftJoin('profile_mobile as b', 'b.user_id', 'a.id')
            ->where("handphone", $username)
            //->exlude("password")
            ->first();
           
        if(!$user){
            return response()->json($this->buildResponse(
                'user not found',
                false
            ));
        }else if($user->is_login == 1){
            return response()->json($this->buildResponse(
                'Your account is already logged in',
                false
            ));
        }

        $isMatch = Hash::check($request->password, $user->password);
        if(!$isMatch){
            return response()->json($this->buildResponse(
                'wrong password',
                false
            ));
        }

        $user->token = 'a12';
        $user->save();
        $user->makeHidden(['password', 'jenis_oauth', 'is_verified', 'is_active']);
        return response()->json($this->buildResponse(
            'success',
            true,
            $user
        ));
    }

    // public function logout(){
    //     $user = user_mobile::find();
    //     $user->is_login = 0;
    //     if(!$user->save()){
    //         return response()->json($this->buildResponse(
    //             'failed',
    //             false,
    //         ));
    //     }
    //     return response()->json($this->buildResponse(
    //         'success',
    //         true,
    //     ));
    // }

    public function buildResponse(string $pMessage, $pType = true, $pData = null)
    {
        return [
            'status' => $pType,
            'message' => $pMessage,
            'data' => $pData,
        ];
    }
}
