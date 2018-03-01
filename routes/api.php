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

Route::middleware('auth:api')->resource('badges', 'BadgeController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('countries', 'CountryController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('games', 'GameController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('grades', 'GradeController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('levels', 'LevelController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('schools', 'SchoolController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('users', 'UserController', ['except' => ['create', 'edit']]);

Route::middleware('auth:api')->get('/me', function (Request $request) {
    return $request->user();
});
