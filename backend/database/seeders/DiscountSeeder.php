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
        Discount::create([
            'title' => 'New year sale',
            'type' => 'percentage',
            'value' => 35,
            'is_active' => true,
        ]);

        Discount::create([
            'title' => 'Black Friday Sale',
            'type' => 'percentage',
            'value' => 25,
            'is_active' => true,
        ]);

        Discount::create([
            'title' => 'New Customer Discount',
            'type' => 'percentage',
            'value' => 15,
            'is_active' => true,
        ]);

        Discount::create([
            'title' => 'Bulk Purchase Discount',
            'type' => 'fixed',
            'value' => 150.00,
            'is_active' => true,
        ]);

        Discount::create([
            'title' => 'Student Discount',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
        ]);
    }
}
