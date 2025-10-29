<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Pin;
use App\Models\Result;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pin>
 */
class PinFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pin' => fake()->numerify('########'),
            'serial_number' => fake()->numerify('SN########'),
            'count' => 0,
            'result_id' => null,
            'use_status' => '',
        ];
    }

    /**
     * Indicate that the PIN has been used.
     */
    public function used(?Result $result = null): static
    {
        return $this->state(fn (array $attributes) => [
            'use_status' => 'used',
            'result_id' => $result?->id ?? Result::factory(),
            'count' => fake()->numberBetween(1, 5),
        ]);
    }

    /**
     * Indicate that the PIN has expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'use_status' => 'used',
            'result_id' => Result::factory(),
            'count' => 5,
        ]);
    }

    /**
     * Indicate that the PIN is unused.
     */
    public function unused(): static
    {
        return $this->state(fn (array $attributes) => [
            'use_status' => '',
            'result_id' => null,
            'count' => 0,
        ]);
    }
}
