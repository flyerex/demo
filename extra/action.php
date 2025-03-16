<?php

namespace App\Actions\API;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

//В данном случае контроллер позволяет получить данные о корзине пользователя, добавить товар в корзину, обновить товар в корзине и удалить товар из корзины.
class CartAction
{
    public function getCart(){
        $cartItems = Cart::where('user_id', Auth::id())->get();

        $products = [];
        $total = 0;

        foreach ($cartItems as $item){
            $price = number_format(($item->products->price - ($item->products->price*$item->products->discount/100)), 2, '.', ' ');
            $subtotal = number_format($price*$item->prod_qty, 2, '.', ' ');
            $total += $subtotal;
            array_push($products, [
                'cart_id'=>$item->user_id,
                'prod_id'=>$item->prod_id,
                'title'=>$item->products->title,
                'price'=> $price,
                'subtotal' => $subtotal,
                'img'=>$item->products->imageUrl1,
                'size'=>$item->products->size,
                'qty'=> $item->prod_qty,
            ]);
        }

        return ["cartItems" => $cartItems, "products" => $products, "total" => $total];
    }

    public function addToCart($request){
        $product_id = $request->input('product_id');
        $product_qty = $request->input('product_qty');
        $updated = $request->input('update');

        if(Auth::check()){
            $prod_check = Product::query()->where('id', $product_id)->first();
            if($prod_check){
                if (Cart::where('prod_id', $product_id)->where('user_id', Auth::id())->exists()){
                    $cartItem = Cart::where('prod_id', $product_id)->where('user_id', Auth::id())->first();
                    if($updated){
                        $cartItem->prod_qty = $product_qty;
                    }else {
                        $cartItem->prod_qty += $product_qty;
                    }
                    $cartItem->update();
                    return response()->json(['status' => $prod_check->title." Updated"]);
                }else{
                    $cartItem = new Cart();
                    $cartItem->prod_id = $product_id;
                    $cartItem->prod_qty = $product_qty;
                    $cartItem->user_id = Auth::id();
                    $cartItem->save();
                    return response()->json(['status' => $prod_check->title." Added to cart"]);
                }

            }
        }else{
            return response()->json(['status' => 'Login to Continue']);
        }
    }

    public function updateCart($request){
        $product_id = $request->input('product_id');
        $product_qty = $request->input('product_qty');

        if(Auth::check()){
            $prod_check = Product::query()->where('id', $product_id)->first();
            if($prod_check){
                if (Cart::where('prod_id', $product_id)->where('user_id', Auth::id())->exists()){
                    $cartItem = Cart::where('prod_id', $product_id)->where('user_id', Auth::id())->first();
                    $cartItem->prod_qty = $product_qty;
                    $cartItem->update();
                    return response()->json(['status' => $prod_check->title." Updated"]);
                }else{
                    return response()->json(['status' => " Error"]);
                }
            }
        }else{
            return response()->json(['status' => 'Login to Continue']);
        }
    }

    public function removeFromCart($request){
        if(Auth::check()){
            $product_id = $request->input('product_id');
            if(Cart::where('prod_id', $product_id)->where('user_id', Auth::id())->exists()){
                $cartItem = Cart::where('prod_id', $product_id)->where('user_id', Auth::id())->first();
                $cartItem->delete();
                return response()->json(['status' => " Removed from cart"]);
            }
        }else{
            return response()->json(['status' => 'Login to Continue']);
        }
    }

}
