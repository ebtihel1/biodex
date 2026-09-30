<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WasteCategory>
 */
class WasteCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            "name"=>fake()->word(),
            "description"=>fake()->sentence(4),
            "recycling_instructions"=>fake()->paragraph(4)
        ];
    }
}
