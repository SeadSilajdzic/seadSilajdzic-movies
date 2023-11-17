<?php

namespace Tests\Feature\Movies\Cache;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CachedMoviesMethodsTest extends TestCase
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
    public function testCacheMethodWithoutExpiration(): void
    {
        $user = $this->createAuthenticatedUser();

        $movie = Movie::factory()->create();
        $user->movies()->attach($movie->slug);

        $response = $this->postJson('/api/movies/cache-favourite-list');

        $response->assertStatus(200);

        // Assert the response has the expected structure
        $response->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'movie_slug',
                    'user_id',
                ],
            ],
        ]);

        // Assert the response contains the expected data
        $response->assertJson([
            'message' => 'Data cached indefinitely',
            'data' => [
                [
                    'id' => 1,
                    'movie_slug' => $movie->slug,
                    'user_id' => $user->id,
                ],
            ],
        ]);
    }

    /** @test */
    public function testCacheMethodWithExpiration(): void
    {
        $user = $this->createAuthenticatedUser();

        Cache::forget('movie_user_data');

        $movie = Movie::factory()->create();
        $user->movies()->attach($movie->slug);

        $response = $this->postJson('/api/movies/cache-favourite-list/60');

        // Assert the response status is 200 OK
        $response->assertStatus(200);

        // Assert the response has the expected structure for the 'data' key
        $response->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'movie_slug',
                    'user_id',
                ],
            ],
        ]);

        // Assert the response contains the expected message
        $response->assertJson(['message' => 'Data cached for 60 seconds']);

        // Assert that the cache is set and contains data
        $this->assertNotNull(Cache::get('movie_user_data'));
    }

    /** @test */
    public function testCachedMethod(): void
    {
        // Creating a user for authentication
        $user = User::factory()->create();
        $this->actingAs($user);

        // Assuming some data is already cached for testing
        $cachedData = [
            [
                'movie_slug' => 'architecto-vero-et-ea',
                'user_id' => $user->id,
            ]
        ];

        // Putting cached data into the cache
        Cache::put('movie_user_data', $cachedData, 60);

        // Making a request to the 'cached' endpoint
        $response = $this->getJson('/api/movies/cache-favourite-list');

        // Asserting the response status is 200 OK
        $response->assertStatus(200);

        // Asserting the response has the expected structure
        $response->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'movie_slug',
                    'user_id',
                ],
            ],
        ]);

        // Asserting the response contains the expected data
        $response->assertJson([
            'message' => 'Cached data',
            'data' => $cachedData,
        ]);
    }
}
