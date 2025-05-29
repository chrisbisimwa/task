<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\AccessToken;

use App\Models\Employee;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccessTokenControllerTest extends TestCase
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
    public function it_displays_index_view_with_access_tokens(): void
    {
        $accessTokens = AccessToken::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('access-tokens.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.access_tokens.index')
            ->assertViewHas('accessTokens');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_access_token(): void
    {
        $response = $this->get(route('access-tokens.create'));

        $response->assertOk()->assertViewIs('app.access_tokens.create');
    }

    /**
     * @test
     */
    public function it_stores_the_access_token(): void
    {
        $data = AccessToken::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('access-tokens.store'), $data);

        $this->assertDatabaseHas('access_tokens', $data);

        $accessToken = AccessToken::latest('id')->first();

        $response->assertRedirect(route('access-tokens.edit', $accessToken));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_access_token(): void
    {
        $accessToken = AccessToken::factory()->create();

        $response = $this->get(route('access-tokens.show', $accessToken));

        $response
            ->assertOk()
            ->assertViewIs('app.access_tokens.show')
            ->assertViewHas('accessToken');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_access_token(): void
    {
        $accessToken = AccessToken::factory()->create();

        $response = $this->get(route('access-tokens.edit', $accessToken));

        $response
            ->assertOk()
            ->assertViewIs('app.access_tokens.edit')
            ->assertViewHas('accessToken');
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

        $response = $this->put(
            route('access-tokens.update', $accessToken),
            $data
        );

        $data['id'] = $accessToken->id;

        $this->assertDatabaseHas('access_tokens', $data);

        $response->assertRedirect(route('access-tokens.edit', $accessToken));
    }

    /**
     * @test
     */
    public function it_deletes_the_access_token(): void
    {
        $accessToken = AccessToken::factory()->create();

        $response = $this->delete(route('access-tokens.destroy', $accessToken));

        $response->assertRedirect(route('access-tokens.index'));

        $this->assertModelMissing($accessToken);
    }
}
