<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    use RefreshDatabase;

    /** @test */
    public function it_logs_out_a_user_successfully()
    {
        // Create a user using the factory
        $user = User::factory()->create();

        // Log in the user
        Auth::login($user);

        // Make a request to log out the user
        $response = $this->json('POST', '/api/logout');

        // Assert the response is successful
        $response->assertStatus(JsonResponse::HTTP_OK);

        // Assert the response structure
        $response->assertJsonStructure([
            'status',
            'message',
        ]);

        // Assert the correct success message
        $response->assertJson(['status' => 'success', 'message' => 'Successfully logged out']);

        // Assert the user is now logged out
        $this->assertFalse(Auth::check());
    }

    /** @test */
    public function it_handles_logout_when_user_is_not_authenticated()
    {
        // Make a request to log out a user when no user is authenticated
        $response = $this->json('POST', '/api/logout');

        // Assert the response status code is 401 (Unauthorized)
        $response->assertStatus(401);

        // Assert the response structure
        $response->assertJsonStructure([
            'message',
        ]);

        // Assert the correct error message for unauthorized logout attempt
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}
