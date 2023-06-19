<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\LoginController;
use App\Http\Controllers\api\UserController;
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



//first is count second is time
Route::group(['prefix'=>'oauth2', 'middleware'=>['throttle:20,1', 'cors']], function(){
    Route::post('/login', [LoginController::class, 'login'])->name("login");
    Route::get('/user', [UserController::class, 'index']);
    Route::group(['middleware' => 'auth:api'], function() {
        Route::post('/user/create', [UserController::class, 'save']);
        Route::get('/user/edit/{id}', [UserController::class, 'edit']);
        Route::post('/user/update/{id}', [UserController::class, 'update']);
        Route::delete('/user/delete/{id}', [UserController::class, 'delete']);
        Route::get('logout',  [LoginController::class, 'logout']);
    });
});
