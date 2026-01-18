<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Discount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

   
    /**
     * Test listing products with authentication succeeds
     */
    public function test_list_products_with_authentication()
    {
        Product::factory()->count(5)->create();
        
        $response = $this->withJWTToken($this->user)
            ->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'price', 'final_price', 'savings']
                ],
                'pagination'
            ]);
    }

    /**
     * Test product search functionality
     */
    public function test_search_products_by_name()
    {
        Product::factory()->create(['name' => 'Laptop Pro']);
        Product::factory()->create(['name' => 'Smartphone X']);

        $response = $this->withJWTToken($this->user)
            ->getJson('/api/products?search=Laptop');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals(1, count($data));
        $this->assertEquals('Laptop Pro', $data[0]['name']);
    }

    /**
     * Test product search by description
     */
    public function test_search_products_by_description()
    {
        Product::factory()->create([
            'name' => 'Product A',
            'description' => 'High-end gaming laptop'
        ]);
        Product::factory()->create([
            'name' => 'Product B',
            'description' => 'Budget smartphone'
        ]);

        $response = $this->withJWTToken($this->user)
            ->getJson('/api/products?search=gaming');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals(1, count($data));
    }

    /**
     * Test sorting products by name ascending
     */
    public function test_sort_products_by_name_ascending()
    {
        Product::factory()->create(['name' => 'Zebra']);
        Product::factory()->create(['name' => 'Apple']);
        Product::factory()->create(['name' => 'Mango']);

        $response = $this->withJWTToken($this->user)
            ->getJson('/api/products?sort_by=name&sort_order=asc');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('Apple', $data[0]['name']);
        $this->assertEquals('Mango', $data[1]['name']);
        $this->assertEquals('Zebra', $data[2]['name']);
    }

    /**
     * Test sorting products by name descending
     */
    public function test_sort_products_by_name_descending()
    {
        Product::factory()->create(['name' => 'Zebra']);
        Product::factory()->create(['name' => 'Apple']);
        Product::factory()->create(['name' => 'Mango']);

        $response = $this->withJWTToken($this->user)
            ->getJson('/api/products?sort_by=name&sort_order=desc');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('Zebra', $data[0]['name']);
        $this->assertEquals('Mango', $data[1]['name']);
        $this->assertEquals('Apple', $data[2]['name']);
    }

    /**
     * Test sorting by price
     */
    public function test_sort_products_by_price()
    {
        Product::factory()->create(['name' => 'Expensive', 'price' => 5000]);
        Product::factory()->create(['name' => 'Cheap', 'price' => 500]);
        Product::factory()->create(['name' => 'Medium', 'price' => 2000]);

        $response = $this->withJWTToken($this->user)
            ->getJson('/api/products?sort_by=price&sort_order=asc');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('500.00', $data[0]['price']);
        $this->assertEquals('2000.00', $data[1]['price']);
        $this->assertEquals('5000.00', $data[2]['price']);
    }

    /**
     * Test sorting by final price (with discounts)
     */
    public function test_sort_products_by_final_price_with_discounts()
    {
        $product1 = Product::factory()->create(['name' => 'Product 1', 'price' => 1000]);
        $product2 = Product::factory()->create(['name' => 'Product 2', 'price' => 1000]);
        $product3 = Product::factory()->create(['name' => 'Product 3', 'price' => 1000]);

        $discount10 = Discount::factory()->create(['type' => 'percentage', 'value' => 10]);
        $discount20 = Discount::factory()->create(['type' => 'percentage', 'value' => 20]);

        $product1->discounts()->attach($discount10);
        $product2->discounts()->attach($discount20);

        $response = $this->withJWTToken($this->user)
            ->getJson('/api/products?sort_by=final_price&sort_order=asc');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Product with 20% discount should have lowest final price
        $this->assertEquals('800.00', $data[0]['final_price']);
    }

    /**
     * Test sorting by savings
     */
    public function test_sort_products_by_savings()
    {
        $product1 = Product::factory()->create(['name' => 'P1', 'price' => 1000]);
        $product2 = Product::factory()->create(['name' => 'P2', 'price' => 1000]);

        $discount5 = Discount::factory()->create(['type' => 'percentage', 'value' => 5]);
        $discount15 = Discount::factory()->create(['type' => 'percentage', 'value' => 15]);

        $product1->discounts()->attach($discount5);
        $product2->discounts()->attach($discount15);

        $response = $this->withJWTToken($this->user)
            ->getJson('/api/products?sort_by=savings&sort_order=desc');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Product with 15% discount should have higher savings
        $this->assertEquals('150.00', $data[0]['savings']);
    }

    /**
     * Test pagination
     */
    public function test_pagination_works_correctly()
    {
        Product::factory()->count(25)->create();

        $response = $this->withJWTToken($this->user)
            ->getJson('/api/products?per_page=10&page=1');

        $response->assertStatus(200);
        $pagination = $response->json('pagination');
        $this->assertEquals(25, $pagination['total']);
        $this->assertEquals(10, $pagination['per_page']);
        $this->assertEquals(1, $pagination['current_page']);
        $this->assertEquals(3, $pagination['last_page']);
    }

    /**
     * Test get single product
     */
    public function test_get_single_product()
    {
        $product = Product::factory()->create(['name' => 'Test Product']);

        $response = $this->withJWTToken($this->user)
            ->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Test Product')
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'name', 'price', 'final_price', 'savings']
            ]);
    }

    /**
     * Test get non-existent product returns 404
     */
    public function test_get_non_existent_product_returns_404()
    {
        $response = $this->withJWTToken($this->user)
            ->getJson('/api/products/99999');

        $response->assertStatus(404);
    }

    /**
     * Test create product with valid data
     */
    public function test_create_product_with_valid_data()
    {
        $data = [
            'name' => 'New Product',
            'description' => 'Test description',
            'price' => 1000
        ];

        $response = $this->withJWTToken($this->user)
            ->postJson('/api/products', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'New Product')
            ->assertJsonPath('data.price', '1000.00');

        $this->assertDatabaseHas('products', ['name' => 'New Product']);
    }

    /**
     * Test create product without required fields
     */
    public function test_create_product_without_required_fields()
    {
        $data = ['name' => 'Product'];

        $response = $this->withJWTToken($this->user)
            ->postJson('/api/products', $data);

        $response->assertStatus(422);
    }

    /**
     * Test create product with discounts
     */
    public function test_create_product_with_discounts()
    {
        $discount = Discount::factory()->create();

        $data = [
            'name' => 'Product with discount',
            'price' => 1000,
            'discounts' => [$discount->id]
        ];

        $response = $this->withJWTToken($this->user)
            ->postJson('/api/products', $data);

        $response->assertStatus(201);
        $productId = $response->json('data.id');
        
        $product = Product::find($productId);
        $this->assertTrue($product->discounts->contains($discount));
    }

    /**
     * Test update product
     */
    public function test_update_product()
    {
        $product = Product::factory()->create(['name' => 'Old Name', 'price' => 500]);

        $data = [
            'name' => 'New Name',
            'price' => 1500
        ];

        $response = $this->withJWTToken($this->user)
            ->putJson("/api/products/{$product->id}", $data);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.price', '1500.00');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'New Name']);
    }

    /**
     * Test delete product
     */
    public function test_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->withJWTToken($this->user)
            ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    /**
     * Test product with multiple discounts calculates final price correctly
     */
    public function test_product_with_multiple_discounts()
    {
        $product = Product::factory()->create(['price' => 1000]);
        $discount1 = Discount::factory()->create(['type' => 'percentage', 'value' => 10]);
        $discount2 = Discount::factory()->create(['type' => 'fixed', 'value' => 50]);

        $product->discounts()->attach([$discount1->id, $discount2->id]);

        $response = $this->withJWTToken($this->user)
            ->getJson("/api/products/{$product->id}");

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // 10% off 1000 = 900, then 50 off = 850
        $this->assertEquals(850, $data['final_price']);
        $this->assertEquals(150, $data['savings']);
    }

    /**
     * Test invalid sort field defaults to name
     */
    public function test_invalid_sort_field_defaults_to_name()
    {
        Product::factory()->create(['name' => 'A']);
        Product::factory()->create(['name' => 'Z']);

        $response = $this->withJWTToken($this->user)
            ->getJson('/api/products?sort_by=invalid_field');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('A', $data[0]['name']);
    }

    /**
     * Test invalid sort order defaults to asc
     */
    public function test_invalid_sort_order_defaults_to_asc()
    {
        Product::factory()->create(['name' => 'B']);
        Product::factory()->create(['name' => 'A']);

        $response = $this->withJWTToken($this->user)
            ->getJson('/api/products?sort_order=invalid');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('A', $data[0]['name']);
    }

    /**
     * Test product with negative price after discount is set to 0
     */
    public function test_product_final_price_never_negative()
    {
        $product = Product::factory()->create(['price' => 100]);
        $discount = Discount::factory()->create(['type' => 'fixed', 'value' => 200]);

        $product->discounts()->attach($discount);

        $response = $this->withJWTToken($this->user)
            ->getJson("/api/products/{$product->id}");

        $data = $response->json('data');
        $this->assertEquals(0, $data['final_price']);
        $this->assertGreaterThanOrEqual(0, $data['final_price']);
    }
}
