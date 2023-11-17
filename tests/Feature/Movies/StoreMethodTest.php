<?php

namespace Tests\Feature\Movies;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreMethodTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_stores_a_movie_successfully()
    {
        $data = [
            'title' => 'Test Movie',
            'description' => 'This is a test movie.',
            'rating' => 4.5,
        ];

        $response = $this->json('POST', '/api/movies', $data);

        $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
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
