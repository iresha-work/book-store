<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookCategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\OrderController;

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

Route::get('/', [App\Http\Controllers\FrontController::class, 'index'])->name('home');
Route::get('/book-detail/{name}', [App\Http\Controllers\FrontController::class, 'bookDetail'])->name('detail');
Route::post('/add-to/cart', [App\Http\Controllers\FrontController::class, 'addToCart'])->name('addtocart');
Route::post('/get/cart/qty', [App\Http\Controllers\FrontController::class, 'getCartQty'])->name('getcartqty');
Route::get('/get/cart', [App\Http\Controllers\FrontController::class, 'getCart'])->name('getcart');
Route::get('/get/cart/ajax', [App\Http\Controllers\FrontController::class, 'getCartAjax'])->name('getcartajax');
Route::post('/place/order', [App\Http\Controllers\FrontController::class, 'placeOrder'])->name('placeorder');


Route::group(['prefix' => 'admin'], function(){
    Auth::routes();
});

Route::middleware(['auth'])->prefix('admin')->group(function () {
    // book category routes
    Route::get('/book-category', [App\Http\Controllers\BookCategoryController::class, 'index'])->name('book.category.index');
    Route::get('/book-category-list-ajax', [App\Http\Controllers\BookCategoryController::class, 'create'])->name('book.category.list.ajax');
    Route::post('/book-category-store-ajax', [App\Http\Controllers\BookCategoryController::class, 'store'])->name('book.category.store.ajax');
    Route::get('/get-book-category', [App\Http\Controllers\BookCategoryController::class, 'show'])->name('book.category.show');
    Route::post('/book-category-update-ajax', [App\Http\Controllers\BookCategoryController::class, 'update'])->name('book.category.update.ajax');
    Route::post('/book-category-delete-ajax', [App\Http\Controllers\BookCategoryController::class, 'destroy'])->name('book.category.delete.ajax');
    // end book category routes

    // book routes
    Route::get('/book', [App\Http\Controllers\BookController::class, 'index'])->name('book.index');
    Route::get('/book-list-ajax', [App\Http\Controllers\BookController::class, 'create'])->name('book.list.ajax');
    Route::post('/book-store-ajax', [App\Http\Controllers\BookController::class, 'store'])->name('book.store.ajax');
    Route::get('/get-book', [App\Http\Controllers\BookController::class, 'show'])->name('book.show');
    Route::post('/book-update-ajax', [App\Http\Controllers\BookController::class, 'update'])->name('book.update.ajax');
    Route::post('/book-delete-ajax', [App\Http\Controllers\BookController::class, 'destroy'])->name('book.delete.ajax');
    Route::post('/book-delete-image-ajax', [App\Http\Controllers\BookController::class, 'destroyImage'])->name('book.delete.image.ajax');
    // end book routes

    // coupn routes
    Route::get('/coupon', [App\Http\Controllers\CouponController::class, 'index'])->name('coupon.index');
    Route::get('/coupon-list-ajax', [App\Http\Controllers\CouponController::class, 'create'])->name('coupon.list.ajax');
    Route::post('/coupon-store-ajax', [App\Http\Controllers\CouponController::class, 'store'])->name('coupon.store.ajax');
    Route::get('/get-coupon', [App\Http\Controllers\CouponController::class, 'show'])->name('coupon.show');
    Route::post('/coupon-update-ajax', [App\Http\Controllers\CouponController::class, 'update'])->name('coupon.update.ajax');
    Route::post('/coupon-delete-ajax', [App\Http\Controllers\CouponController::class, 'destroy'])->name('coupon.delete.ajax');
    // end coupn routes

    // coupn routes
    Route::get('/discount', [App\Http\Controllers\DiscountController::class, 'index'])->name('discount.index');
    Route::get('/discount-list-ajax', [App\Http\Controllers\DiscountController::class, 'create'])->name('discount.list.ajax');
    Route::post('/discount-store-ajax', [App\Http\Controllers\DiscountController::class, 'store'])->name('discount.store.ajax');
    Route::get('/get-discount', [App\Http\Controllers\DiscountController::class, 'show'])->name('discount.show');
    Route::post('/discount-update-ajax', [App\Http\Controllers\DiscountController::class, 'update'])->name('discount.update.ajax');
    Route::post('/discount-delete-ajax', [App\Http\Controllers\DiscountController::class, 'destroy'])->name('discount.delete.ajax');
    // end coupn routes

    // orders routes
    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders-list-ajax', [App\Http\Controllers\OrderController::class, 'create'])->name('orders.list.ajax');
    Route::get('/orders/detail/{id}', [App\Http\Controllers\OrderController::class, 'orderDetail'])->name('order.detail.page');
    
    // end orders routes
});