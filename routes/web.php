<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/logout',[ApplicationController::class,'logout']);
Route::get('/auth/{view}',[ApplicationController::class,'auth'])->where('view','(.*)');
Route::get('admin/{view}',[ApplicationController::class,'admin'])->where('view','(.*)');
Route::get('/{view}',[ApplicationController::class,'shop'])->where('view','(.*)');

