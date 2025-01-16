<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\HelloController;
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
//Povinne
// Route::get('/', [HelloController::class, 'view']);

Route::get('/', function(){ return File::get(public_path() . '/index.html');});
Route::get('/{any}', function(){ return File::get(public_path() . '/index.html');});
Route::get('/{any}/{any2}', function(){ return File::get(public_path() . '/index.html');});

// Route::get('/games', [GameController::class, 'getAll']); 
// Route::post('/games', [GameController::class, 'new']); 
// Route::get('/games/new', [GameController::class, 'newForm']); 
// Route::get('/games/update/{uuid}', [GameController::class, 'updateForm']); 
// Route::put('/games/{uuid}', [GameController::class, 'update']); 

// Route::get('/games/{uuid}', [GameController::class, 'get']); 
// Route::put('/games/{uuid}', [GameController::class, 'update']); 
// Route::delete('/games/{uuid}', [GameController::class, 'remove']); 

//odpovedi 
//201 = vytvorena
//400 = bad request
//404 = not found
//422 = spatny format, treba plochy