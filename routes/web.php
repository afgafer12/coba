<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/emailOTP', function () {
    return view('emailOTP');
})->name('emailOTP');
 
Route::resource('login', LoginController::class);
// Route::post('/user/login', '\App\Http\Controllers\Api\user\LoginController@login');
Route::post('send-otp-form', [LoginController::class, 'smsOTP']);
