<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Collection;

class ProductController extends Controller
{
    /**
     * Database columns that can be sorted at database level
     */
    private array $databaseColumns = ['id', 'name', 'description', 'price', 'created_at', 'updated_at'];

    /**
     * Calculated fields that must be sorted in PHP
     */
    private array $calculatedFields = ['final_price', 'savings', 'original_price'];

    /**
     * List all products with pagination, search, and sorting.
     */
    public function index(Request $request): JsonResponse
    {
        // Apply search filter
        $query = $this->applySearchFilter(Product::with('discounts'), $request);

        // Get sort parameters
        $sortParams = $this->getSortParameters($request);

        // Apply database-level sorting
        if ($this->isDbColumn($sortParams['sortBy'])) {
            $query = $this->applyDatabaseSort($query, $sortParams);
        }

        // Get all matching products
        $allProducts = $query->get();

        // Enrich products with calculated fields
        $allProducts = $this->enrichProductsWithCalculations($allProducts);

        // Apply in-memory sorting for calculated fields
        if ($this->isCalculatedField($sortParams['sortBy']) && $sortParams['sortBy'] !== 'original_price') {
            $allProducts = $this->applySortToCalculatedField($allProducts, $sortParams);
        }

        // Apply pagination 
        $paginationParams = $this->getPaginationParameters($request);
        $paginatedResult = $this->applyPagination($allProducts, $paginationParams);

        return response()->json([
            'success' => true,
            'data' => $paginatedResult['products'],
            'pagination' => $paginatedResult['pagination']
        ]);
    }

    /**
     * Get a single product with detailed information.
     */
    public function show($id): JsonResponse
    {
        $product = Product::with('discounts')->findOrFail($id);
        $product = $this->enrichProductWithCalculations($product);

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

        if (!empty($validated['discounts'])) {
            $product->discounts()->attach($validated['discounts']);
        }

        $product->load('discounts');
        $product = $this->enrichProductWithCalculations($product);

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

        if (isset($validated['discounts'])) {
            $product->discounts()->sync($validated['discounts']);
        }

        $product->load('discounts');
        $product = $this->enrichProductWithCalculations($product);

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
     * Apply search filter to the query
     */
    private function applySearchFilter($query, Request $request)
    {
        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        return $query;
    }

    /**
     * Get and validate sort parameters from request
     */
    private function getSortParameters(Request $request): array
    {
        $sortBy = $request->query('sort_by', 'name');
        $sortOrder = strtolower($request->query('sort_order', 'asc'));

        // Validate sort order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        // Validate sort field
        if (!in_array($sortBy, array_merge($this->databaseColumns, $this->calculatedFields))) {
            $sortBy = 'name';
        }

        return [
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder
        ];
    }

    /**
     * Apply database-level sorting to query
     */
    private function applyDatabaseSort($query, array $sortParams)
    {
        $sortBy = $sortParams['sortBy'];
        $sortOrder = $sortParams['sortOrder'];

        // Map 'original_price' to 'price' column
        if ($sortBy === 'original_price') {
            $sortBy = 'price';
        }

        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Check if a field is a database column
     */
    private function isDbColumn(string $field): bool
    {
        return in_array($field, $this->databaseColumns);
    }

    /**
     * Check if a field is a calculated field
     */
    private function isCalculatedField(string $field): bool
    {
        return in_array($field, $this->calculatedFields);
    }

    /**
     * Enrich a single product with calculated values
     */
    private function enrichProductWithCalculations(Product $product): Product
    {
        $product->final_price = $this->calculateFinalPrice($product);
        $product->savings = $product->price - $product->final_price;
        return $product;
    }

    /**
     * Enrich multiple products with calculated values
     */
    private function enrichProductsWithCalculations(Collection $products): Collection
    {
        return $products->map(function ($product) {
            return $this->enrichProductWithCalculations($product);
        });
    }

    /**
     * Apply sorting to calculated field in memory
     */
    private function applySortToCalculatedField(Collection $products, array $sortParams): Collection
    {
        $sortBy = $sortParams['sortBy'];
        $sortOrder = $sortParams['sortOrder'];
        $isDescending = $sortOrder === 'desc';

        return $products->sortBy(function ($product) use ($sortBy) {
            return $product->{$sortBy};
        }, SORT_REGULAR, $isDescending);
    }

    /**
     * Get pagination parameters from request
     */
    private function getPaginationParameters(Request $request): array
    {
        return [
            'perPage' => (int) $request->query('per_page', 10),
            'page' => (int) $request->query('page', 1)
        ];
    }

    /**
     * Apply pagination to collection and return formatted result
     */
    private function applyPagination(Collection $products, array $params): array
    {
        $perPage = $params['perPage'];
        $page = $params['page'];
        $total = $products->count();

        // Calculate pagination info
        $lastPage = ceil($total / $perPage);
        $from = ($page - 1) * $perPage + 1;
        $to = min($page * $perPage, $total);

        // Slice the collection for current page
        $paginatedProducts = $products->slice(($page - 1) * $perPage, $perPage)->values();

        return [
            'products' => $paginatedProducts,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $lastPage,
                'from' => $total === 0 ? 0 : $from,
                'to' => $total === 0 ? 0 : $to,
            ]
        ];
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

