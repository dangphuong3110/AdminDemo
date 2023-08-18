<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Manufacturer;
use App\Models\Product;
use Database\Factories\CategoryProductFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Schema::disableForeignKeyConstraints();
        Category::truncate();
//        Category::factory(10)->create();
        Manufacturer::truncate();
        Manufacturer::factory(10)->create();
        Product::truncate();
//        Product::factory(15)->create();
        CategoryProduct::truncate();
        CategoryProduct::factory(8)->create();
    }
}
