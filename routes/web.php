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

use App\Services\Client\UserService;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/user/{id}', function($id) {
    $userService = new UserService();
    //$user = $userService->getUserInfoViaRpc($id);
    $user = $userService->getUserInfoViaSwoole($id);
    return $user;
});