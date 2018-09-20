<?php


Route::get('/', function (){
   return 'Home';
});

Route::get('/usuarios','UserController@index');
Route::get('/usuarios/nuevo','UserController@create');
Route::get('/usuarios/{id}','UserController@show');
Route::get('/saludo/{name}/{nickname?}','WelcomeUserController');
