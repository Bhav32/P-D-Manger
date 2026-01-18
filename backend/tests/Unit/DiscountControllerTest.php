<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Discount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DiscountControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test listing discounts with authentication
     */
    public function test_list_discounts_with_authentication()
    {
        Discount::factory()->count(5)->create();

        $response = $this->withJWTToken($this->user)
            ->getJson('/api/discounts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'type', 'value']
                ],
                'pagination'
            ]);
    }

    /**
     * Test search discounts by title
     */
    public function test_search_discounts_by_title()
    {
        Discount::factory()->create(['title' => 'Summer Sale']);
        Discount::factory()->create(['title' => 'Winter Sale']);

        $response = $this->withJWTToken($this->user)
            ->getJson('/api/discounts?search=Summer');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals(1, count($data));
        $this->assertEquals('Summer Sale', $data[0]['title']);
    }

    /**
     * Test filter discounts by type
     */
    public function test_filter_discounts_by_type()
    {
        Discount::factory()->create(['type' => 'percentage', 'value' => 10]);
        Discount::factory()->create(['type' => 'percentage', 'value' => 20]);
        Discount::factory()->create(['type' => 'fixed', 'value' => 100]);

        $response = $this->withJWTToken($this->user)
            ->getJson('/api/discounts?type=percentage');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals(2, count($data));
        
        foreach ($data as $discount) {
            $this->assertEquals('percentage', $discount['type']);
        }
    }

    /**
     * Test sort discounts by created_at descending
     */
    public function test_sort_discounts_by_created_at()
    {
        Discount::factory()->create(['title' => 'First']);
        sleep(1);
        Discount::factory()->create(['title' => 'Second']);

        $response = $this->withJWTToken($this->user)
            ->getJson('/api/discounts?sort_by=created_at&sort_order=desc');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals('Second', $data[0]['title']);
    }

    /**
     * Test pagination for discounts
     */
    public function test_discount_pagination()
    {
        Discount::factory()->count(15)->create();

        $response = $this->withJWTToken($this->user)
            ->getJson('/api/discounts?per_page=5&page=2');

        $response->assertStatus(200);
        $pagination = $response->json('pagination');
        $this->assertEquals(15, $pagination['total']);
        $this->assertEquals(5, $pagination['per_page']);
        $this->assertEquals(2, $pagination['current_page']);
    }

    /**
     * Test get single discount
     */
    public function test_get_single_discount()
    {
        $discount = Discount::factory()->create(['title' => 'Test Discount']);

        $response = $this->withJWTToken($this->user)
            ->getJson("/api/discounts/{$discount->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Test Discount');
    }

    /**
     * Test create discount with percentage type
     */
    public function test_create_percentage_discount()
    {
        $data = [
            'title' => 'Summer Sale 20%',
            'type' => 'percentage',
            'value' => 20,
            'is_active' => true
        ];

        $response = $this->withJWTToken($this->user)
            ->postJson('/api/discounts', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'percentage')
            ->assertJsonPath('data.value', '20.00');

        $this->assertDatabaseHas('discounts', ['title' => 'Summer Sale 20%']);
    }

    /**
     * Test create discount with fixed type
     */
    public function test_create_fixed_discount()
    {
        $data = [
            'title' => 'Holiday Special ₹500',
            'type' => 'fixed',
            'value' => 500,
            'is_active' => true
        ];

        $response = $this->withJWTToken($this->user)
            ->postJson('/api/discounts', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.type', 'fixed')
            ->assertJsonPath('data.value', '500.00');
    }

    /**
     * Test percentage discount cannot exceed 100%
     */
    public function test_percentage_discount_cannot_exceed_100()
    {
        $data = [
            'title' => 'Invalid Discount',
            'type' => 'percentage',
            'value' => 150
        ];

        $response = $this->withJWTToken($this->user)
            ->postJson('/api/discounts', $data);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Percentage discount cannot exceed 100%');
    }

    /**
     * Test discount value cannot be negative
     */
    public function test_discount_value_cannot_be_negative()
    {
        $data = [
            'title' => 'Invalid Discount',
            'type' => 'fixed',
            'value' => -10
        ];

        $response = $this->withJWTToken($this->user)
            ->postJson('/api/discounts', $data);

        $response->assertStatus(422);
    }

    /**
     * Test create discount without required fields
     */
    public function test_create_discount_without_required_fields()
    {
        $data = ['title' => 'Incomplete'];

        $response = $this->withJWTToken($this->user)
            ->postJson('/api/discounts', $data);

        $response->assertStatus(422);
    }

    /**
     * Test create discount with invalid type
     */
    public function test_create_discount_with_invalid_type()
    {
        $data = [
            'title' => 'Test',
            'type' => 'invalid_type',
            'value' => 10
        ];

        $response = $this->withJWTToken($this->user)
            ->postJson('/api/discounts', $data);

        $response->assertStatus(422);
    }

    /**
     * Test update discount
     */
    public function test_update_discount()
    {
        $discount = Discount::factory()->create(['title' => 'Old Title', 'value' => 10]);

        $data = [
            'title' => 'New Title',
            'value' => 25
        ];

        $response = $this->withJWTToken($this->user)
            ->putJson("/api/discounts/{$discount->id}", $data);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'New Title')
            ->assertJsonPath('data.value', '25.00');

        $this->assertDatabaseHas('discounts', ['id' => $discount->id, 'title' => 'New Title']);
    }

    /**
     * Test update discount with percentage exceeding 100
     */
    public function test_update_discount_percentage_exceeds_100()
    {
        $discount = Discount::factory()->create(['type' => 'percentage', 'value' => 50]);

        $data = ['value' => 150];

        $response = $this->withJWTToken($this->user)
            ->putJson("/api/discounts/{$discount->id}", $data);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Percentage discount cannot exceed 100%');
    }

    /**
     * Test update discount type from percentage to fixed
     */
    public function test_update_discount_type()
    {
        $discount = Discount::factory()->create(['type' => 'percentage', 'value' => 10]);

        $data = [
            'type' => 'fixed',
            'value' => 500
        ];

        $response = $this->withJWTToken($this->user)
            ->putJson("/api/discounts/{$discount->id}", $data);

        $response->assertStatus(200)
            ->assertJsonPath('data.type', 'fixed')
            ->assertJsonPath('data.value', '500.00');
    }

    /**
     * Test delete discount
     */
    public function test_delete_discount()
    {
        $discount = Discount::factory()->create();

        $response = $this->withJWTToken($this->user)
            ->deleteJson("/api/discounts/{$discount->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('discounts', ['id' => $discount->id]);
    }

    /**
     * Test discount default is_active is true
     */
    public function test_discount_default_is_active()
    {
        $data = [
            'title' => 'New Discount',
            'type' => 'percentage',
            'value' => 10
        ];

        $response = $this->withJWTToken($this->user)
            ->postJson('/api/discounts', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.is_active', true);
    }

    /**
     * Test create discount with max value 99999.99
     */
    public function test_create_discount_with_max_value()
    {
        $data = [
            'title' => 'High Value Discount',
            'type' => 'fixed',
            'value' => 99999.99
        ];

        $response = $this->withJWTToken($this->user)
            ->postJson('/api/discounts', $data);

        $response->assertStatus(201);
    }

    /**
     * Test create discount exceeds max value
     */
    public function test_create_discount_exceeds_max_value()
    {
        $data = [
            'title' => 'Over Max Discount',
            'type' => 'fixed',
            'value' => 100000
        ];

        $response = $this->withJWTToken($this->user)
            ->postJson('/api/discounts', $data);

        $response->assertStatus(422);
    }

    /**
     * Test get non-existent discount
     */
    public function test_get_non_existent_discount()
    {
        $response = $this->withJWTToken($this->user)
            ->getJson('/api/discounts/99999');

        $response->assertStatus(404);
    }

    /**
     * Test percentage discount value 0
     */
    public function test_percentage_discount_value_zero()
    {
        $data = [
            'title' => 'Zero Discount',
            'type' => 'percentage',
            'value' => 0
        ];

        $response = $this->withJWTToken($this->user)
            ->postJson('/api/discounts', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.value', '0.00');
    }

    /**
     * Test percentage discount value 100
     */
    public function test_percentage_discount_value_100()
    {
        $data = [
            'title' => 'Free Product',
            'type' => 'percentage',
            'value' => 100
        ];

        $response = $this->withJWTToken($this->user)
            ->postJson('/api/discounts', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.value', '100.00');
    }

    /**
     * Test update discount with partial data
     */
    public function test_update_discount_partial_data()
    {
        $discount = Discount::factory()->create(['title' => 'Original', 'value' => 10]);

        $data = ['title' => 'Updated'];

        $response = $this->withJWTToken($this->user)
            ->putJson("/api/discounts/{$discount->id}", $data);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated')
            ->assertJsonPath('data.value', '10.00');
    }

    /**
     * Test discount with decimal values
     */
    public function test_discount_with_decimal_values()
    {
        $data = [
            'title' => 'Decimal Discount',
            'type' => 'percentage',
            'value' => 15.50
        ];

        $response = $this->withJWTToken($this->user)
            ->postJson('/api/discounts', $data);

        $response->assertStatus(201);
        // Laravel stores it as decimal with 2 places
        $this->assertDatabaseHas('discounts', ['title' => 'Decimal Discount']);
    }
}
