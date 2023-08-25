<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Product;
use Illuminate\Support\Carbon;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $totalRecords = 40000;
        $chunkSize = 500;

        $faker = Faker::create();

        for ($i = 0; $i < $totalRecords/$chunkSize; $i++) {
            $data = [];
            for ($j = 0; $j < $chunkSize; $j++) {
                $data[] = [
                    'manufacturer_id' => $faker->numberBetween(1, 10),
                    'name' => $faker->sentence(3, true),
                    'shortDesc' => $faker->paragraph(1),
                    'detailDesc' => $faker->paragraph(3, true),
                    'price' => $faker->numberBetween(100, 800),
                    'quantity' => $faker->numberBetween(10, 80),
                    'link_video' => '',
                    'status' => true,
                    'updated_at'=> Carbon::now(),
                ];
            }
            Product::insert($data);
        }
    }
}
