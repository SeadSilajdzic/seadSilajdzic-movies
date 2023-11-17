<?php

namespace Tests\Feature\Movies;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeleteMethodTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_deletes_a_movie_successfully()
    {
        // Create a movie in the database
        $movie = Movie::create([
            'title' => 'My movie',
            'slug' => Str::slug('My movie'),
            'description' => 'My movie description',
            'rating' => '5.0'
        ]);

        // Make a request to delete the movie
        $this->json('DELETE', "/api/movies/".Str::slug('My movie'));

        $movie = Movie::where('slug', Str::slug('My movie'))->first();

        $this->assertNull($movie);
    }
}
