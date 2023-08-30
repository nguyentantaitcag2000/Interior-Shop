<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShoppingCartController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => ['web']], function () {
    Route::post('/signin', [AuthController::class, 'signin']);

});

Route::post('/shoppingcart', [ShoppingCartController::class, 'index']);
Route::post('/signup', [UserController::class, 'store']);
Route::post('/InsertCategory', [CategoryController::class, 'store']);
Route::post('/InsertProduct', [ProductController::class, 'store']);
Route::post('/UpdateCategory/{id}', [CategoryController::class, 'update']);
Route::post('/UpdateProduct/{id}', [ProductController::class, 'update']);
Route::delete('/delete/category/{id}', [CategoryController::class, 'destroy']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
Route::get('/product/{id}', [ProductController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/materials', [MaterialController::class, 'index']);
Route::get('/colors', [ColorController::class, 'index']);
Route::get('/suppliers', [SupplierController::class, 'index']);
