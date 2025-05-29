<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\AccessToken;

use App\Models\Employee;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccessTokenTest extends TestCase
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
    public function it_gets_access_tokens_list(): void
    {
        $accessTokens = AccessToken::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.access-tokens.index'));

        $response->assertOk()->assertSee($accessTokens[0]->token);
    }

    /**
     * @test
     */
    public function it_stores_the_access_token(): void
    {
        $data = AccessToken::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.access-tokens.store'), $data);

        $this->assertDatabaseHas('access_tokens', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_access_token(): void
    {
        $accessToken = AccessToken::factory()->create();

        $employee = Employee::factory()->create();

        $data = [
            'token' => $this->faker->unique->text(255),
            'expires_at' => $this->faker->dateTime(),
            'employee_id' => $employee->id,
        ];

        $response = $this->putJson(
            route('api.access-tokens.update', $accessToken),
            $data
        );

        $data['id'] = $accessToken->id;

        $this->assertDatabaseHas('access_tokens', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_access_token(): void
    {
        $accessToken = AccessToken::factory()->create();

        $response = $this->deleteJson(
            route('api.access-tokens.destroy', $accessToken)
        );

        $this->assertModelMissing($accessToken);

        $response->assertNoContent();
    }
}
