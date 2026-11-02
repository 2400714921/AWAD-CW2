<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * @var list<string>
     */
    public const PROGRAMMES = [
        'Bachelor of Business Computing',
        'Bachelor of Science in Software Engineering',
        'Bachelor of Business Administration',
        'Bachelor of Science in Information Technology',
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
            'student_number' => '2400'.fake()->unique()->numerify('######'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'programme' => fake()->randomElement(self::PROGRAMMES),
            'year_of_study' => fake()->numberBetween(1, 4),
            'date_of_birth' => fake()->dateTimeBetween('-25 years', '-18 years'),
        ];
    }

    /**
     * A student who can sign in. The roles must already be seeded, so use this after
     * RolePermissionSeeder.
     */
    public function withAccount(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => UserFactory::new()->student(),
        ]);
    }
}
