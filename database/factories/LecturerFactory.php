<?php

namespace Database\Factories;

use App\Models\Lecturer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lecturer>
 */
class LecturerFactory extends Factory
{
    /**
     * @var list<string>
     */
    public const DEPARTMENTS = [
        'Department of Computing',
        'Department of Information Systems',
        'Department of Business Analytics',
        'Department of Entrepreneurship',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'staff_number' => fake()->unique()->numerify('SP-24#####'),
            'first_name' => fake()->firstName('male'),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'department' => fake()->randomElement(self::DEPARTMENTS),
        ];
    }

    /**
     * A lecturer who can sign in. The roles must already be seeded.
     */
    public function withAccount(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => UserFactory::new()->lecturer(),
        ]);
    }
}
