<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'manufacturer_id' => $this->faker->numberBetween(1, 10),
            'name' => $this->faker->sentence(3, true),
            'shortDesc' => $this->faker->paragraph(1),
            'detailDesc' => $this->faker->paragraph(3, true),
            'price' => $this->faker->numberBetween(100, 800),
            'quantity' => $this->faker->numberBetween(10, 80),
            'link_video' => '',
            'status' => true,
        ];
    }
}
