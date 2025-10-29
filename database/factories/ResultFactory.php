<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Result;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Result>
 */
class ResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $score = fake()->randomFloat(2, 0, 100);
        $grade = $this->calculateGrade($score);
        $remark = $score >= 50 ? 'PASS' : 'FAIL';

        return [
            'exam_number' => fake()->unique()->numerify('EX#########'),
            'student_name' => fake()->name(),
            'course' => fake()->randomElement([
                'Computer Science',
                'Mathematics',
                'Physics',
                'Chemistry',
                'Biology',
                'English',
                'Economics',
            ]),
            'score' => $score,
            'grade' => $grade,
            'remark' => $remark,
        ];
    }

    /**
     * Indicate that the result is a passing grade.
     */
    public function passed(): static
    {
        return $this->state(function (array $attributes) {
            $score = fake()->randomFloat(2, 50, 100);

            return [
                'score' => $score,
                'grade' => $this->calculateGrade($score),
                'remark' => 'PASS',
            ];
        });
    }

    /**
     * Indicate that the result is a failing grade.
     */
    public function failed(): static
    {
        return $this->state(function (array $attributes) {
            $score = fake()->randomFloat(2, 0, 49);

            return [
                'score' => $score,
                'grade' => $this->calculateGrade($score),
                'remark' => 'FAIL',
            ];
        });
    }

    /**
     * Calculate grade based on score.
     */
    private function calculateGrade(float $score): string
    {
        return match (true) {
            $score >= 80 => 'A',
            $score >= 70 => 'B',
            $score >= 60 => 'C',
            $score >= 50 => 'D',
            $score >= 40 => 'E',
            default => 'F',
        };
    }
}
