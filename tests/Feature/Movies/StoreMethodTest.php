<?php

namespace Tests\Feature\Movies;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreMethodTest extends TestCase
{
    use RefreshDatabase;

    protected function createAuthenticatedUser()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        // Authenticate the user
        $this->actingAs($user);

        return $user;
    }

    /** @test */
    public function it_stores_a_movie_successfully()
    {
        $user = $this->createAuthenticatedUser();

        $data = [
            'title' => 'Test Movie',
            'description' => 'This is a test movie.',
            'rating' => 4.5,
        ];

        $response = $this->json('POST', '/api/movies', $data);

        $response->assertStatus(200)
        ->assertJson([
            'message' => 'Movie stored successfully',
            'data' => [
                'title' => $data['title'],
                'description' => $data['description'],
                'rating' => $data['rating'],
            ],
        ]);

        $this->assertDatabaseHas('movies', [
            'title' => $data['title'],
            'slug' => Str::slug($data['title']),
            'description' => $data['description'],
            'rating' => $data['rating'],
        ]);
    }

    /** @test */
    public function it_requires_title_for_storing_a_movie()
    {
        $user = $this->createAuthenticatedUser();

        $data = [
            'description' => 'This is a test movie.',
            'rating' => 4.5,
        ];

        $response = $this->json('POST', '/api/movies', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /** @test */
    public function it_requires_rating_to_be_numeric()
    {
        $user = $this->createAuthenticatedUser();

        $data = [
            'title' => 'Test Movie',
            'description' => 'This is a test movie.',
            'rating' => 'not a number',
        ];

        $response = $this->json('POST', '/api/movies', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);
    }
}
