<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Signup;
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\VerifyMail;
use App\Http\Controllers\Categories;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\UserAuth;
use App\Http\Controllers\Products;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware([UserAuth::class, IsAdmin::class])
    ->group(function(){
        // category management
        Route::post('/category', [Categories::class, 'store']);
        Route::put('/category/{id}', [Categories::class, 'update']);
        Route::delete('/category/{id}', [Categories::class, 'destroy']);

        // product management
        Route::post('/product', [Products::class, 'store']);
        Route::put('/product/{id}', [Products::class, 'update']);
        Route::delete('/product/{id}', [Products::class, 'destroy']);
    });

Route::middleware([])->group(function(){
    // registration
    Route::post('/signup', Signup::class);
    Route::post('/login', Login::class);

    // Categories
    Route::get('/category', [Categories::class, 'index']);
    Route::get('/category/{id}', [Categories::class, 'show']);

    // Products
    Route::get('/product', [Products::class, 'index']);
    Route::get('/product/{id}', [Products::class, 'show']);
});

Route::middleware(UserAuth::class)->group(function(){
    Route::post('/email/verify', [VerifyMail::class, 'verify']);
    Route::post('/email/code', [VerifyMail::class, 'getNewCode']);
});
