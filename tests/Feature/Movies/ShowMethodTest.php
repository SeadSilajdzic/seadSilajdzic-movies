<?php

namespace Tests\Feature\Movies;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShowMethodTest extends TestCase
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
    public function it_returns_a_movie_successfully()
    {
        $user = $this->createAuthenticatedUser();

        Movie::create([
            'title' => 'My movie',
            'slug' => Str::slug('My movie'),
            'description' => 'My movie description',
            'rating' => 5.0
        ]);

        $response = $this->json('GET', "/api/movies/my-movie");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Movie loaded successfully',
                'data' => [
                    "title" => "My movie",
                    "slug" => "my-movie",
                    "description" => "My movie description",
                    "rating" => "5.0",
                ]
            ]);
    }


    /** @test */
    public function it_returns_404_for_invalid_movie_id()
    {
        $user = $this->createAuthenticatedUser();

        $invalidMovieId = 999; // Assuming this ID does not exist in the database

        $response = $this->json('GET', "/api/movies/{$invalidMovieId}");

        $response->assertStatus(404);
    }
}
