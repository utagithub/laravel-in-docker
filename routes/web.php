<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function (Request $request) {
    //Log::info('Hello chaoyang');
    //Log::info('Hello beijing');
    //Log::info('Hello zhongguo');
    //\App\Jobs\TestJobQueue::dispatch();
    return view('welcome');

});

//Jaeger链路追踪测试路由
Route::middleware('jaeger')->group(function () {
    Route::get('/jaeger/index','\App\Http\Controllers\JaegerController@index');
    Route::get('/jaeger/insert','\App\Http\Controllers\JaegerController@insert');
});

