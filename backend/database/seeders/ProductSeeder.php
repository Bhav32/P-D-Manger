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
        $products = [
            // Electronics
            [
                'name' => 'Bluetooth Speaker',
                'description' => 'Waterproof Bluetooth speaker with 360-degree sound and long battery life.',
                'price' => 5809.17,
            ],
            [
                'name' => 'Forbes Select WD X2 Vacuum Cleaner',
                'description' => 'Forbes Euroclean WD X2 Wet & Dry Vacuum cleaner is a Patented technology for Deep Cleaning+, along with other advanced features.',
                'price' => 15790.00,
            ],
            [
                'name' => 'Gaming Mouse',
                'description' => 'Professional gaming mouse with RGB lighting and programmable buttons.',
                'price' => 6639.17,
            ],
            [
                'name' => 'Laptop Pro 15"',
                'description' => 'High-performance laptop with 15" display, perfect for professionals and developers.',
                'price' => 107899.17,
            ],
            [
                'name' => 'Mechanical Keyboard',
                'description' => 'Compact mechanical keyboard with tactile switches and backlighting.',
                'price' => 10789.17,
            ],
            // Accessories
            [
                'name' => 'USB-C Hub',
                'description' => 'Multi-port USB-C hub with HDMI, USB 3.0, and SD card reader.',
                'price' => 3499.00,
            ],
            [
                'name' => 'Wireless Mouse',
                'description' => 'Ergonomic wireless mouse with adjustable DPI settings.',
                'price' => 1299.00,
            ],
            [
                'name' => 'Monitor Stand',
                'description' => 'Adjustable monitor stand with storage drawer.',
                'price' => 2999.00,
            ],
            [
                'name' => 'Desk Lamp',
                'description' => 'LED desk lamp with adjustable brightness and color temperature.',
                'price' => 2199.00,
            ],
            [
                'name' => 'Cable Organizer',
                'description' => 'Cable management kit with clips and sleeves.',
                'price' => 599.00,
            ],
            // Phones & Tablets
            [
                'name' => 'Smartphone 5G',
                'description' => 'Latest 5G smartphone with advanced camera system.',
                'price' => 65000.00,
            ],
            [
                'name' => 'Tablet 10"',
                'description' => '10-inch tablet for productivity and entertainment.',
                'price' => 42000.00,
            ],
            [
                'name' => 'Phone Case',
                'description' => 'Protective phone case with shockproof technology.',
                'price' => 799.00,
            ],
            [
                'name' => 'Screen Protector',
                'description' => 'Tempered glass screen protector for smartphones.',
                'price' => 399.00,
            ],
            [
                'name' => 'Stylus Pen',
                'description' => 'Precision stylus for tablets and drawing.',
                'price' => 1499.00,
            ],
            // Audio
            [
                'name' => 'Wireless Earbuds',
                'description' => 'True wireless earbuds with active noise cancellation.',
                'price' => 8999.00,
            ],
            [
                'name' => 'Over-Ear Headphones',
                'description' => 'Premium over-ear headphones with studio quality sound.',
                'price' => 16500.00,
            ],
            [
                'name' => 'Microphone USB',
                'description' => 'Professional USB microphone for streaming and recording.',
                'price' => 4999.00,
            ],
            [
                'name' => 'Speaker Dock',
                'description' => 'Docking speaker for smartphones and tablets.',
                'price' => 3199.00,
            ],
            [
                'name' => 'Sound Card',
                'description' => 'External sound card for enhanced audio quality.',
                'price' => 5499.00,
            ],
            // Storage
            [
                'name' => 'SSD 1TB',
                'description' => '1TB Solid State Drive for faster data transfer.',
                'price' => 8999.00,
            ],
            [
                'name' => 'External Hard Drive',
                'description' => '2TB external hard drive for backup and storage.',
                'price' => 5999.00,
            ],
            [
                'name' => 'USB Flash Drive',
                'description' => '64GB USB 3.0 flash drive for portable storage.',
                'price' => 999.00,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
