<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GameApiController;

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

Route::prefix('/v1')->group(function () {

    Route::get('/', function () {
        return response()->json(['organization' => 'Student Cyber Games']);
    });

    Route::get('/games', [GameApiController::class, 'getAll']);
    Route::post('/games', [GameApiController::class, 'new']);
    Route::get('/games/{uuid}', [GameApiController::class, 'get']);
    Route::put('/games/{uuid}', [GameApiController::class, 'update']);
    Route::delete('/games/{uuid}', [GameApiController::class, 'remove']);
});