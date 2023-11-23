<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductExport;

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
Route::get('/export', function () {
    return Excel::download(new ProductExport, 'products.xlsx');
});
Route::get('/image', 'ImageController@getImage');
Route::get('auth/logout',[ApplicationController::class,'logout']);
Route::get('/auth/{view}',[ApplicationController::class,'auth'])->where('view','(.*)');
Route::get('admin',[ApplicationController::class,'admin'])->where('view','(.*)');
Route::get('admin/{view}',[ApplicationController::class,'admin'])->where('view','(.*)');
Route::get('/{view}',[ApplicationController::class,'shop'])->where('view','(.*)');

