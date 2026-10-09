<?php

namespace Database\Factories;

use App\Models\CollectionPoint;
use Illuminate\Database\Eloquent\Factories\Factory;

class CollectionPointFactory extends Factory
{
    protected $model = CollectionPoint::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company().' Collect Point',
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'contact_phone' => $this->faker->phoneNumber(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'opening_hours' => ['Lundi: 9h-18h', 'Samedi: 10h-14h'],
            'accepted_categories' => ['Plastic', 'Glass'],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => 'active']);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => 'inactive']);
    }
}
