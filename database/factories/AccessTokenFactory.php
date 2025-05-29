<?php

namespace Database\Factories;

use App\Models\AccessToken;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccessTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccessToken::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'token' => $this->faker->unique->text(255),
            'expires_at' => $this->faker->dateTime(),
            'employee_id' => \App\Models\Employee::factory(),
        ];
    }
}
