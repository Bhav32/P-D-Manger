<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Discount;
use Illuminate\Database\Seeder;

class ProductDiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all products and discounts
        $products = Product::all();
        $discounts = Discount::all();

        if ($products->isEmpty() || $discounts->isEmpty()) {
            $this->command->warn('Products or Discounts not found. Please run ProductSeeder and DiscountSeeder first.');
            return;
        }

        // Map discounts to products
        $productDiscountMappings = [
            // Product 1: Bluetooth Speaker
            1 => [
                'Student Discount' => 1,  // 10% off
                'New year sale' => 1,     // 35% off
            ],
            // Product 2: Forbes Select WD X2 Vacuum Cleaner
            2 => [
                'Black Friday Sale' => 1,
                'New Customer Discount' => 1,
            ],
            // Product 3: Gaming Mouse
            3 => [
                'Student Discount' => 1,
                'New year sale' => 1,
            ],
            // Product 4: Laptop Pro 15"
            4 => [
                'New year sale' => 1,
                'Black Friday Sale' => 1,
            ],
            // Product 5: Mechanical Keyboard
            5 => [
                'Student Discount' => 1,
                'New Customer Discount' => 1,
            ],
            // Product 6: USB-C Hub
            6 => [
                'New year sale' => 1,
                'Bulk Purchase Discount' => 1,
            ],
            // Product 7: Wireless Mouse
            7 => [
                'Student Discount' => 1,
                'New Customer Discount' => 1,
            ],
            // Product 8: Monitor Stand
            8 => [
                'Black Friday Sale' => 1,
                'Student Discount' => 1,
            ],
            // Product 9: Desk Lamp
            9 => [
                'New year sale' => 1,
                'Bulk Purchase Discount' => 1,
            ],
            // Product 10: Cable Organizer
            10 => [
                'Student Discount' => 1,
                'Black Friday Sale' => 1,
            ],
            // Product 11: Smartphone 5G
            11 => [
                'New year sale' => 1,
                'Black Friday Sale' => 1,
                'New Customer Discount' => 1,
            ],
            // Product 12: Tablet 10"
            12 => [
                'New year sale' => 1,
                'Student Discount' => 1,
            ],
            // Product 13: Phone Case
            13 => [
                'Student Discount' => 1,
                'Bulk Purchase Discount' => 1,
            ],
            // Product 14: Screen Protector
            14 => [
                'New Customer Discount' => 1,
                'Bulk Purchase Discount' => 1,
            ],
            // Product 15: Stylus Pen
            15 => [
                'Black Friday Sale' => 1,
                'Student Discount' => 1,
            ],
            // Product 16: Wireless Earbuds
            16 => [
                'New year sale' => 1,
                'New Customer Discount' => 1,
            ],
            // Product 17: Over-Ear Headphones
            17 => [
                'Black Friday Sale' => 1,
                'Student Discount' => 1,
            ],
            // Product 18: Microphone USB
            18 => [
                'New year sale' => 1,
                'Student Discount' => 1,
            ],
            // Product 19: Speaker Dock
            19 => [
                'New Customer Discount' => 1,
                'Student Discount' => 1,
            ],
            // Product 20: Sound Card
            20 => [
                'Black Friday Sale' => 1,
                'New year sale' => 1,
            ],
            // Product 21: SSD 1TB
            21 => [
                'Black Friday Sale' => 1,
                'Bulk Purchase Discount' => 1,
            ],
            // Product 22: External Hard Drive
            22 => [
                'New year sale' => 1,
                'Student Discount' => 1,
            ],
            // Product 23: USB Flash Drive
            23 => [
                'New Customer Discount' => 1,
                'Bulk Purchase Discount' => 1,
            ],
        ];

        // Get discount IDs by title for easier mapping
        $discountsByTitle = $discounts->keyBy('title');

        // Attach discounts to products
        foreach ($productDiscountMappings as $productId => $discountTitles) {
            $product = $products->find($productId);
            
            if ($product) {
                $discountIds = [];
                foreach ($discountTitles as $title => $count) {
                    if (isset($discountsByTitle[$title])) {
                        $discountIds[] = $discountsByTitle[$title]->id;
                    }
                }
                
                if (!empty($discountIds)) {
                    $product->discounts()->sync($discountIds);
                }
            }
        }

        $this->command->info('Product-Discount relationships created successfully!');
    }
}
