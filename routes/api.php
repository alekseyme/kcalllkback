<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', 'AuthController@login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('/projects', 'ProjectController');
    Route::resource('/managers', 'ManagerController');
    Route::resource('/users', 'UserController');

    Route::patch('/user/changepwd', 'UserController@changepassword');
    Route::post('/userprojects', 'ProjectController@userprojects');
    Route::post('/project/search', 'ProjectController@search');
    Route::post('/project/export', 'ProjectController@export');
    Route::put('/project/editrow/{id}', 'ProjectController@editrow');
    
    Route::post('/me', 'AuthController@me');
    Route::post('/logout', 'AuthController@logout');

    Route::post('/da/{rid}', 'AudioController@da');
});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
