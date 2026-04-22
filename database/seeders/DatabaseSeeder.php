<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\QrCode;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create Categories
        $categories = [
            [
                'name' => 'Seafood',
                'slug' => 'seafood',
                'description' => 'Hidangan laut segar dan lezat'
            ],
            [
                'name' => 'Masakan Sunda',
                'slug' => 'masakan-sunda',
                'description' => 'Hidangan tradisional khas Sunda'
            ],
            [
                'name' => 'Minuman',
                'slug' => 'minuman',
                'description' => 'Berbagai minuman segar dan hangat'
            ],
            [
                'name' => 'Aneka Gorengan',
                'slug' => 'gorengan',
                'description' => 'Gorengan renyah dan gurih'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Products
        $products = [
            // Seafood
            [
                'category_id' => 1,
                'name' => 'Gurame Bakar',
                'slug' => 'gurame-bakar',
                'description' => 'Gurame bakar bumbu spesial',
                'price' => 85000,
                'is_available' => true
            ],
            [
                'category_id' => 1,
                'name' => 'Cumi Goreng Tepung',
                'slug' => 'cumi-goreng-tepung',
                'description' => 'Cumi goreng tepung renyah',
                'price' => 65000,
                'is_available' => true
            ],
            [
                'category_id' => 1,
                'name' => 'Udang Asam Manis',
                'slug' => 'udang-asam-manis',
                'description' => 'Udang saus asam manis',
                'price' => 75000,
                'is_available' => true
            ],
            
            // Masakan Sunda
            [
                'category_id' => 2,
                'name' => 'Nasi Tutug Oncom',
                'slug' => 'nasi-tutug-oncom',
                'description' => 'Nasi campur oncom dengan lalapan',
                'price' => 25000,
                'is_available' => true
            ],
            [
                'category_id' => 2,
                'name' => 'Ikan Bakar Cianjur',
                'slug' => 'ikan-bakar-cianjur',
                'description' => 'Ikan bakar dengan sambal terasi',
                'price' => 45000,
                'is_available' => true
            ],
            [
                'category_id' => 2,
                'name' => 'Sayur Asem',
                'slug' => 'sayur-asem',
                'description' => 'Sayur asem segar khas Sunda',
                'price' => 20000,
                'is_available' => true
            ],
            
            // Minuman
            [
                'category_id' => 3,
                'name' => 'Es Cendol',
                'slug' => 'es-cendol',
                'description' => 'Es cendol dawet segar',
                'price' => 15000,
                'is_available' => true
            ],
            [
                'category_id' => 3,
                'name' => 'Jus Alpukat',
                'slug' => 'jus-alpukat',
                'description' => 'Jus alpukat dengan susu coklat',
                'price' => 18000,
                'is_available' => true
            ],
            [
                'category_id' => 3,
                'name' => 'Teh Tarik',
                'slug' => 'teh-tarik',
                'description' => 'Teh tarik hangat',
                'price' => 12000,
                'is_available' => true
            ],
            
            // Gorengan
            [
                'category_id' => 4,
                'name' => 'Tahu Isi',
                'slug' => 'tahu-isi',
                'description' => 'Tahu isi sayur',
                'price' => 5000,
                'is_available' => true
            ],
            [
                'category_id' => 4,
                'name' => 'Tempe Mendoan',
                'slug' => 'tempe-mendoan',
                'description' => 'Tempe mendoan dengan sambal kecap',
                'price' => 5000,
                'is_available' => true
            ],
            [
                'category_id' => 4,
                'name' => 'Pisang Goreng',
                'slug' => 'pisang-goreng',
                'description' => 'Pisang goreng madu',
                'price' => 8000,
                'is_available' => true
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Create initial QR Code
        QrCode::create([
            'code' => 'QR-' . uniqid(),
            'status' => 'active'
        ]);
    }
}