<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $maxSortOrder = Category::max('sort_order');

        return [
            'name' => $this->faker->randomElement(['Dây da đồng hồ', 'Găng tay', 'Giày da nam', 'Thắt lưng', 'Túi xách', 'Ví da nam']),
            'parent_id' => 0,
            'desc' => $this->faker->paragraph(2, true),
            'status' => true,
            'sort_order' => floor($maxSortOrder) + 1,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Category $category) {
            if ($category->name === 'Túi xách') {
                Category::factory()->create([
                    'parent_id' => $category->id,
                    'name' => 'Túi xách 1',
                    'desc' => $this->faker->paragraph(2, true),
                    'status' => true,
                    'sort_order' => $category->sort_order + 0.1,
                ]);
                Category::factory()->create([
                    'parent_id' => $category->id,
                    'name' => 'Túi xách 2',
                    'desc' => $this->faker->paragraph(2, true),
                    'status' => true,
                    'sort_order' => $category->sort_order + 0.2,
                ]);

                $category1 = Category::where('parent_id', $category->id)->where('sort_order', $category->sort_order + 0.1)->first();
                if ($category1->name === 'Túi xách 1') {
                    Category::factory()->create([
                        'parent_id' => $category1->id,
                        'name' => 'Túi xách 1.1',
                        'desc' => $this->faker->paragraph(2, true),
                        'status' => true,
                        'sort_order' => $category1->sort_order + 0.01,
                    ]);
                    Category::factory()->create([
                        'parent_id' => $category1->id,
                        'name' => 'Túi xách 1.2',
                        'desc' => $this->faker->paragraph(2, true),
                        'status' => true,
                        'sort_order' => $category1->sort_order + 0.02,
                    ]);
                }
                $category2 = Category::where('parent_id', $category->id)->where('sort_order', $category->sort_order + 0.2)->first();
                if ($category2->name === 'Túi xách 2') {
                    Category::factory()->create([
                        'parent_id' => $category2->id,
                        'name' => 'Túi xách 2.1',
                        'desc' => $this->faker->paragraph(2, true),
                        'status' => true,
                        'sort_order' => $category2->sort_order + 0.01,
                    ]);
                }
            }
        });
    }
}
