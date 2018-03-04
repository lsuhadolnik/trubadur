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

Route::middleware('auth:api')->resource('badges', 'API\BadgeController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('countries', 'API\CountryController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('games', 'API\GameController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('grades', 'API\GradeController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('levels', 'API\LevelController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('schools', 'API\SchoolController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('users', 'API\UserController', ['except' => ['create', 'edit']]);

Route::middleware('auth:api')->get('schools/{schoolId}/grades/{gradeId}/level', 'API\SchoolController@level')
    ->where(['schoolId' => '[0-9]+', 'gradeId' => '[0-9]+']);
Route::middleware('auth:api')->get('grades/{gradeId}/schools/{schoolId}/level', 'API\GradeController@level')
    ->where(['gradeId' => '[0-9]+', 'schoolId' => '[0-9]+']);

Route::middleware('auth:api')->get('/me', function (Request $request) {
    return $request->user();
});
