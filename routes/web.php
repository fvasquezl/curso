<?php


Route::get('/', function (){
   return 'Home';
});

Route::get('/usuarios','UserController@index')->name('users.index');
Route::get('/usuarios/create','UserController@create')->name('users.create');
Route::post('/usuarios','UserController@store')->name('users.store');
Route::get('/usuarios/papelera','UserController@trashed')->name('users.trashed');

Route::get('/usuarios/{user}/edit','UserController@edit')->name('users.edit');
Route::get('/usuarios/{user}','UserController@show')->name('users.show');
Route::put('/usuarios/{user}','UserController@update')->name('users.update');

Route::patch('/usuarios/{user}/papelera','UserController@trash')->name('users.trash');
Route::delete('/usuarios/{id}','UserController@destroy')->name('users.destroy');
Route::patch('/usuarios/{user}/restaurar','UserController@restore')->name('users.restore');

// Profile
Route::get('/editar-perfil/', 'ProfileController@edit');
Route::put('/editar-perfil/', 'ProfileController@update');

//Professions
Route::get('/profesiones/', 'ProfessionController@index')->name('professions.index');
Route::delete('/profesiones/{profession}', 'ProfessionController@destroy')->name('professions.destroy');


//Skills
Route::get('/habilidades/', 'SkillController@index')->name('skills.index');

