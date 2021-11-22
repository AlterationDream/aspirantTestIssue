<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', "\App\Http\Controllers\PostController@index");
Route::get('/trailer/{id}', "\App\Http\Controllers\PostController@show");
Route::post('/trailer/{id}/like', "\App\Http\Controllers\PostController@like");
Route::get('/login', "\App\Http\Controllers\UserController@login")->name('login');
Route::post('/login', "\App\Http\Controllers\UserController@authenticate");
Route::get('/logout', "\App\Http\Controllers\UserController@logout")->name('logout');
Route::get('/register', "\App\Http\Controllers\UserController@register")->name('register');
Route::post('/register', "\App\Http\Controllers\UserController@store");

