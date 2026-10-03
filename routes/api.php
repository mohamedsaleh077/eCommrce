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
use App\Http\Controllers\Admin\Uploads;

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
        Route::get('/product/all', [Products::class, 'allProducts']);
        Route::put('/product/{id}', [Products::class, 'update']);
        Route::delete('/product/{id}', [Products::class, 'destroy']);

        // uploads management
        Route::get('/upload', [Uploads::class, 'index']);
        Route::get('/upload/{id}', [Uploads::class, 'show']);
        Route::post('/upload', [Uploads::class, 'store']);
        Route::put('/upload/{id}', [Uploads::class, 'update']);
        Route::delete('/upload/{id}', [Uploads::class, 'destroy']);
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
    Route::get('/product/image/{id}', [Uploads::class, 'getImage']);

});

Route::middleware(UserAuth::class)->group(function(){
    Route::post('/email/verify', [VerifyMail::class, 'verify']);
    Route::post('/email/code', [VerifyMail::class, 'getNewCode']);
});
