<?php

use App\Http\Controllers\API\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('log')->group(function(){

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('/usermobile', App\Http\Controllers\Api\UserMobileController::class);

// Routes API
// Routes User
Route::post('/user/login', '\App\Http\Controllers\Api\user\LoginController@login');
Route::post('/user/signup/send-otp', '\App\Http\Controllers\Api\user\LoginController@sendOTP');
Route::post('/user/signup/verify-otp', '\App\Http\Controllers\Api\user\LoginController@verifyOTP');
Route::post('/user/signup/resend-otp', '\App\Http\Controllers\Api\user\LoginController@resendOTP');
Route::post('/user/signup', '\App\Http\Controllers\Api\user\LoginController@signUp');
Route::post('/user/signup/verify', '\App\Http\Controllers\Api\user\LoginController@verify');
Route::post('/user/forgot-password', '\App\Http\Controllers\Api\user\LoginController@forgotPassword');
Route::post('/user/forgot-password-verify', '\App\Http\Controllers\Api\user\LoginController@passwordVerify');
Route::post('/user/update-profile', '\App\Http\Controllers\Api\user\LoginController@updateProfile');
Route::post('/user/detail', '\App\Http\Controllers\Api\user\LoginController@userDetail');
Route::post('/user/change-password', '\App\Http\Controllers\Api\user\LoginController@changePassword');
Route::post('/user/reset-password', '\App\Http\Controllers\Api\user\LoginController@resetPassword');

// Routes Berita
Route::get('/berita/all/{page?}', '\App\Http\Controllers\Api\berita\BeritaController@getAll');
Route::get('/berita/detail/{kode?}', '\App\Http\Controllers\Api\berita\BeritaController@detailBerita');

// Routes Promo
Route::get('/promo/terbaru/{page?}', '\App\Http\Controllers\Api\promo\PromoController@getAll');
Route::get('/promo/all/{page?}', '\App\Http\Controllers\Api\promo\PromoController@getAll');
Route::get('/promo/{kodeCabang?}', '\App\Http\Controllers\Api\promo\PromoController@promoCabang');
Route::get('/promo-klinik/detail/{kode?}/{page?}', '\App\Http\Controllers\Api\promo\PromoController@detailPromo');

// Routes Dokter
Route::post('/dokter/list', '\App\Http\Controllers\Api\dokter\DokterController@listDokterPerCabang');
Route::get('/dokter/favorit', '\App\Http\Controllers\Api\dokter\DokterController@dokterFavorit');
Route::get('/dokter/detail/{id?}', '\App\Http\Controllers\Api\dokter\DokterController@detailDokter');
Route::post('/dokter/rating', '\App\Http\Controllers\Api\dokter\DokterController@ratingDokter');

// Routes Cabang
Route::post('/klinik/list', '\App\Http\Controllers\Api\cabang\KlinikController@listCabang');
Route::get('/klinik/detail/{id?}', '\App\Http\Controllers\Api\cabang\KlinikController@detailCabang');
Route::post('/klinik/rating', '\App\Http\Controllers\Api\cabang\KlinikController@ratingCabang');

// Routes Images
Route::post('/upload/upload-images', '\App\Http\Controllers\Api\upload\ImagesController@upload');

// Routes Aktifitas
Route::post('/transaksi/list', '\App\Http\Controllers\Api\transaksi\TransaksiController@transaksiList');
Route::post('/transaksi/by-status', '\App\Http\Controllers\Api\transaksi\TransaksiController@transaksiByStatusList');
Route::post('/transaksi/aktivitas/hari-ini', '\App\Http\Controllers\Api\transaksi\TransaksiController@aktifitasHariIniList');
Route::post('/transaksi/aktivitas/detail', '\App\Http\Controllers\Api\transaksi\TransaksiController@aktivitasDetail');


// Routes Transaksi




// Route::delete('/v1/klinik/{kduser?}', 'api\v1\KlinikController@destroy');
Route::prefix('/user')->controller(UserController::class)->group(function(){
    Route::post('/login', 'login');
    Route::post('/logout', 'logout');
});

Route::prefix('/user')->middleware('log')->controller(UserController::class)->group(function(){
    Route::post('/login2', 'login');
});

});