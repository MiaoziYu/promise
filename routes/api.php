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
Route::group(
    [
        'middleware' => ['auth:api'],
        'namespace' => 'Api'
    ], function () {
    Route::get('/promises/{id}', 'PromisesController@show');
    Route::get('/promises/', 'PromisesController@index');
    Route::put('/promises/{id}', 'PromisesController@update');
    Route::post('/promises/', 'PromisesController@store');
    Route::delete('/promises/{id}', 'PromisesController@destroy');
    Route::post('/promises/{promiseId}/checklists/', 'ChecklistsController@store');
    Route::put('/promises/{promiseId}/checklists/{checklistId}', 'ChecklistsController@update');
    Route::delete('/promises/{promiseId}/checklists/{checklistId}', 'ChecklistsController@destroy');
    Route::get('/profile/', 'UserProfileController@show');
    Route::put('/profile/', 'UserProfileController@update');
});
