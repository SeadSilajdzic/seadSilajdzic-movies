<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RefreshTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_refreshes_token_successfully()
    {
        // Create a user using the factory
        $user = User::factory()->create();

        // Log in the user
        Auth::login($user);

        // Make a request to refresh the token
        $response = $this->json('POST', '/api/refresh');

        // Assert the response is successful
        $response->assertStatus(200);

        // Assert the response structure
        $response->assertJsonStructure([
            'status',
            'user',
            'authorisation' => [
                'token',
                'type',
            ],
        ]);

        // Assert the token is present in the response
        $response->assertJson(['authorisation' => ['token' => true]]);

        // Assert the user is the correct user
        $response->assertJson(['user' => ['email' => $user->email]]);
    }

    /** @test */
    public function it_handles_refresh_when_user_is_not_authenticated()
    {
        // Make a request to refresh the token when no user is authenticated
        $response = $this->json('POST', '/api/refresh');

        // Assert the response status code is 401 (Unauthorized)
        $response->assertStatus(401);

        // Assert the response structure
        $response->assertJsonStructure([
            'message',
        ]);

        // Assert the correct error message for unauthorized refresh attempt
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}
