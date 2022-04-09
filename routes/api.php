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

Route::middleware('auth:api')->get('/users', function (Request $request) {
    return $request->user();
});

Route::apiResources(['users'=>'API\UserController']);

Route::get('users/pdf', function(){
    $users = \App\User::all();
    return view('downloads.users', compact('users'));
});

Route::get('count', function(){ return \App\User::count();});
Route::get('profile', 'API\UserController@profile');
Route::get('download-pdf', 'API\UserController@downloadPdf');
Route::get('download-excel', 'API\UserController@downloadExcel');
Route::get('download-csv', 'API\UserController@downloadCsv');

// Route::put('profile', 'API\UserController@updateProfile');
//above covered in the web route
Route::get('findUser', 'API\UserController@search');

