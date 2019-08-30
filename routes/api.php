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

Route::middleware('auth:api')->resource('answers', 'API\AnswerController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('badges', 'API\BadgeController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('countries', 'API\CountryController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('difficulties', 'API\DifficultyController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('games', 'API\GameController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->get('games/{id}/statistics', 'API\GameController@statistics');
Route::middleware('auth:api')->resource('grades', 'API\GradeController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->get('levels/find', 'API\LevelController@find');
Route::middleware('auth:api')->resource('levels', 'API\LevelController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('logins', 'API\LoginController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('questions', 'API\QuestionController', ['except' => ['create', 'edit']]);

// DANGER!!
Route::post('questions/generate', 'API\QuestionController@generate');


Route::middleware('auth:api')->resource('schools', 'API\SchoolController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->resource('users', 'API\UserController', ['except' => ['create', 'edit']]);
Route::middleware('auth:api')->put('users/{userId}/complete', 'API\UserController@complete')
    ->where(['userId' => '[0-9]+']);

Route::middleware('auth:api')->get('badgeuser', 'API\BadgeUserController@index');
Route::middleware('auth:api')->post('badgeuser', 'API\BadgeUserController@store');
Route::middleware('auth:api')->get('badgeuser/{badgeId}/{userId}', 'API\BadgeUserController@show')
    ->where(['badgeId' => '[0-9]+', 'userId' => '[0-9]+']);
Route::middleware('auth:api')->put('badgeuser/{badgeId}/{userId}', 'API\BadgeUserController@update')
    ->where(['badgeId' => '[0-9]+', 'userId' => '[0-9]+']);
Route::middleware('auth:api')->delete('badgeuser/{badgeId}/{userId}', 'API\BadgeUserController@destroy')
    ->where(['badgeId' => '[0-9]+', 'userId' => '[0-9]+']);

Route::middleware('auth:api')->get('gameuser', 'API\GameUserController@index');
Route::middleware('auth:api')->post('gameuser', 'API\GameUserController@store');
Route::middleware('auth:api')->get('gameuser/{gameId}/{userId}', 'API\GameUserController@show')
    ->where(['gameId' => '[0-9]+', 'userId' => '[0-9]+']);
Route::middleware('auth:api')->put('gameuser/{gameId}/{userId}', 'API\GameUserController@update')
    ->where(['gameId' => '[0-9]+', 'userId' => '[0-9]+']);
Route::middleware('auth:api')->delete('gameuser/{gameId}/{userId}', 'API\GameUserController@destroy')
    ->where(['gameId' => '[0-9]+', 'userId' => '[0-9]+']);
Route::middleware('auth:api')->put('gameuser/{gameId}/{userId}/finish', 'API\GameUserController@finish')
    ->where(['gameId' => '[0-9]+', 'userId' => '[0-9]+']);

Route::middleware('auth:api')->get('gradeschool', 'API\GradeSchoolController@index');
Route::middleware('auth:api')->post('gradeschool', 'API\GradeSchoolController@store');
Route::middleware('auth:api')->get('gradeschool/{gradeId}/{schoolId}', 'API\GradeSchoolController@show')
    ->where(['gradeId' => '[0-9]+', 'schoolId' => '[0-9]+']);
Route::middleware('auth:api')->put('gradeschool/{gradeId}/{schoolId}', 'API\GradeSchoolController@update')
    ->where(['gradeId' => '[0-9]+', 'schoolId' => '[0-9]+']);
Route::middleware('auth:api')->delete('gradeschool/{gradeId}/{schoolId}', 'API\GradeSchoolController@destroy')
    ->where(['gradeId' => '[0-9]+', 'schoolId' => '[0-9]+']);


// DANGER!! 
Route::resource('rhythmBars', 'API\RhythmBarController', ['except' => ['create', 'edit']]);
//Route::middleware('auth:api')->resource('rhythm_exercise', 'API\RhythmExerciseController', ['except' => ['create', 'edit']]);

// DANGER
//Route::post('rhythm_exercise_bar', 'API\RhythmExerciseBar', ['except' => ['create', 'edit']]);
Route::post('rhythmExerciseBar/import/musicXML', 'API\RhythmBarController@importMusicXML');
Route::post('rhythmExercise/generate/{n}/{level}', 'API\RhythmExerciseController@generateNForLevel');

Route::post('rhythmBars/{id}/occurrences', 'API\RhythmBarOccurrenceController@create');
Route::get('rhythmBars/{id}/occurrences', 'API\RhythmBarOccurrenceController@index');
Route::get('rhythmBars/{id}/occurrences/{fid}', 'API\RhythmBarOccurrenceController@show');
Route::put('rhythmBars/{id}/occurrences/{fid}', 'API\RhythmBarOccurrenceController@update');
Route::delete('rhythmBars/{id}/occurrences/{fid}', 'API\RhythmBarOccurrenceController@destroy');

Route::resource('barInfo', 'API\BarInfoController', ['except' => ['create', 'edit']]);
Route::resource('rhythmFeatures', 'API\RhythmFeatureController', ['except' => ['create', 'edit']]);

Route::post('rhythmFeatures/{fid}/occurrences', 'API\RhythmFeatureOccurrenceController@store');
Route::    get('rhythmFeatures/{fid}/occurrences', 'API\RhythmFeatureOccurrenceController@index');
Route::    get('rhythmFeatures/{fid}/occurrences/{level}/{bar}', 'API\RhythmFeatureOccurrenceController@show');
Route::    put('rhythmFeatures/{fid}/occurrences', 'API\RhythmFeatureOccurrenceController@update');
Route:: delete('rhythmFeatures/{fid}/occurrences/{level}/{bar}', 'API\RhythmFeatureOccurrenceController@destroy');

