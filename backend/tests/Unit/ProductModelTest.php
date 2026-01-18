<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Discount;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test product can be created
     */
    public function test_product_can_be_created()
    {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 1000
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 1000
        ]);
    }

    /**
     * Test product has fillable attributes
     */
    public function test_product_fillable_attributes()
    {
        $data = [
            'name' => 'New Product',
            'description' => 'Description',
            'price' => 500
        ];

        $product = Product::create($data);

        $this->assertEquals('New Product', $product->name);
        $this->assertEquals('Description', $product->description);
        $this->assertEquals(500, $product->price);
    }

    /**
     * Test product belongs to many discounts
     */
    public function test_product_has_many_discounts()
    {
        $product = Product::factory()->create();
        $discount1 = Discount::factory()->create();
        $discount2 = Discount::factory()->create();

        $product->discounts()->attach([$discount1->id, $discount2->id]);

        $this->assertEquals(2, $product->discounts()->count());
        $this->assertTrue($product->discounts->contains($discount1));
        $this->assertTrue($product->discounts->contains($discount2));
    }

    /**
     * Test product soft delete
     */
    public function test_product_soft_deletes()
    {
        $product = Product::factory()->create();
        $productId = $product->id;

        $product->delete();

        $this->assertSoftDeleted('products', ['id' => $productId]);
        $this->assertNull(Product::find($productId));
        $this->assertNotNull(Product::withTrashed()->find($productId));
    }

    /**
     * Test product price casting
     */
    public function test_product_price_cast_to_decimal()
    {
        $product = Product::factory()->create(['price' => 1000.50]);

        $this->assertEquals('1000.50', $product->price);
    }

    /**
     * Test product can update discounts
     */
    public function test_product_can_sync_discounts()
    {
        $product = Product::factory()->create();
        $discount1 = Discount::factory()->create();
        $discount2 = Discount::factory()->create();
        $discount3 = Discount::factory()->create();

        $product->discounts()->attach([$discount1->id, $discount2->id]);
        $this->assertEquals(2, $product->discounts()->count());

        $product->discounts()->sync([$discount2->id, $discount3->id]);
        $this->assertEquals(2, $product->discounts()->count());
        $this->assertTrue($product->discounts->contains($discount2));
        $this->assertTrue($product->discounts->contains($discount3));
        $this->assertFalse($product->discounts->contains($discount1));
    }

    /**
     * Test product can detach all discounts
     */
    public function test_product_can_detach_all_discounts()
    {
        $product = Product::factory()->create();
        $discount = Discount::factory()->create();

        $product->discounts()->attach($discount);
        $this->assertEquals(1, $product->discounts()->count());

        $product->discounts()->detach();
        $this->assertEquals(0, $product->discounts()->count());
    }

    /**
     * Test product table has correct columns
     */
    public function test_product_table_structure()
    {
        $product = Product::factory()->create([
            'name' => 'Test',
            'description' => 'Desc',
            'price' => 100
        ]);

        $this->assertIsInt($product->id);
        $this->assertIsString($product->name);
        $this->assertIsString($product->description);
        $this->assertTrue(is_numeric($product->price));
        $this->assertNotNull($product->created_at);
        $this->assertNotNull($product->updated_at);
        $this->assertNull($product->deleted_at);
    }

    /**
     * Test product timestamps
     */
    public function test_product_timestamps_update()
    {
        $product = Product::factory()->create(['name' => 'Original']);
        $createdAt = $product->created_at;

        sleep(1);
        $product->update(['name' => 'Updated']);

        $this->assertEquals($createdAt, $product->created_at);
        $this->assertGreaterThan($createdAt, $product->updated_at);
    }

    /**
     * Test multiple products with same discount
     */
    public function test_multiple_products_can_share_discount()
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $discount = Discount::factory()->create();

        $product1->discounts()->attach($discount);
        $product2->discounts()->attach($discount);

        $this->assertTrue($product1->discounts->contains($discount));
        $this->assertTrue($product2->discounts->contains($discount));
        $this->assertEquals(2, $discount->products()->count());
    }

    /**
     * Test product factory creates valid product
     */
    public function test_product_factory_creates_valid_product()
    {
        $product = Product::factory()->create();

        $this->assertNotNull($product->id);
        $this->assertNotNull($product->name);
        $this->assertGreaterThanOrEqual(0, $product->price);
    }
}
