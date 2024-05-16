<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\YoungApprenticesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/user', [UserController::class, 'store']);
Route::post('/user/login', [UserController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/user', [UserController::class, 'user']);

    Route::post('/young_apprentices', [YoungApprenticesController::class, 'store']);
    Route::get('/young_apprentices', [YoungApprenticesController::class, 'index']);

});
