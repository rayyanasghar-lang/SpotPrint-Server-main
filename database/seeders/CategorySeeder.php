<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Products',
                'description' => 'All Products',
                'status' => 'Active',
                'show_in_menu' => true,
                'parent_id' => 0,
                'metadata' => ['images' => ['category1.jpg', 'category2.jpg'], 'banners' => ['category_banner1.jpg', 'category_banner2.jpg']],
            ],
            [
                'name' => 'Shop By',
                'description' => 'Shop By Occasion or Industry',
                'status' => 'Active',
                'show_in_menu' => true,
                'parent_id' => 0,
                'metadata' => ['images' => ['category1.jpg', 'category2.jpg'], 'banners' => ['category_banner1.jpg', 'category_banner2.jpg']],
            ],

            [
                'name' => 'All Products',
                'description' => 'All available products',
                'status' => 'Active',
                'show_in_menu' => false,
                'parent_id' => 1,
                'metadata' => ['images' => ['category1.jpg', 'category2.jpg'], 'banners' => ['category_banner1.jpg', 'category_banner2.jpg']],
            ],
            [
                'name' => 'Most Popular',
                'description' => 'Most popular products based on sales',
                'status' => 'Active',
                'show_in_menu' => false,
                'parent_id' => 1,
                'metadata' => ['images' => ['category1.jpg', 'category2.jpg'], 'banners' => ['category_banner1.jpg', 'category_banner2.jpg']],
            ],
            [
                'name' => 'Shop By Occasion',
                'description' => 'Products categorized by occasion',
                'status' => 'Active',
                'show_in_menu' => false,
                'parent_id' => 2,
                'metadata' => ['images' => ['category1.jpg', 'category2.jpg'], 'banners' => ['category_banner1.jpg', 'category_banner2.jpg']],
            ],
            [
                'name' => 'Shop By Industry',
                'description' => 'Products categorized by industry',
                'status' => 'Active',
                'show_in_menu' => false,
                'parent_id' => 2,
                'metadata' => ['images' => ['category1.jpg', 'category2.jpg'], 'banners' => ['category_banner1.jpg', 'category_banner2.jpg']],
            ], 
        ];

        
        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
