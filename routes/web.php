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

Route::get('/', function () {
    return view('welcome');
});

Route::any('/api/test','Api\TestController@test');
Route::any('/api/user/reg','Api\TestController@reg'); //用户注册
Route::any('/api/user/login','Api\TestController@login'); //用户登录
Route::any('/api/user/list','Api\TestController@userlist')->middleware('filter'); //用户列表