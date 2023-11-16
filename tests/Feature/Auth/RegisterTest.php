<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_registers_a_user_successfully()
    {
        // Make a request to register a new user
        $response = $this->json('POST', '/api/register', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(200);

        // Assert the response structure
        $response->assertJsonStructure([
            'status',
            'message',
            'user' => [
                'id',
                'name',
                'email',
            ],
            'authorisation' => [
                'token',
                'type',
            ],
        ]);

        // Assert the token is present in the response
        $response->assertJson(['authorisation' => ['token' => true]]);

        // Assert the user is the correct user
        $response->assertJson(['user' => ['email' => 'john.doe@example.com']]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        // Make a request to register a new user without providing required fields
        $response = $this->json('POST', '/api/register', []);

        // Assert the response contains validation errors
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
