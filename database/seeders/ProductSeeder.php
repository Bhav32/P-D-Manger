<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Laptop',
            'description' => 'High-performance laptop for business and gaming',
            'price' => 85000.00,  // INR
        ]);

        Product::create([
            'name' => 'Smartphone',
            'description' => 'Latest smartphone with advanced features',
            'price' => 65000.00,  // INR
        ]);

        Product::create([
            'name' => 'Headphones',
            'description' => 'Wireless noise-cancelling headphones',
            'price' => 16500.00,  // INR
        ]);

        Product::create([
            'name' => 'Tablet',
            'description' => '10-inch tablet for productivity and entertainment',
            'price' => 42000.00,  // INR
        ]);
    }
}
