<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DiscountModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test discount can be created
     */
    public function test_discount_can_be_created()
    {
        $discount = Discount::factory()->create([
            'title' => 'Test Discount',
            'type' => 'percentage',
            'value' => 10
        ]);

        $this->assertDatabaseHas('discounts', [
            'title' => 'Test Discount',
            'type' => 'percentage',
            'value' => 10
        ]);
    }

    /**
     * Test discount has fillable attributes
     */
    public function test_discount_fillable_attributes()
    {
        $data = [
            'title' => 'Summer Sale',
            'type' => 'percentage',
            'value' => 20,
            'is_active' => true
        ];

        $discount = Discount::create($data);

        $this->assertEquals('Summer Sale', $discount->title);
        $this->assertEquals('percentage', $discount->type);
        $this->assertEquals(20, $discount->value);
        $this->assertTrue($discount->is_active);
    }

    /**
     * Test discount belongs to many products
     */
    public function test_discount_has_many_products()
    {
        $discount = Discount::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $discount->products()->attach([$product1->id, $product2->id]);

        $this->assertEquals(2, $discount->products()->count());
        $this->assertTrue($discount->products->contains($product1));
        $this->assertTrue($discount->products->contains($product2));
    }

    /**
     * Test discount soft delete
     */
    public function test_discount_soft_deletes()
    {
        $discount = Discount::factory()->create();
        $discountId = $discount->id;

        $discount->delete();

        $this->assertSoftDeleted('discounts', ['id' => $discountId]);
        $this->assertNull(Discount::find($discountId));
        $this->assertNotNull(Discount::withTrashed()->find($discountId));
    }

    /**
     * Test discount value casting
     */
    public function test_discount_value_cast_to_decimal()
    {
        $discount = Discount::factory()->create(['value' => 10.50]);

        $this->assertEquals('10.50', $discount->value);
    }

    /**
     * Test discount is_active casting to boolean
     */
    public function test_discount_is_active_cast_to_boolean()
    {
        $discount1 = Discount::factory()->create(['is_active' => true]);
        $discount2 = Discount::factory()->create(['is_active' => false]);

        $this->assertTrue($discount1->is_active);
        $this->assertFalse($discount2->is_active);
    }

    /**
     * Test discount can sync products
     */
    public function test_discount_can_sync_products()
    {
        $discount = Discount::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $product3 = Product::factory()->create();

        $discount->products()->attach([$product1->id, $product2->id]);
        $this->assertEquals(2, $discount->products()->count());

        $discount->products()->sync([$product2->id, $product3->id]);
        $this->assertEquals(2, $discount->products()->count());
        $this->assertTrue($discount->products->contains($product2));
        $this->assertTrue($discount->products->contains($product3));
        $this->assertFalse($discount->products->contains($product1));
    }

    /**
     * Test discount can detach all products
     */
    public function test_discount_can_detach_all_products()
    {
        $discount = Discount::factory()->create();
        $product = Product::factory()->create();

        $discount->products()->attach($product);
        $this->assertEquals(1, $discount->products()->count());

        $discount->products()->detach();
        $this->assertEquals(0, $discount->products()->count());
    }

    /**
     * Test discount table has correct columns
     */
    public function test_discount_table_structure()
    {
        $discount = Discount::factory()->create([
            'title' => 'Test',
            'type' => 'percentage',
            'value' => 10
        ]);

        $this->assertIsInt($discount->id);
        $this->assertIsString($discount->title);
        $this->assertIsString($discount->type);
        $this->assertTrue(is_numeric($discount->value));
        $this->assertIsBool($discount->is_active);
        $this->assertNotNull($discount->created_at);
        $this->assertNotNull($discount->updated_at);
        $this->assertNull($discount->deleted_at);
    }

    /**
     * Test discount timestamps
     */
    public function test_discount_timestamps_update()
    {
        $discount = Discount::factory()->create(['title' => 'Original']);
        $createdAt = $discount->created_at;

        sleep(1);
        $discount->update(['title' => 'Updated']);

        $this->assertEquals($createdAt, $discount->created_at);
        $this->assertGreaterThan($createdAt, $discount->updated_at);
    }

    /**
     * Test percentage type discount
     */
    public function test_percentage_type_discount()
    {
        $discount = Discount::factory()->create(['type' => 'percentage', 'value' => 15]);

        $this->assertEquals('percentage', $discount->type);
        $this->assertEquals(15, $discount->value);
    }

    /**
     * Test fixed type discount
     */
    public function test_fixed_type_discount()
    {
        $discount = Discount::factory()->create(['type' => 'fixed', 'value' => 500]);

        $this->assertEquals('fixed', $discount->type);
        $this->assertEquals(500, $discount->value);
    }

    /**
     * Test discount can toggle active status
     */
    public function test_discount_can_toggle_active_status()
    {
        $discount = Discount::factory()->create(['is_active' => true]);
        $this->assertTrue($discount->is_active);

        $discount->update(['is_active' => false]);
        $this->assertFalse($discount->is_active);

        $discount->update(['is_active' => true]);
        $this->assertTrue($discount->is_active);
    }

    /**
     * Test multiple discounts can be applied to same product
     */
    public function test_multiple_discounts_same_product()
    {
        $product = Product::factory()->create();
        $discount1 = Discount::factory()->create(['type' => 'percentage', 'value' => 10]);
        $discount2 = Discount::factory()->create(['type' => 'fixed', 'value' => 50]);

        $product->discounts()->attach([$discount1->id, $discount2->id]);

        $this->assertEquals(2, $product->discounts()->count());
    }

    /**
     * Test discount factory creates valid discount
     */
    public function test_discount_factory_creates_valid_discount()
    {
        $discount = Discount::factory()->create();

        $this->assertNotNull($discount->id);
        $this->assertNotNull($discount->title);
        $this->assertContains($discount->type, ['percentage', 'fixed']);
        $this->assertGreaterThanOrEqual(0, $discount->value);
    }

    /**
     * Test discount with zero value
     */
    public function test_discount_with_zero_value()
    {
        $discount = Discount::factory()->create(['value' => 0]);

        $this->assertEquals(0, $discount->value);
    }

    /**
     * Test discount with decimal value
     */
    public function test_discount_with_decimal_value()
    {
        $discount = Discount::factory()->create(['value' => 15.75]);

        $this->assertEquals('15.75', $discount->value);
    }

    /**
     * Test discount title length
     */
    public function test_discount_title_max_length()
    {
        $longTitle = str_repeat('A', 255);
        $discount = Discount::factory()->create(['title' => $longTitle]);

        $this->assertEquals(255, strlen($discount->title));
    }
}
