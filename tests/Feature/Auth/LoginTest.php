<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_logs_in_a_user_successfully()
    {
        // Create a user in the database
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Make a request to log in the user
        $response = $this->json('POST', '/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Assert the response is successful
        $response->assertStatus(JsonResponse::HTTP_OK);

        // Assert the response structure
        $response->assertJsonStructure([
            'status',
            'user' => [
                'id',
                'name',
                'email',
                // Add more user attributes as needed
            ],
            'authorisation' => [
                'token',
                'type',
            ],
        ]);

        // Assert the token is present in the response
        $response->assertJson(['authorisation' => ['token' => true]]);

        // Assert the user is the correct user
        $response->assertJson(['user' => ['id' => $user->id]]);
    }

    /** @test */
    public function it_returns_unauthorized_for_invalid_credentials()
    {
        // Make a request to log in with invalid credentials
        $response = $this->json('POST', '/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'invalidpassword',
        ]);

        // Assert the response is unauthorized
        $response->assertStatus(JsonResponse::HTTP_UNAUTHORIZED);

        // Assert the response structure
        $response->assertJsonStructure([
            'status',
            'message',
        ]);

        // Assert the correct error message
        $response->assertJson(['message' => 'Unauthorized']);
    }
}
