<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Percentage discounts (no currency conversion needed)
        Discount::create([
            'title' => 'Summer Sale 10%',
            'type' => 'percentage',
            'value' => 10,
        ]);

        Discount::create([
            'title' => 'Winter Sale 20%',
            'type' => 'percentage',
            'value' => 20,
        ]);

        // Fixed discounts in INR (Indian Rupees)
        Discount::create([
            'title' => 'Holiday Special ₹5,000 off',
            'type' => 'fixed',
            'value' => 5000.00,  // INR
        ]);

        Discount::create([
            'title' => 'Flash Sale ₹10,000 off',
            'type' => 'fixed',
            'value' => 10000.00,  // INR
        ]);
    }
}
