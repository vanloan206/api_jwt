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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::pattern('id','([0-9]+)');

Route::group(['middleware' => 'api','prefix' => 'api'], function () {
    Route::post('register', 'APIController@register');
    Route::post('login', 'APIController@login');

    Route::group(['middleware' => 'jwt-auth'], function () {
      	Route::post('/', 'APIController@getUserDetails');

        Route::group(['prefix' => 'user'], function(){
            Route::post('/', 'APIController@index');
        });

        Route::group(['prefix' => 'news'], function() {
            Route::post('/', 'NewsController@index');
            Route::post('/create', 'NewsController@store');
            Route::post('/edit/{id}', 'NewsController@edit');
            Route::post('/update/{id}', 'NewsController@update');
            Route::post('/del/{id}', 'NewsController@destroy');
        });
    });
});
