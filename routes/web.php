<?php
Route::get('/', 'IndexController@index');

Route::group(['namespace' => 'Auth'], function () {
    Route::get('/register', 'RegisterController@create');
    Route::post('/register', 'RegisterController@store');
    Route::get('/login', 'LoginController@create');
    Route::post('/login', 'LoginController@store');
    Route::get('/logout', 'LoginController@destroy');
});
