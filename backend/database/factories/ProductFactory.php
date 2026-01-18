<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word() . ' ' . $this->faker->word(),
            'description' => $this->faker->sentence(10),
            'price' => $this->faker->numberBetween(100, 10000),
        ];
    }

    /**
     * State for high-priced products
     */
    public function expensive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'price' => $this->faker->numberBetween(5000, 50000),
            ];
        });
    }

    /**
     * State for budget products
     */
    public function budget(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'price' => $this->faker->numberBetween(10, 500),
            ];
        });
    }

    /**
     * State without description
     */
    public function withoutDescription(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'description' => null,
            ];
        });
    }
}
