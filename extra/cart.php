<?php

namespace App\Http\Controllers\API;

use App\Actions\API\CartAction;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;

//Контроллер который обрабатывает запросы из условного cart.vue и содержит методы из action.php
class CartController extends Controller
{
    public function getCart(){
        $cart = CartAction::getCart();
        return response()->json(['products' => $cart['products'],
            'total' =>  number_format($cart['total'], 2, '.', ' '),
            'count' => count($cart['cartItems'])]);

    }

    public function addToCart(Request $request){
        return CartAction::addToCart($request);
    }

    public function updateCart(Request $request){
        return CartAction::updateCart($request);
    }

    public function removeFromCart(Request $request){
        return CartAction::removeFromCart($request);
    }


}
