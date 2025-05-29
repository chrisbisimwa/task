<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Task;

use App\Models\Employee;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(
            User::factory()->create(['email' => 'admin@admin.com'])
        );

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_displays_index_view_with_tasks(): void
    {
        $tasks = Task::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('tasks.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.tasks.index')
            ->assertViewHas('tasks');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_task(): void
    {
        $response = $this->get(route('tasks.create'));

        $response->assertOk()->assertViewIs('app.tasks.create');
    }

    /**
     * @test
     */
    public function it_stores_the_task(): void
    {
        $data = Task::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('tasks.store'), $data);

        $this->assertDatabaseHas('tasks', $data);

        $task = Task::latest('id')->first();

        $response->assertRedirect(route('tasks.edit', $task));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->get(route('tasks.show', $task));

        $response
            ->assertOk()
            ->assertViewIs('app.tasks.show')
            ->assertViewHas('task');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->get(route('tasks.edit', $task));

        $response
            ->assertOk()
            ->assertViewIs('app.tasks.edit')
            ->assertViewHas('task');
    }

    /**
     * @test
     */
    public function it_updates_the_task(): void
    {
        $task = Task::factory()->create();

        $employee = Employee::factory()->create();

        $data = [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(15),
            'status' => 'pending',
            'due_week' => $this->faker->text(255),
            'employee_id' => $employee->id,
        ];

        $response = $this->put(route('tasks.update', $task), $data);

        $data['id'] = $task->id;

        $this->assertDatabaseHas('tasks', $data);

        $response->assertRedirect(route('tasks.edit', $task));
    }

    /**
     * @test
     */
    public function it_deletes_the_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));

        $this->assertModelMissing($task);
    }
}
