<?php

namespace Tests\Feature\Movies;

use App\Models\Movie;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateMethodTest extends TestCase
{
    use RefreshDatabase, InteractsWithDatabase;

    /** @test */
//    public function it_updates_a_movie_successfully()
//    {
//        // Create a movie in the database
//        $movie = Movie::factory()->create([
//            'title' => 'Old Title',
//            'slug' => Str::slug('Old Title'),
//            'description' => 'Old description',
//            'rating' => 4.2,
//        ]);
//
//        // New data to update the movie
//        $newData = [
//            'title' => 'New Movie Title',
//            'description' => 'New movie description',
//            'rating' => '4.5',
//        ];
//
//        // Make a request to update the movie
//        $response = $this->json('PUT', "/api/movies/{$movie->slug}", $newData);
//
//        // Assert the response is successful
//        $response->assertStatus(JsonResponse::HTTP_OK);
//
//        // Assert the response structure
//        $response->assertJsonStructure([
//            'status',
//            'message',
//            'data' => [
//                'slug', // Updated to use 'slug' as the key
//                'title',
//                'description',
//                'rating',
//                'created_at',
//                'updated_at',
//            ],
//        ]);
//
//        // Assert the database has been updated
//        $this->assertDatabaseHas('movies', [
//            'slug' => Str::slug($newData['title']), // Updated to use 'slug' as the key and Str::slug()
//            'title' => $newData['title'],
//            'description' => $newData['description'],
//            'rating' => $newData['rating'],
//        ]);
//    }

    /** @test */
//    public function it_validates_required_fields()
//    {
//        // Make a request to update the movie without providing required fields
//        $response = $this->json('PUT', "/api/movies/some-nonexistent-slug", []);
//
//        // Assert the response contains validation errors
//        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
//            ->assertJsonValidationErrors(['title']);
//
//        // Assert the database has not been updated
//        $this->assertDatabaseMissing('movies', ['slug' => 'some-nonexistent-slug']); // Updated to use 'slug' as the key
//    }
}
