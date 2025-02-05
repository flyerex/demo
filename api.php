<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Апи роуты для обеспечения работы магазина с vue.js. Как видно все стандартно и есть обязательная авторизация для некоторых роутов.

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'products'], function()
{
    Route::get('filters', [\App\Http\Controllers\API\Product\FilterListController::class, "getFilters"]);
    Route::get('getComments/{id}', [\App\Http\Controllers\API\CommentController::class, "getComments"]);
    Route::post('get', [\App\Http\Controllers\API\Product\ProductController::class, "getProducts"]);
    Route::get('homePageProducts', [\App\Http\Controllers\API\Product\ProductController::class, "getHomePageProducts"]);
    Route::get('recommendedProducts', [\App\Http\Controllers\API\Product\ProductController::class, "getRecommendedProducts"]);
    Route::get('{product}', [\App\Http\Controllers\API\Product\ProductController::class, "getProduct"]);

});

Route::group(['middleware' => 'auth:sanctum'], function ($router)
{
    Route::group(['prefix' => 'cart'], function(){
        Route::get('get', [\App\Http\Controllers\API\CartController::class, "getCart"]);
        Route::post('add', [\App\Http\Controllers\API\CartController::class, "addToCart"]);
        Route::post('update', [\App\Http\Controllers\API\CartController::class, "updateCart"]);
        Route::post('remove', [\App\Http\Controllers\API\CartController::class, "removeFromCart"]);
    });

    Route::group(['prefix' => 'wishlist'], function(){
        Route::post('add', [\App\Http\Controllers\API\WishlistController::class, "addToWishlist"]);
        Route::post('remove', [\App\Http\Controllers\API\WishlistController::class, "removeFromWishlist"]);
        Route::post('checkItem', [\App\Http\Controllers\API\WishlistController::class, "checkItemInWishlist"]);
        Route::get('get', [\App\Http\Controllers\API\WishlistController::class, "getWishlist"]);
    });

    Route::group(['prefix' => 'personal-account'], function() {
        Route::get('user-orders', [\App\Http\Controllers\API\UserController::class, 'getOrders']);
        Route::get('user-orders/{id}', [\App\Http\Controllers\API\UserController::class, 'getOrderItem']);


        Route::get('user-data', [\App\Http\Controllers\API\UserController::class, 'getUserData']);
        Route::post('update-user-data', [\App\Http\Controllers\API\UserController::class, 'updateUserData']);
    });

    Route::post('addComment', [\App\Http\Controllers\API\CommentController::class, "addComment"]);
    Route::get('getCommentsUser', [\App\Http\Controllers\API\CommentController::class, "getCommentsUser"]);

    Route::get('checkout', [\App\Http\Controllers\API\CheckoutController::class, "index"]);
    Route::post('place-order', [\App\Http\Controllers\API\CheckoutController::class, "placeOrder"]);
    Route::post('proceed-to-pay', [\App\Http\Controllers\API\CheckoutController::class, "payment"]);
    Route::post('add-rating', [\App\Http\Controllers\API\RatingController::class, "add"]);

});
