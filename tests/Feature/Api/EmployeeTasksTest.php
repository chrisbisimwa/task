<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Task;
use App\Models\Employee;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeTasksTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['email' => 'admin@admin.com']);

        Sanctum::actingAs($user, [], 'web');

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_gets_employee_tasks(): void
    {
        $employee = Employee::factory()->create();
        $tasks = Task::factory()
            ->count(2)
            ->create([
                'employee_id' => $employee->id,
            ]);

        $response = $this->getJson(
            route('api.employees.tasks.index', $employee)
        );

        $response->assertOk()->assertSee($tasks[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_employee_tasks(): void
    {
        $employee = Employee::factory()->create();
        $data = Task::factory()
            ->make([
                'employee_id' => $employee->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.employees.tasks.store', $employee),
            $data
        );

        $this->assertDatabaseHas('tasks', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $task = Task::latest('id')->first();

        $this->assertEquals($employee->id, $task->employee_id);
    }
}
