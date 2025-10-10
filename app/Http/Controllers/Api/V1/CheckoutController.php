<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class CheckoutController extends BaseApiController
{
    private $cartKey;

    public function __construct()
    {
        $this->cartKey = "cart:user:" . auth('api')->user()->id;
    }


    /**
     * Checkout using Cart (Redis)
     */
    public function checkoutFromCart(Request $request)
    {
        dd('test cheeckout');

        try {
            $cart = json_decode(Redis::get($this->cartKey), true) ?? [];
            if (empty($cart)) {
                return $this->errorResponse('Cart is empty', 400);
            }

            $validated = $request->validate([
                'payment_method' => 'required|string',
                'paid_amount'    => 'required|numeric|min:0'
            ]);

            DB::beginTransaction();

            // calculate totals
            $subTotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
            $tax = $subTotal * 0.1; // 10% tax
            $grandTotal = $subTotal + $tax;

            if ($validated['paid_amount'] < $grandTotal) {
                return $this->errorResponse('Insufficient payment amount', 400);
            }

            $order = Order::create([
                'user_id'      => auth()->id(),
                'total_amount' => $subTotal,
                'tax'          => $tax,
                'grand_total'  => $grandTotal,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);

                // reduce stock
                Product::where('id', $item['product_id'])
                    ->decrement('stock', $item['quantity']);
            }

            Payment::create([
                'order_id' => $order->id,
                'amount'   => $validated['paid_amount'],
                'method'   => $validated['payment_method'],
            ]);

            DB::commit();

            // clear cart after success
            Redis::del($this->cartKey);

            $change = $validated['paid_amount'] - $grandTotal;

            return $this->successResponse([
                'order'  => $order->load('items', 'payment'),
                'change' => $change,
            ], 'Checkout successful', 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error("Cart Checkout Error: " . $e->getMessage());
            return $this->errorResponse('Checkout failed', 500);
        }
    }
}
