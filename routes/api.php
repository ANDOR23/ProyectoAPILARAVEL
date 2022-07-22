<?php

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
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/
Route::apiResource('v1/posts', App\Http\Controllers\Api\v1\PostController::class)->middleware('api');

Route::group([
    'middleware' => 'api',
    'prefix' => 'v1/auth'],

    function ($router){
        //Route::post('login', 'AuthController@login');
        Route::post('login', [\App\Http\Controllers\Api\v1\AuthController::class, 'login'])-> name('login');
        //Route::post('logout', 'AuthController@logout');
        Route::post('logout', [\App\Http\Controllers\Api\v1\AuthController::class, 'logout'])-> name('logout');
        //Route::post('refresh', 'AuthController@refresh');
        Route::post('refresh', [\App\Http\Controllers\Api\v1\AuthController::class, 'refresh'])-> name('refresh');
        //Route::post('me', 'AuthController@me');
        Route::post('me', [\App\Http\Controllers\Api\v1\AuthController::class, 'me'])-> name('me');
        Route::post('register', 'App\Http\Controllers\Api\v1\RegisterController@store');
    }
);