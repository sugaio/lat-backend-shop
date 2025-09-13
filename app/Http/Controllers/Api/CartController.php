<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('product')->where('customer_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
    
        return response()->json([
            'success' => true,
            'message' => 'List Data Cart',
            'cart' => $carts
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|integer'
        ]);

        $product = Product::findOrFail($request->product_id);
        // dd($product);
        $cartItem = Cart::where('product_id', $product->id)->where('customer_id', $request->customer_id)->first();

        if($cartItem) {
            $cartItem->increment('qty', $request->quantity ?? 1);

            $cartItem->update([
                'price' => $request->price * $cartItem->qty,
                'weight' => $product->weight * $cartItem->qty
            ]);
        } else {
            $cartItem = Cart::create([
                'product_id' => $product->id,
                'customer_id' => $request->customer_id,
                'qty' => $request->quantity ?? 1,
                'price' => $request->price * ($request->quantity ?? 1),
                'weight' => $product->weight * ($request->quantity ?? 1),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success Add to Cart',
            'quantity' => $cartItem->qty,
            'product' => $cartItem->product,
        ]);
    }

    public function getCartTotal()
    {
        $totalCart = Cart::with('product')
        ->where('customer_id', Auth::user()->id)
        ->orderBy('created_at', 'desc')
        ->sum('price');

        return response()->json([
            'success' => true,
            'message' => 'Total Cart Price',
            'total' => $totalCart
        ]);
    }

    public function getCartTotalWeight()
    {
        $totalWeight = Cart::with('product')
        ->where('customer_id', Auth::user()->id)
        ->orderBy('created_at', 'desc')
        ->sum('weight');

        return response()->json([
            'success' => true,
            'message' => 'Total Cart Weight',
            'total' => $totalWeight
        ]);
    }

    public function removeCart(Cart $cart)
    {
        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Remove Item Success',
        ]);
    }
}
