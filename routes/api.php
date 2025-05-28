<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BrandsController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\FooterController;
use App\Http\Controllers\API\products;
use App\Http\Controllers\API\SettingContoller;
use App\Http\Controllers\API\SlideController;
use App\Http\Controllers\API\TempImageController;
use App\Http\Controllers\front\AccountController;
use App\Http\Controllers\front\FavoriteController;
use App\Http\Controllers\front\FooterController as FrontFooterController;
use App\Http\Controllers\front\OrderController;
use App\Http\Controllers\front\ProductController;
use App\Http\Controllers\front\UserController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/* 
    Auth Admin
*/
Route::post('/login', [AuthController::class, 'login']);
// get all products
Route::get('/getAllProduct', [ProductController::class, 'getAllProduct']);
// last feature products
Route::get('/getLastProduct', [ProductController::class, 'lastProducts']);
/* 
    feature products
*/
Route::get('/getFeatureProduct', [ProductController::class, 'featureProducts']);
/* 
    get product detail
*/
Route::get('/getProductDetail/{id}', [ProductController::class, 'getProductDetail']);
// get all categories
Route::get('/getCategory', [ProductController::class, 'getCategory']);
// get all brands
Route::get('/getBrands', [ProductController::class, 'getBrands']);
/* 
    login & register a user
*/

// -----------------------------------------------------------
Route::post('/account/register', [AuthController::class, 'register']);
/* 
    login & login a user
*/
Route::post('/account/login', [AccountController::class, 'login']);
/* 
    login & update profile a user
*/

Route::middleware(['jwt.auth', 'checkRoleUser'])->group(function () {
    Route::post('/saveorder', [OrderController::class, 'SaveOrder']);
    Route::get('/getOrder', [AccountController::class, 'getOrders']);
    Route::get('/getOrderDetail/{id}', [AccountController::class, 'getOrderDetails']);
    //  Payment Routes
    Route::post('/payments', [PaymentController::class, 'created'])->name('payments.create');
    Route::post('/favorites/{productId}', [FavoriteController::class, 'addToFavorites']);
    Route::delete('/favorites/{productId}', [FavoriteController::class, 'removeFromFavorites']);
    Route::get('/favorites/{productId}', [FavoriteController::class, 'isFavorite']);
    Route::get('/account/user', [AccountController::class, 'user']);
    Route::post('/account/updateprofile', [AccountController::class, 'updateProfile']);
    Route::get('/getAllfavorites', [FavoriteController::class, 'getFavorites']);
    // reset password
    Route::post('/account/resetpassword', [AccountController::class, 'resetPassword']);
});

Route::get('/user', [UserController::class, 'index']);
// middleware('auth:sanctum')->
Route::middleware(['jwt.auth', 'checkRoleAdmin'])->group(function () {
    Route::apiResource('/products', products::class);
    // category
    Route::apiResource('/categories', CategoryController::class);
    // brands
    Route::apiResource('/brands', BrandsController::class);
    // test temp image
    Route::apiResource('/temp', TempImageController::class);
    // order 
    Route::get('/order', [\App\Http\Controllers\API\OrderController::class, 'index']);
    Route::get('/order/{id}', [\App\Http\Controllers\API\OrderController::class, 'show']);
    // update order status
    Route::post('/order/{id}', [\App\Http\Controllers\API\OrderController::class, 'updateOrder']);

    Route::apiResource('/slides', SlideController::class);

    // route settings
    Route::get('/settings', [SettingContoller::class, 'index']);
    Route::post('/settings', [SettingContoller::class, 'store']);
    // footer
    Route::apiResource('/footer', FooterController::class);
});

// Route::apiResource('/category', CategoryController::class);
// Route::get('category', CategoryController::class)->name('index');

// slide show routes
Route::get('account/getslides', [AccountController::class, 'getSlides']);
// get all footer
Route::get('/getfooter', [FrontFooterController::class, 'getAllFooter']);


