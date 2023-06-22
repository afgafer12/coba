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

class LoginController extends Controller
{
    public function Login(Request $request){
        $username = htmlentities($request->input('handphone'));
        $password = htmlentities($request->input('password'));
        
        $pengguna = user_mobile::select("*")
                            ->where("handphone", $username)
                            ->get()->toArray();
                            

        if(count($pengguna) === 0){
            return response()->json([
                'success' => false,
                'message' => 'User not found!',
                'data'    => ''
            ], 204);
        } else {
            // if($pengguna[0]['is_loggin'] == 1){
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Your account is already logged in!',
            //         'data'    => ''
            //     ], 455);
            // }

            $passwordDB = $pengguna[0]['password'];
            $periksaPassword = Hash::check($password, $passwordDB);
            if($periksaPassword == true){
                user_mobile::whereHandphone($username)->update([
                    'is_loggin'   => 1,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'User Data!',
                    'data'    => $pengguna
                ], 200);
            }else{    
                return response()->json([
                    'success' => false,
                    'message' => 'Wrong Password!',
                    'data'    => ''
                ], 422);
            }
        }
    }

    public function sendOTP(Request $request)
    {
        info('a11');
        $handphone = htmlentities($request->input('handphone'));
        $email = htmlentities($request->input('email'));

        if($handphone != null){
            $this->smsOTP($handphone);
        } else {
            $this->emailOTP($email);
        }

    }

    public function smsOTP($username)
    {
        $angka = floor(rand(1000, 9999));
        $current_date_time = \Carbon\Carbon::now()->toDateTimeString();
        $otp_expired = \Carbon\Carbon::now()->addMinutes(10)->toDateTimeString();
        // var_dump('Current Date Time = ' . $current_date_time);
        // var_dump('OTP Expired = ' . $otp_expired);
        // exit();
        
        $pengguna = user_mobile::select("*")
                            ->where("handphone", $username)
                            ->where("is_active", null)
                            ->get()->toArray();
        if(count($pengguna) === 0){
            $penggunaLama = user_mobile::select("*")
                            ->where("handphone", $username)
                            ->where("is_active", 1)
                            ->get()->toArray();
            if(count($penggunaLama) === 0){
                user_mobile::create([
                    'handphone'     => $username,
                    'otp'   => $angka,
                    'otp_expire'     => $otp_expired,
                    'is_verified'   => false,
                    'createdAt' => $current_date_time,
                    'updatedAt' => $current_date_time,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Token successfully send to your phone.'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is already active'
                ], 402);
            }
        } else {
            // var_dump("Pengguna Ada");
            // var_dump($pengguna[0]['id']);
            user_mobile::whereHandphone($username)->update([
                'otp'   => $angka,
                'otp_expire'     => $otp_expired,
                'is_verified'   => false,
                'updatedAt' => $current_date_time,
            ]);
        }
        
        $message = "Kode OTP Anda: " . $angka . ", RAHASIAKAN kode OTP Anda" ;
        $smsAPI = $this->smsAPI($username, $message);
        if($smsAPI == 'Success'){
            return response()->json([
                'success' => true,
                'message' => 'Token successfully send to your phone.'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Token Failed send to your phone'
            ], 422);
        }
        
    }

    public function smsAPI($username, $message){
        $url = 'https://api-sms.clenicapp.com/otp_api.php';

        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->withBody(http_build_query([
            "no_hp" => $username,
            "message" => $message
        ]), 'application/json')->post($url)->collect()->toArray();
        
        return $response['message'];

    }

    public function emailOTP($email)
    {
        $angka = floor(rand(1000, 9999));
        $text = "Your OTP = " . $angka;
 
        Mail::to($email)->send(new NotifyMail());
 
        return 'Great! Successfully send in your mail';
    }

    public function signUp(Request $request)
    {
        $username = htmlentities($request->input('handphone'));
        $password = htmlentities($request->input('password'));
        $hashed = Hash::make($password, [
            'rounds' => 10,
        ]);
        
        $pengguna = user_mobile::select("*")
                            ->where("handphone", $username)
                            ->get()->toArray();
                            
        if(count($pengguna) === 0){
            return response()->json([
                'success' => false,
                'message' => 'User Not Found!',
                'data'    => ''
            ], 204);
        } else {
            // Update pengguna
            $updateUser = user_mobile::whereHandphone($username)->update([
                'password'   => $hashed,
                'is_active'     => 1,
            ]);

            // Create profile
            //if else digunakan untuk upload foto
            if($request->file('image')){
                //tambahkan untuk logic ketika upload foto
                $validatedData = $request->validate([
                    'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
                ]);
            
                $image = $request->file('image')->getClientOriginalName();
                // $path = $request->file('image')->store('public/images');
                
                $path = $request->file('image')->storeAs('public/images', $image);
            }else{
                $image = null;
            }

            $profilePengguna = profile_mobile::select("*")
                            ->where("user_id", $pengguna[0]['id'])
                            ->get()->toArray();

            if(count($profilePengguna) === 0){
                $createProfile = profile_mobile::create([
                    'user_id'     => $pengguna[0]['id'],
                    'nik'   => $request->input('nik'),
                    'first_name'     => $request->input('first_name'),
                    // 'last_name'   => request->input('last_name'),
                    'gender' => $request->input('gender'),
                    'address' => $request->input('address'),
                    'tanggal_lahir'     => $request->input('tanggal_lahir'),
                    'blood_type'   => $request->input('blood_type'),
                    'image'     => $image,
                    'phone'   => $request->input('phone'),
                    'age' => $request->input('age'),
                    'is_parent' => $request->input('is_parent'),
                ]);
            } else {
                $createProfile = profile_mobile::whereUser_id($pengguna[0]['id'])->update([
                    'nik'   => $request->input('nik'),
                    'first_name'     => $request->input('first_name'),
                    // 'last_name'   => request->input('last_name'),
                    'gender' => $request->input('gender'),
                    'address' => $request->input('address'),
                    'tanggal_lahir'     => $request->input('tanggal_lahir'),
                    'blood_type'   => $request->input('blood_type'),
                    'image'     => $image,
                    'phone'   => $request->input('phone'),
                    'age' => $request->input('age'),
                    'is_parent' => $request->input('is_parent'),
                ]);
            }

            if($updateUser && $createProfile){
                return response()->json([
                    'success' => true,
                    'message' => 'Register Successfull!'
                ], 200);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Register Failed!'
                ], 402);
            }
        }
    }

    public function verify(Request $request)
    {
        $username = htmlentities($request->input('handphone'));
        $otp = htmlentities($request->input('otp'));

        $current_date_time = \Carbon\Carbon::now()->toDateTimeString();

        $pengguna = user_mobile::select("*")
                            ->where("handphone", $username)
                            ->where("otp", $otp)
                            ->where("is_verified", 0)
                            ->get()->toArray();
                                    
        if(count($pengguna) === 0){
            return response()->json([
                'success' => false,
                'message' => 'OTP not found!',
                'data'    => ''
            ], 204);
        } else {
            if($current_date_time < $pengguna[0]['otp_expire']){
                $createPengguna = user_mobile::whereHandphone($username)->update([
                    'otp'   => null,
                    'otp_expire'     => null,
                    'is_verified' => 1,
                    'updatedAt' => $current_date_time,
                ]);
                if($createPengguna){
                    return response()->json([
                        'success' => true,
                        'message' => 'Success',
                        'data'    => ''
                    ], 200);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed',
                        'data'    => ''
                    ], 201);
                }
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP or OTP already expire!',
                    'data'    => ''
                ], 201);                
            }            
        }
    }

    public function forgotPassword(Request $request)
    {
        $handphone = htmlentities($request->input('handphone'));
        $angka = floor(rand(1000, 9999));
        $current_date_time = \Carbon\Carbon::now()->toDateTimeString();
        $otp_expired = \Carbon\Carbon::now()->addMinutes(10)->toDateTimeString();
        
        $pengguna = user_mobile::select("*")
                            ->where("handphone", $handphone)
                            ->get()->toArray();
        
        if(count($pengguna) === 0){
            return response()->json([
                'success' => false,
                'message' => 'User not found!',
                'data'    => ''
            ], 204);
        } else {
                
            $kirimPesan = user_mobile::whereHandphone($handphone)->update([
                'otp'   => $angka,
                'otp_expire' => $otp_expired
            ]);

            $message = "Kode OTP Anda: " . $angka . ", RAHASIAKAN kode OTP Anda" ;
            $smsAPI = $this->smsAPI($handphone, $message);
            if($smsAPI == 'Success'){
                return response()->json([
                    'success' => true,
                    'message' => 'Token successfully send to your phone',
                    'data'    => ''
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Token failed send to your phone',
                    'data'    => ''
                ], 201);
                
            }
        }

    }

    public function passwordVerify(Request $request)
    {
        $handphone = htmlentities($request->input('handphone'));
        $otp = htmlentities($request->input('otp'));

        $current_date_time = \Carbon\Carbon::now()->toDateTimeString();

        $pengguna = user_mobile::select("*")
                            ->where("handphone", $handphone)
                            ->where("otp", $otp)
                            ->get()->toArray();
                                    
        if(count($pengguna) === 0){
            return response()->json([
                'success' => false,
                'message' => 'OTP not found!',
                'data'    => ''
            ], 204);
        } else {
            if($current_date_time < $pengguna[0]['otp_expire']){
                $createPengguna = user_mobile::whereHandphone($handphone)->update([
                    'otp'   => null,
                    'otp_expire'     => null,
                    'updatedAt' => $current_date_time,
                ]);
                if($createPengguna){
                    return response()->json([
                        'success' => true,
                        'message' => 'Success',
                        'data'    => ''
                    ], 200);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed',
                        'data'    => ''
                    ], 201);
                }
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP or OTP already expire!',
                    'data'    => ''
                ], 201);                
            }            
        }
    }

    public function updateProfile(Request $request)
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
        
        $user_id = $request->input('user_id');
        $profilPengguna = profile_mobile::select("*")
                            ->where("user_id", $user_id)
                            ->get()->toArray();
                            
        if(count($profilPengguna) === 0){
            return response()->json([
                'success' => false,
                'message' => 'User not found!',
                'data'    => ''
            ], 403);      
        } else {
            // Proses Update
            if($request->file('filetoupload')){
                $validatedData = $request->validate([
                    'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:20097152',
                ]);

                if($validatedData){
                    return response()->json([
                        'success' => false,
                        'message' => 'Please select .jpg or .png file and max size  < 20mb',
                        'data'    => ''
                    ], 403);      
                } else {
                    $image = $request->file('image')->getClientOriginalName();
                    // $path = $request->file('image')->store('public/images');
                    $path = $request->file('image')->storeAs('public/images', $image);
                }
            } else {
                $image = null;
            }

            $updateProfile = profile_mobile::whereUser_id($user_id)->update([
                'nik'   => $request->input('nik'),
                'first_name'     => $request->input('first_name'),
                'last_name'   => $request->input('last_name'),
                'phone'   => $request->input('phone'),
                'address' => $request->input('address'),
                'age' => $request->input('age'),
                'tanggal_lahir'     => $request->input('tanggal_lahir'),
                'blood_type'   => $request->input('blood_type'),
                'kd_cust'   => $request->input('jenis_pembayaran'),
                'image'     => $image,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Update Profile success!',
                'data'    => ''
            ], 200);      
        }

    }

    public function userDetail(Request $request){
        $authorization = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authorization);
        if($token != '4pb4tech'){
            return response()->json([
                'success' => false,
                'message' => 'You dont have authorization!',
                'data'    => ''
            ], 402);      
        }

        $handphone = htmlentities($request->input('handphone'));
        $pengguna = user_mobile::select("*")
                            ->where("handphone", $handphone)
                            ->get()->toArray();

        if(count($pengguna) === 0){
            return response()->json([
                'success' => false,
                'message' => 'User not found!',
                'data'    => ''
            ], 204);
        } else {
            $profilPengguna = profile_mobile::select("*")
            ->where("user_id", $pengguna[0]['id'])
            ->get()->toArray();

            return response()->json([
                'success' => true,
                'message' => 'Profile Pengguna!',
                'data'    => $profilPengguna
            ], 200);      
        }
    }

    public function changePassword(Request $request){
        $authorization = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authorization);
        if($token != '4pb4tech'){
            return response()->json([
                'success' => false,
                'message' => 'You dont have authorization!',
                'data'    => ''
            ], 402);      
        }

        $handphone = htmlentities($request->input('handphone'));
        $new_password = $request->input('new_password');
        $repeat_new_password = $request->input('repeat_new_password');

        if($new_password != $repeat_new_password){
            return response()->json([
                'success' => false,
                'message' => 'Your password not match!',
                'data'    => ''
            ], 402);      
        }

        $hashed = Hash::make($new_password, [
            'rounds' => 10,
        ]);
        
        user_mobile::whereHandphone($handphone)->update([
            'password'   => $hashed,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Password has been changed!',
            'data'    => ''
        ], 200);
    }

    public function resetPassword(Request $request){
        $handphone = htmlentities($request->input('handphone'));
        $angka = floor(rand(1000000, 9999999));
        $hashed = Hash::make($angka, [
            'rounds' => 10,
        ]);
        
        $user_mobile = user_mobile::whereHandphone($handphone)->update([
            'password'   => $hashed,
        ]);
        
        $message = "Password Sementara Anda: " . $angka . ", RAHASIAKAN Password baru Anda. Segera perbaharui Password setelah anda login." ;
        $smsAPI = $this->smsAPI($handphone, $message);
        if($smsAPI == 'Success'){
            return response()->json([
                'success' => true,
                'message' => 'New Password successfully send to your phone.'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'New Password Failed send to your phone'
            ], 422);
        }
    }

    public function verifyOTP(Request $request){
        $handphone = htmlentities($request->input('handphone'));
        $otp = htmlentities($request->input('otp'));
        $current_date_time = \Carbon\Carbon::now()->toDateTimeString();
        
        $pengguna = user_mobile::select("*")
                            ->where("handphone", $handphone)
                            ->where("otp", $otp)
                            ->get()->toArray();
                            
        if(count($pengguna) === 0){
            return response()->json([
                'success' => false,
                'message' => 'OTP not found!',
                'data'    => ''
            ], 204);
        } else {
            if($current_date_time < $pengguna[0]['otp_expire']){     
                // Proses update OTP
                $updatePengguna = user_mobile::whereHandphone($handphone)->update([
                    'otp'   => null,
                    'otp_expire'   => null,
                    'is_verified'     => 1,
                ]);
                if($updatePengguna){
                    return response()->json([
                        'success' => true,
                        'message' => 'Token Valid!',
                        'data'    => ''
                    ], 200);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'System Error, please insert otp again!',
                        'data'    => ''
                    ], 202);
                }                               
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Otp expired!',
                    'data'    => ''
                ], 202);
            }
        }
    }

    public function resendOTP(Request $request){
        $handphone = htmlentities($request->input('handphone'));
        $angka = floor(rand(1000, 9999));
        $current_date_time = \Carbon\Carbon::now()->toDateTimeString();
        $otp_expired = \Carbon\Carbon::now()->addMinutes(10)->toDateTimeString();

        $pengguna = user_mobile::select("*")
                            ->where("handphone", $handphone)
                            ->get()->toArray();
                
        $updatePengguna = user_mobile::where('handphone', $handphone)->update([
            'otp'   => $angka,
            'otp_expire'   => $otp_expired,
            'is_verified'     => false,
        ]);
        
        $message = "Kode OTP Anda: " . $angka . ", RAHASIAKAN kode OTP Anda" ;
        $smsAPI = $this->smsAPI($handphone, $message);
        if($smsAPI == 'Success'){
            return response()->json([
                'success' => true,
                'message' => 'Token successfully send to your phone.'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Token Failed send to your phone'
            ], 422);
        }
        
    }



    

}
