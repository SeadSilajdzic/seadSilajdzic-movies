<?php

namespace Tests\Feature\Movies;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class FavouriteMethodTest extends TestCase
{
    use RefreshDatabase;


    public function testUserCanAddNewFavorite()
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        Auth::login($user);

        $response = $this->json('POST', route('movies.favourite', $movie->slug));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Movie has been added to favorites!']);

        $this->assertTrue($user->movies()->where('movie_slug', $movie->slug)->exists());
    }

    public function testUserCannotAddExistingFavorite()
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        // Attach the movie to the user's favorites
        $user->movies()->attach($movie->slug);

        Auth::login($user);

        $response = $this->json('POST', route('movies.favourite', $movie->slug));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Movie is already in favorites.']);

        $this->assertTrue($user->movies()->where('movie_slug', $movie->slug)->exists());
    }
}
