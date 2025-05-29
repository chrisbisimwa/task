<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(15),
            'status' => 'pending',
            'due_week' => $this->faker->text(255),
            'employee_id' => \App\Models\Employee::factory(),
        ];
    }
}
