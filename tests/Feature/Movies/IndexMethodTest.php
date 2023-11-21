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
                'message',
                'data' => [
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
                        '*' => [
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
                ],
            ]);
    }

    /** @test */
    public function it_orders_results_in_ascending_order_when_requested()
    {
        $user = $this->createAuthenticatedUser();

        // Assuming 'rating' is a valid column for ordering
        $response = $this->json('GET', '/api/movies', ['order-asc' => 'rating']);

        $response->assertStatus(200);

        // Get the JSON response as an array
        $responseData = $response->json();

        $ratings = collect($responseData['data'])->pluck('rating')->toArray();
        $sortedRatings = $ratings;
        rsort($sortedRatings);

        // Check if the movies are ordered in ascending order based on the 'rating' column
        $this->assertEquals($ratings, $sortedRatings);
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
