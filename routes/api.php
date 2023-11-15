<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DetailSaleOfProductController;
use App\Http\Controllers\ImportHistoryController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MethodOfPaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShoppingCartController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleOffController;
use App\Http\Controllers\ShipMethodController;

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
    Route::get('/shoppingcart', [ShoppingCartController::class, 'show']);
    Route::post('/shoppingcart/insert', [ShoppingCartController::class, 'store']);
    Route::post('/shoppingcart/get_carts_not_checkout', [ShoppingCartController::class, 'get_carts_not_checkout']);
    Route::delete('/shoppingcart/remove/{shoppingCart}', [ShoppingCartController::class, 'destroy']);
    Route::post('/shoppingcart/updates', [ShoppingCartController::class, 'updates']);
    Route::post('/order', [OrderController::class, 'store']);
    Route::post('/order/get', [OrderController::class, 'show']);
    Route::post('/order/getAll', [OrderController::class, 'showAll']);
    Route::get('/bill/processing', [BillController::class, 'processing']);

    Route::get('/bill/{bill}', [BillController::class, 'show']);
    Route::post('/bill/update/{bill}', [BillController::class, 'update']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/products/inventory', [ProductController::class, 'inventory']);
    Route::post('/InsertProduct', [ProductController::class, 'store']);
    Route::post('/ImportHistory', [ImportHistoryController::class, 'store']);
    Route::post('/comment', [CommentController::class, 'store']);
    Route::post('/checklogin', [AuthController::class, 'checklogin']);
    Route::post('/getImagesSpecial', [ProductController::class, 'getImagesSpecial']);
    Route::post('/createSaleOff', [SaleOffController::class, 'store']);
    Route::post('/updateSaleOff/{id}', [SaleOffController::class, 'update']);
    Route::post('/showSales', [SaleOffController::class, 'index']);
    Route::post('/showSale/{id}', [SaleOffController::class, 'show']);
    Route::post('/UpdateDetailSale', [DetailSaleOfProductController::class, 'update']);

});
//POST
Route::post('/products/top10', [ProductController::class, 'top10']);
Route::post('/product/tongDoanhSo', [ProductController::class, 'tongDoanhSo']);
Route::post('/product/tongNguoiDatHang', [ProductController::class, 'tongNguoiDatHang']);
Route::post('/bill/thongke', [BillController::class, 'thongke']);

Route::post('/product/amount', [ProductController::class,'amount']);
Route::get('/filter/{id}', [ProductController::class,'filter']);
Route::get('/comment/{idProduct}',[CommentController::class,'index']);
Route::get('/ship-method', [ShipMethodController::class, 'index']);
Route::get('/method-of-payment', [MethodOfPaymentController::class, 'index']);
Route::post('/signup', [UserController::class, 'store']);
Route::post('/InsertCategory', [CategoryController::class, 'store']);

Route::post('/UpdateCategory/{id}', [CategoryController::class, 'update']);
Route::post('/UpdateProduct/{id}', [ProductController::class, 'update']);
Route::delete('/delete/category/{id}', [CategoryController::class, 'destroy']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
Route::delete('/sales/{id}', [SaleOffController::class, 'destroy']);
//GET
Route::get('/products/{by}/{keyword}', [ProductController::class, 'index']);

Route::get('/product/{id}', [ProductController::class, 'show']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/materials', [MaterialController::class, 'index']);
Route::get('/colors', [ColorController::class, 'index']);
Route::get('/suppliers', [SupplierController::class, 'index']);
