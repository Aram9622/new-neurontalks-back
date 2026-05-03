<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Technology>
 */
class TechnologyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $technologies = ['Laravel', 'React', 'Vue.js', 'Tailwind CSS', 'Next.js', 'PostgreSQL', 'Docker', 'Redis', 'TypeScript', 'Flutter'];
        return [
            'name' => $this->faker->unique()->randomElement($technologies),
            'icon' => null,
        ];
    }
}
