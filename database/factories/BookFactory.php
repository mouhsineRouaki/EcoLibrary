<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(rand(2, 5));
        $totalQuantity = fake()->numberBetween(1, 50);
        $availableQuantity = fake()->numberBetween(0, $totalQuantity);

        return [
            'category_id' => Category::factory(),
            'title' => Str::title(rtrim($title, '.')),
            'slug' => Str::slug($title . '-' . Str::random(5)),
            'description' => fake()->paragraph(),
            'author' => fake()->name(),
            'total_quantity' => $totalQuantity,
            'available_quantity' => $availableQuantity,
            'is_active' => fake()->boolean(85),
        ];
    }
}
