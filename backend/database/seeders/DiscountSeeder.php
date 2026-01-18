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
        // New Year Sale - 35% off
        Discount::create([
            'title' => 'New year sale',
            'type' => 'percentage',
            'value' => 35,
            'is_active' => true,
        ]);

        // Black Friday - 25% off
        Discount::create([
            'title' => 'Black Friday Sale',
            'type' => 'percentage',
            'value' => 25,
            'is_active' => true,
        ]);

        // New Customer Discount - 15% off
        Discount::create([
            'title' => 'New Customer Discount',
            'type' => 'percentage',
            'value' => 15,
            'is_active' => true,
        ]);

        // Bulk Purchase Discount - ₹150 fixed
        Discount::create([
            'title' => 'Bulk Purchase Discount',
            'type' => 'fixed',
            'value' => 150.00,
            'is_active' => true,
        ]);

        // Student Discount - 10% off
        Discount::create([
            'title' => 'Student Discount',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
        ]);
    }
}
