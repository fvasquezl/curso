<?php


Route::get('/', function (){
   return 'Home';
});

Route::get('/usuarios','UserController@index')->name('users.index');
Route::get('/usuarios/create','UserController@create')->name('users.create');
Route::post('/usuarios','UserController@store')->name('users.store');
Route::get('/usuarios/{user}','UserController@show')->name('users.show');
Route::get('/saludo/{name}/{nickname?}','WelcomeUserController');
