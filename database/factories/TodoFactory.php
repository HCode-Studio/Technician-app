<?php

namespace Database\Factories;

use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'=>$this->faker->word(),
            'description'=>$this->faker->text(50),
            'completed'=>$this->faker->randomElement(['todo', 'doing', 'done']),
            'user_id'=> User::all()->random()->id,
            'site_id'=> Site::all()->random()->id,
            'badges'=>$this->faker->words(3),
            'tags'=>$this->faker->words(3),
            'created_at'=>$this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
