<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseApiController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends BaseApiController
{
    public function index()
    {
        try {
            $categories = Category::all();
            return $this->successResponse($categories, 'Categories retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Category index failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve categories', 500, $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $category = Category::create($validated);

            return $this->successResponse($category, 'Category created successfully', 201);
        } catch (\Exception $e) {
            Log::error('Category store failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to create category', 500, $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return $this->successResponse($category, 'Category retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Category show failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve category', 500, $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $category = Category::findOrFail($id);
            $category->update($validated);

            return $this->successResponse($category, 'Category updated successfully');
        } catch (\Exception $e) {
            Log::error('Category update failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to update category', 500, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return $this->successResponse(null, 'Category deleted successfully');
        } catch (\Exception $e) {
            Log::error('Category delete failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to delete category', 500, $e->getMessage());
        }
    }
}
