<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * List all products with pagination, search, and sorting.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('discounts');

        // Search by name or description
        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Sorting
        $sortBy = $request->query('sort_by', 'name');
        $sortOrder = $request->query('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->query('per_page', 10);
        $products = $query->paginate($perPage);

        // Calculate final prices and savings
        $products->getCollection()->transform(function ($product) {
            $product->final_price = $this->calculateFinalPrice($product);
            $product->savings = $product->price - $product->final_price;
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]
        ]);
    }

    /**
     * Get a single product with detailed information.
     */
    public function show($id): JsonResponse
    {
        $product = Product::with('discounts')->findOrFail($id);

        $product->final_price = $this->calculateFinalPrice($product);
        $product->savings = $product->price - $product->final_price;

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Create a new product.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discounts' => 'nullable|array',
            'discounts.*' => 'integer|exists:discounts,id',
        ]);

        $product = Product::create($validated);

        // Attach discounts if provided
        if (!empty($validated['discounts'])) {
            $product->discounts()->attach($validated['discounts']);
        }

        $product->load('discounts');
        $product->final_price = $this->calculateFinalPrice($product);
        $product->savings = $product->price - $product->final_price;

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    /**
     * Update an existing product.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'discounts' => 'nullable|array',
            'discounts.*' => 'integer|exists:discounts,id',
        ]);

        $product->update($validated);

        // Update discounts if provided
        if (isset($validated['discounts'])) {
            $product->discounts()->sync($validated['discounts']);
        }

        $product->load('discounts');
        $product->final_price = $this->calculateFinalPrice($product);
        $product->savings = $product->price - $product->final_price;

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    /**
     * Delete a product.
     */
    public function destroy($id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * Calculate the final price after applying discounts.
     */
    private function calculateFinalPrice(Product $product): float
    {
        $finalPrice = $product->price;

        foreach ($product->discounts as $discount) {
            if ($discount->type === 'percentage') {
                $finalPrice -= ($finalPrice * ($discount->value / 100));
            } elseif ($discount->type === 'fixed') {
                $finalPrice -= $discount->value;
            }
        }

        return max(0, round($finalPrice, 2));
    }
}
