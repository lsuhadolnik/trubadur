<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::get('register/verify/{token?}', 'Auth\RegisterController@verify');
Route::middleware('guest')->get('/', 'Auth\LoginController@showLoginForm');
Route::get('/generator', 'GeneratorController@index');
Route::get('/home', 'HomeController@index');
Route::get('/{vue_capture?}', 'HomeController@index')->where('vue_capture', '[\/\w\.-]*');
