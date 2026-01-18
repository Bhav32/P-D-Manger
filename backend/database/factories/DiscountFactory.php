<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discount>
 */
class DiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['percentage', 'fixed']);
        
        return [
            'title' => $this->faker->unique()->catchPhrase(),
            'type' => $type,
            'value' => $type === 'percentage' 
                ? $this->faker->numberBetween(1, 100)
                : $this->faker->numberBetween(50, 5000),
            'is_active' => $this->faker->boolean(80), // 80% chance of true
        ];
    }

    /**
     * State for percentage discount
     */
    public function percentage(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'percentage',
                'value' => $this->faker->numberBetween(1, 100),
            ];
        });
    }

    /**
     * State for fixed amount discount
     */
    public function fixed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'fixed',
                'value' => $this->faker->numberBetween(50, 10000),
            ];
        });
    }

    /**
     * State for active discount
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }

    /**
     * State for inactive discount
     */
    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    /**
     * State for high percentage discount
     */
    public function highPercentage(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'percentage',
                'value' => $this->faker->numberBetween(50, 100),
            ];
        });
    }

    /**
     * State for low percentage discount
     */
    public function lowPercentage(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'percentage',
                'value' => $this->faker->numberBetween(1, 25),
            ];
        });
    }
}
