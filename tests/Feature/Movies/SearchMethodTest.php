<?php

namespace Tests\Feature\Movies;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchMethodTest extends TestCase
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
    public function it_returns_movies_found()
    {
        $user = $this->createAuthenticatedUser();

        // Create movies in the database
        $movie1 = Movie::create([
            'title' => 'Movie A',
            'description' => 'Some movie "A" description',
            'rating' => 3.2,
            'slug' => Str::slug('Movie A')
        ]);

        $movie2 = Movie::create([
            'title' => 'Movie B',
            'description' => 'Some movie "B" description',
            'rating' => 3.2,
            'slug' => Str::slug('Movie B')
        ]);

        $movie3 = Movie::create([
            'title' => 'Another Movie',
            'description' => 'Some movie "Movie" description',
            'rating' => 3.2,
            'slug' => Str::slug('Another Movie')
        ]);

        // Make a request to search for movies with a common string
        $response = $this->json('GET', 'http://127.0.0.1:8000/api/movies/search/Movie');

        // Assert the response is successful
        $response->assertStatus(200);

        // Assert the response structure
        $response->assertJsonStructure([
            'movies_found',
            'movies' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'description',
                    'rating',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

        // Decode the JSON response for easier handling
        $responseData = $response->json();

        // Assert the correct number of movies found
        $this->assertEquals(3, $responseData['movies_found']);

        // Assert the correct movies are present in the response
        $this->assertCount(3, $responseData['movies']);

        // Additional assertions based on your specific movie attributes
        $this->assertArrayHasKey('id', $responseData['movies'][0]);
        $this->assertArrayHasKey('title', $responseData['movies'][0]);
        $this->assertArrayHasKey('slug', $responseData['movies'][0]);
        $this->assertArrayHasKey('description', $responseData['movies'][0]);
        $this->assertArrayHasKey('rating', $responseData['movies'][0]);
        $this->assertArrayHasKey('created_at', $responseData['movies'][0]);
        $this->assertArrayHasKey('updated_at', $responseData['movies'][0]);
    }

    /** @test */
    public function it_returns_no_results_for_nonexistent_movie()
    {
        $user = $this->createAuthenticatedUser();

        // Make a request to search for movies with a string that doesn't match any movies
        $response = $this->json('GET', 'http://127.0.0.1:8000/api/movies/search/Nonexistent');

        // Assert the response status code is 200
        $response->assertStatus(200);

        // Assert the response structure
        $response->assertJsonStructure([
            'message',
        ]);

        // Decode the JSON response for easier handling
        $responseData = $response->json();

        // Assert the correct message for no search results
        $this->assertEquals("We don't have any results that match your search!", $responseData['message']);

        // Additional assertion to ensure the response is an object
        $this->assertIsArray($responseData);
    }
}
