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

Route::resource('answers', 'API\AnswerController', ['except' => ['create', 'edit']]);
Route::resource('badges', 'API\BadgeController', ['except' => ['create', 'edit']]);
Route::resource('countries', 'API\CountryController', ['except' => ['create', 'edit']]);
Route::resource('games', 'API\GameController', ['except' => ['create', 'edit']]);
Route::resource('grades', 'API\GradeController', ['except' => ['create', 'edit']]);
Route::resource('levels', 'API\LevelController', ['except' => ['create', 'edit']]);
Route::resource('logins', 'API\LoginController', ['except' => ['create', 'edit']]);
Route::resource('questions', 'API\QuestionController', ['except' => ['create', 'edit']]);
Route::resource('schools', 'API\SchoolController', ['except' => ['create', 'edit']]);
Route::resource('users', 'API\UserController', ['except' => ['create', 'edit']]);

Route::get('badgeuser', 'API\BadgeUserController@index');
Route::post('badgeuser', 'API\BadgeUserController@store');
Route::get('badgeuser/{badgeId}/{userId}', 'API\BadgeUserController@show')
    ->where(['badgeId' => '[0-9]+', 'userId' => '[0-9]+']);
Route::put('badgeuser/{badgeId}/{userId}', 'API\BadgeUserController@update')
    ->where(['badgeId' => '[0-9]+', 'userId' => '[0-9]+']);
Route::delete('badgeuser/{badgeId}/{userId}', 'API\BadgeUserController@destroy')
    ->where(['badgeId' => '[0-9]+', 'userId' => '[0-9]+']);

Route::get('gameuser', 'API\GameUserController@index');
Route::post('gameuser', 'API\GameUserController@store');
Route::get('gameuser/{gameId}/{userId}', 'API\GameUserController@show')
    ->where(['gameId' => '[0-9]+', 'userId' => '[0-9]+']);
Route::put('gameuser/{gameId}/{userId}', 'API\GameUserController@update')
    ->where(['gameId' => '[0-9]+', 'userId' => '[0-9]+']);
Route::delete('gameuser/{gameId}/{userId}', 'API\GameUserController@destroy')
    ->where(['gameId' => '[0-9]+', 'userId' => '[0-9]+']);

Route::get('gradeschool', 'API\GradeSchoolController@index');
Route::post('gradeschool', 'API\GradeSchoolController@store');
Route::get('gradeschool/{gradeId}/{schoolId}', 'API\GradeSchoolController@show')
    ->where(['gradeId' => '[0-9]+', 'schoolId' => '[0-9]+']);
Route::put('gradeschool/{gradeId}/{schoolId}', 'API\GradeSchoolController@update')
    ->where(['gradeId' => '[0-9]+', 'schoolId' => '[0-9]+']);
Route::delete('gradeschool/{gradeId}/{schoolId}', 'API\GradeSchoolController@destroy')
    ->where(['gradeId' => '[0-9]+', 'schoolId' => '[0-9]+']);

Route::middleware('auth:api')->get('/me', function (Request $request) {
    return $request->user();
});
