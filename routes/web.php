<?php


Route::get('/', function (){
   return 'Home';
});

Route::get('/usuarios','UserController@index')->name('users.index');
Route::get('/usuarios/create','UserController@create')->name('users.create');
Route::post('/usuarios','UserController@store')->name('users.store');
Route::get('/usuarios/{user}/edit','UserController@edit')->name('users.edit');
Route::get('/usuarios/{user}','UserController@show')->name('users.show');
Route::put('/usuarios/{user}','UserController@update')->name('users.update');
Route::delete('/usuarios/{user}','UserController@destroy')->name('users.delete');
Route::get('/saludo/{name}/{nickname?}','WelcomeUserController');
// Profile
Route::get('/editar-perfil/', 'ProfileController@edit');
Route::put('/editar-perfil/', 'ProfileController@update');
//Professions
Route::get('/profesiones/', 'ProfessionController@index');

//Skills
Route::get('/habilidades/', 'SkillController@index');

