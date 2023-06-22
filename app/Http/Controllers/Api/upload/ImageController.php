<?php

namespace App\Http\Controllers\Api\Upload;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Mail;
use App\Mail\NotifyMail;

class ImagesController extends Controller
{
    //
    public function upload(Request $request)
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
        
        var_dump("Function Upload");
        if($request->file('image')){
            //tambahkan untuk logic ketika upload foto
            $validatedData = $request->validate([
                'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ]);
        
            $image = $request->file('image')->getClientOriginalName();
            
            $path = $request->file('image')->storeAs('public/uploads/pictures/profile', $image);
        }else{
            $image = null;
        }


    }
}
