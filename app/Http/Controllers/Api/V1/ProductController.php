<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProductController extends BaseApiController
{
    public function index()
    {
        try {
            // Return all products with their category
            $products = Product::with('category')->oldest('name')->get();
            return $this->successResponse($products, 'Product retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Product index failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve products', 500, $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => [
                    'required|string|max:255',
                    Rule::unique('products', 'name')->ignore($request->id),
                ],
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|integer|min:0',
                'stock' => 'nullable|integer|min:0'
            ]);
    
            $product = Product::create($validated);
    
            return $this->successResponse($product, 'Product created successfully', 201);
        } catch (\Exception $e) {
            Log::error('Product store failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to create product', 500, $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $product = Product::with('category')->findOrFail($id);
            return $this->successResponse($product, 'Product retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Product show failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve product', 500, $e->getMessage());
        }

    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0'
            ]);
    
            $product = Product::findOrFail($id);
            $product->update($validated);
        
            return $this->successResponse($product, 'Product updated successfully');
        } catch (\Exception $e) {
            Log::error('Product update failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to update product', 500, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);

            // TODO : validate cart and other transaction

            $product->delete();
            
            return $this->successResponse(null, 'Product deleted successfully');
        } catch (\Exception $e) {
            Log::error('Product delete failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to delete product', 500, $e->getMessage());
        }
    }
}
