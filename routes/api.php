<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/promises/{id}', 'PromisesController@show');
    Route::get('/promises/', 'PromisesController@index');
    Route::put('/promises/{id}', 'PromisesController@update');
    Route::post('/promises/', 'PromisesController@store');
});
