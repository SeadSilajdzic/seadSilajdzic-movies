<?php

namespace Tests\Feature\Movies;

use App\Models\Movie;
use App\Models\MovieUser;
use App\Models\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateMethodTest extends TestCase
{
    use RefreshDatabase, InteractsWithDatabase;

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
    public function it_updates_a_movie_successfully()
    {
        $user = $this->createAuthenticatedUser();

        // Create a movie in the database
        $movie = Movie::create([
            'title' => 'Old Title',
            'slug' => Str::slug('Old Title'),
            'description' => 'Old description',
            'rating' => 4.2,
        ]);

        // Assert the initial state of the movie
        $this->assertDatabaseHas('movies', [
            'title' => 'Old Title',
        ]);

        // New data to update the movie
        $newData = [
            'title' => 'New Movie Title',
            'description' => 'New movie description',
            'rating' => '4.5',
        ];

        // Make a request to update the movie
        $response = $this->json('PUT', "/api/movies/{$movie->slug}", $newData);

        // Assert the response is successful
        $response->assertStatus(JsonResponse::HTTP_OK);

        // Assert the response structure
        $response->assertJsonStructure([
            'message',
            'data' => [
                'slug',
                'title',
                'description',
                'rating',
                'created_at',
                'updated_at',
            ],
        ]);

        // Assert the database has been updated
        $this->assertDatabaseHas('movies', [
            'title' => $newData['title'],
            'description' => $newData['description'],
            'rating' => $newData['rating'],
        ]);
    }
}
