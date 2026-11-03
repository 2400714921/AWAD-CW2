<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Lecturer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * @var list<string>
     */
    public const TITLES = [
        'Advanced Web Application Development',
        'Database Systems',
        'Object Oriented Programming',
        'Software Engineering',
        'Business Analytics',
        'Computer Networks',
        'Web and Mobile Computing',
        'Operations Research',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_code' => 'BUC'.fake()->unique()->numerify('####'),
            'title' => fake()->randomElement(self::TITLES),
            'credits' => fake()->numberBetween(2, 6),
            'semester' => fake()->randomElement(['Semester 1', 'Semester 2']),
            'description' => fake()->sentence(12),
            'lecturer_id' => Lecturer::factory(),
        ];
    }

    /**
     * Every course must sit with a lecturer, so this only swaps in a given member of staff.
     */
    public function taughtBy(Lecturer $lecturer): static
    {
        return $this->state(fn (array $attributes) => [
            'lecturer_id' => $lecturer->id,
        ]);
    }
}
