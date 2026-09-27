<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Signup;
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\VerifyMail;
use App\Http\Middleware\UserAuth;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/signup', Signup::class);
Route::post('/login', Login::class);

Route::post('/email/verify', [VerifyMail::class, 'verify'])->middleware(UserAuth::class);
Route::post('/email/code', [VerifyMail::class, 'getNewCode'])->middleware(UserAuth::class);
