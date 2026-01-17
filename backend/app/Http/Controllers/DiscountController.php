<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DiscountController extends Controller
{
    /**
     * List all discounts with pagination, search, and sorting.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Discount::with('products');

        // Search by title
        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where('title', 'like', "%{$search}%");
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->query('type'));
        }

        // Sorting
        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->query('per_page', 10);
        $discounts = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $discounts->items(),
            'pagination' => [
                'total' => $discounts->total(),
                'per_page' => $discounts->perPage(),
                'current_page' => $discounts->currentPage(),
                'last_page' => $discounts->lastPage(),
                'from' => $discounts->firstItem(),
                'to' => $discounts->lastItem(),
            ]
        ]);
    }

    /**
     * Get a single discount with detailed information.
     */
    public function show($id): JsonResponse
    {
        $discount = Discount::with('products')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $discount
        ]);
    }

    /**
     * Create a new discount.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'products' => 'nullable|array',
            'products.*' => 'integer|exists:products,id',
        ]);

        // Default to active
        $validated['is_active'] = $validated['is_active'] ?? true;

        $discount = Discount::create($validated);

        // Attach products if provided
        if (!empty($validated['products'])) {
            $discount->products()->attach($validated['products']);
        }

        $discount->load('products');

        return response()->json([
            'success' => true,
            'message' => 'Discount created successfully',
            'data' => $discount
        ], 201);
    }

    /**
     * Update an existing discount.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $discount = Discount::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:percentage,fixed',
            'value' => 'sometimes|numeric|min:0',
            'is_active' => 'sometimes|boolean',
            'products' => 'nullable|array',
            'products.*' => 'integer|exists:products,id',
        ]);

        $discount->update($validated);

        // Update products if provided
        if (isset($validated['products'])) {
            $discount->products()->sync($validated['products']);
        }

        $discount->load('products');

        return response()->json([
            'success' => true,
            'message' => 'Discount updated successfully',
            'data' => $discount
        ]);
    }

    /**
     * Delete a discount.
     */
    public function destroy($id): JsonResponse
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Discount deleted successfully'
        ]);
    }

    /**
     * Get all active discounts.
     */
    public function active(): JsonResponse
    {
        $discounts = Discount::where('is_active', true)->with('products')->get();

        return response()->json([
            'success' => true,
            'data' => $discounts
        ]);
    }

    /**
     * Get discounts for a specific product.
     */
    public function forProduct($productId): JsonResponse
    {
        $product = Product::findOrFail($productId);
        $discounts = $product->discounts()->where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => $discounts
        ]);
    }
}
