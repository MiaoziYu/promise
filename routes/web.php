<?php
Route::group(['middleware' => ['auth'],], function() {
    Route::get('/', 'IndexController@index');
    Route::get('/wishes/', 'WishesController@index');
    Route::get('/wish-tickets/', 'WishTicketsController@index');
    Route::get('/statistic/', 'StatisticController@index');
});

Route::group(['namespace' => 'Auth'], function () {
    Route::get('/register', 'RegisterController@create');
    Route::post('/register', 'RegisterController@store');
    Route::get('/login', 'LoginController@create')->name('login');
    Route::post('/login', 'LoginController@store');
    Route::get('/logout', 'LoginController@destroy');
});
