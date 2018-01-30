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
    Route::put('/promises/reorder', 'PromisesController@reorder');
    Route::put('/promises/{id}', 'PromisesController@update');
    Route::put('/promises/{id}/finish', 'PromisesController@finish');
    Route::post('/promises/', 'PromisesController@store');
    Route::delete('/promises/{id}', 'PromisesController@destroy');

    Route::post('/promises/{promiseId}/checklists/', 'ChecklistsController@store');
    Route::put('/promises/{promiseId}/checklists/{checklistId}', 'ChecklistsController@update');
    Route::delete('/promises/{promiseId}/checklists/{checklistId}', 'ChecklistsController@destroy');

    Route::get('/profile/', 'UserProfileController@show');
    Route::put('/profile/', 'UserProfileController@update');

    Route::get('/wishes/{id}', 'WishesController@show');
    Route::get('/wishes/', 'WishesController@index');
    Route::post('/wishes/', 'WishesController@store');
    Route::put('/wishes/reorder', 'WishesController@reorder');
    Route::put('/wishes/{id}', 'WishesController@update');
    Route::put('/wishes/{id}/purchase', 'WishesController@purchase');
    Route::put('/wishes/{id}/share', 'WishesController@share');
    Route::put('/wishes/{id}/contribute', 'WishesController@contribute');
    Route::delete('/wishes/{id}', 'WishesController@destroy');

    Route::get('/wish-tickets/', 'WishTicketsController@index');
    Route::put('/wish-tickets/{id}/claim', 'WishTicketsController@claim');
    Route::delete('/wish-tickets/{id}', 'WishTicketsController@destroy');

    Route::get('/habits/{id}', 'HabitsController@show');
    Route::get('/habits/', 'HabitsController@index');
    Route::post('/habits/', 'HabitsController@store');
    Route::put('/habits/reorder', 'HabitsController@reorder');
    Route::put('/habits/{id}', 'HabitsController@update');
    Route::put('/habits/{id}/check', 'HabitsController@check');
    Route::delete('/habits/{id}', 'HabitsController@destroy');

    route::get('/weekly-challenges/{id}', 'WeeklyChallengesController@show');
    route::get('/weekly-challenges/', 'WeeklyChallengesController@index');
    Route::put('/weekly-challenges/reorder', 'WeeklyChallengesController@reorder');
    route::put('/weekly-challenges/{id}', 'WeeklyChallengesController@update');
    route::put('/weekly-challenges/{id}/check', 'WeeklyChallengesController@check');
    route::post('/weekly-challenges/', 'WeeklyChallengesController@store');
    route::delete('/weekly-challenges/{id}', 'WeeklyChallengesController@destroy');
});
