<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Employee;
use App\Models\AccessToken;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeAccessTokensTest extends TestCase
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
    public function it_gets_employee_access_tokens(): void
    {
        $employee = Employee::factory()->create();
        $accessTokens = AccessToken::factory()
            ->count(2)
            ->create([
                'employee_id' => $employee->id,
            ]);

        $response = $this->getJson(
            route('api.employees.access-tokens.index', $employee)
        );

        $response->assertOk()->assertSee($accessTokens[0]->token);
    }

    /**
     * @test
     */
    public function it_stores_the_employee_access_tokens(): void
    {
        $employee = Employee::factory()->create();
        $data = AccessToken::factory()
            ->make([
                'employee_id' => $employee->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.employees.access-tokens.store', $employee),
            $data
        );

        $this->assertDatabaseHas('access_tokens', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $accessToken = AccessToken::latest('id')->first();

        $this->assertEquals($employee->id, $accessToken->employee_id);
    }
}
