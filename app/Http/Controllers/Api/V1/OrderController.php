<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        // Fetch orders with optional filters (date range, user_id)
        // Include order items and payment relation
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        // Validate request:
        // - items: array of product_id, quantity
        // - payment method & amount
        // - optional note

        // Begin DB transaction

        // Calculate total
        // Deduct stock from products
        // Create payment record
        // Create order
        // Create order items

        // Commit transaction

        // Return created order with relations
    }

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        // Return order detail with items and payment
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, $id)
    {
        // Update order note or payment info (if allowed)
    }

    /**
     * Remove the specified order.
     */
    public function destroy($id)
    {
        // Delete/cancel order
        // (Optional) Reverse stock quantities if needed
    }
}
