<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attachment>
 */
class AttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'attachable_id' => Article::factory(),
            'attachable_type' => Article::class,
            'file_type' => $this->faker->randomElement(['image', 'pdf']),
            'file_path'       => 'attachments/' . fake()->uuid() . '.jpg',
        ];
    }
}
