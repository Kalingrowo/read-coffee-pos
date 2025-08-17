<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class CartController extends BaseApiController
{
    protected $cartKey;

    public function __construct()
    {
        // Each user has their own cart key
        $this->cartKey = "cart:" . auth('api')->user()->id;
    }

    /**
     * Get cart items with subtotal, tax, total.
     */
    public function index()
    {
        $cart = json_decode(Redis::get($this->cartKey), true) ?? [];

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $tax = $subtotal * 0.1; // 10% PPN
        $total = $subtotal + $tax;

        return $this->successResponse([
            'items' => $cart,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total
        ]);
    }

    /**
     * Add product to cart.
     */
    public function add(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity'   => 'required|integer|min:1'
            ]);
    
            $cart = json_decode(Redis::get($this->cartKey), true) ?? [];
    
            $product = Product::findOrFail($validated['product_id']);
    
            // If already exists, increase qty
            if (isset($cart[$product->id])) {
                $cart[$product->id]['quantity'] += $validated['quantity'];
            } else {
                $cart[$product->id] = [
                    'product_id' => $product->id,
                    'name'       => $product->name,
                    'price'      => $product->price,
                    'quantity'   => $validated['quantity']
                ];
            }
    
            Redis::set($this->cartKey, json_encode($cart));
    
            return $this->successResponse($cart, 'Item added to cart');
        } catch (\Throwable $th) {
            Log::error('Cart Add Error: ' . $th->getMessage(), ['trace' => $th->getTraceAsString()]);
            return $this->errorResponse('Failed to add cart', 500, $th->getMessage());
        }
    }

    /**
     * Update item quantity.
     */
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity'   => 'required|integer|min:1'
            ]);
    
            $cart = json_decode(Redis::get($this->cartKey), true) ?? [];
    
            if (!isset($cart[$validated['product_id']])) {
                return $this->errorResponse('Item not found in cart', 404);
            }
    
            $cart[$validated['product_id']]['quantity'] = $validated['quantity'];
    
            Redis::set($this->cartKey, json_encode($cart));
    
            return $this->successResponse($cart, 'Cart updated');
        } catch (\Throwable $th) {
            Log::error('Cart Update Error: ' . $th->getMessage(), ['trace' => $th->getTraceAsString()]);
            return $this->errorResponse('Failed to update cart', 500, $th->getMessage());
        }
    }

    /**
     * Remove item from cart.
     */
    public function remove($productId)
    {
        try {
            $cart = json_decode(Redis::get($this->cartKey), true) ?? [];
    
            if (isset($cart[$productId])) {
                unset($cart[$productId]);
                Redis::set($this->cartKey, json_encode($cart));
            }
    
            return $this->successResponse($cart, 'Item removed from cart');
        } catch (\Throwable $th) {
            Log::error('Cart Remove Item Error: ' . $th->getMessage(), ['trace' => $th->getTraceAsString()]);
            return $this->errorResponse('Failed to remove cart item', 500, $th->getMessage());
        }
    }

    /**
     * Clear cart.
     */
    public function clear()
    {
        try {
            Redis::del($this->cartKey);
    
            return $this->successResponse([], 'Cart cleared');
        } catch (\Throwable $th) {
            Log::error('Cart Clear Error: ' . $th->getMessage(), ['trace' => $th->getTraceAsString()]);
            return $this->errorResponse('Failed to clear cart', 500, $th->getMessage());
        }
    }
}
