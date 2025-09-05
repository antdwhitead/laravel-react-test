<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->sentence(3);

        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'content' => fake()->paragraphs(3, true),
            'category' => fake()->randomElement(['Technology', 'Business', 'Health', 'Travel', 'Food', 'Entertainment', 'Sports', 'Education']),
        ];
    }
}
