<?php

namespace Tests\Feature\Movies;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IndexMethodTest extends TestCase
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
    public function it_returns_a_valid_json_response()
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->json('GET', '/api/movies');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'rating',
                        'slug',
                    ],
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links' => [
                    [
                        'url',
                        'label',
                        'active',
                    ],
                ],
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ]);
    }

    /** @test */
    public function it_orders_results_in_ascending_order_when_requested()
    {
        $user = $this->createAuthenticatedUser();

        // Create movies with specific titles for testing
        $movie1 = Movie::factory()->create(['title' => 'Test Movie 1']);
        $movie2 = Movie::factory()->create(['title' => 'Test Movie 2']);

        // Make the request with the 'order-asc' parameter
        $response = $this->json('GET', '/api/movies', ['order-asc' => 'title']);

        $response->assertStatus(200);

        $responseData = $response->json();

        // Check if the movies are ordered in ascending order based on the 'title' column
        $this->assertEquals(
            collect([$movie1->title, $movie2->title])->sort()->values()->toArray(),
            collect($responseData['data'])->pluck('title')->toArray()
        );
    }


    /** @test */
    public function it_orders_results_in_descending_order_when_requested()
    {
        $user = $this->createAuthenticatedUser();

        // Assuming 'rating' is a valid column for ordering
        $response = $this->json('GET', '/api/movies', ['order-desc' => 'rating']);

        $response->assertStatus(200);

        // Get the JSON response as an array
        $responseData = $response->json();

        $ratings = collect($responseData['data'])->pluck('rating')->toArray();
        $sortedRatings = $ratings;
        rsort($sortedRatings);

        // Check if the movies are ordered in descending order based on the 'rating' column
        $this->assertEquals($ratings, $sortedRatings);
    }
}
