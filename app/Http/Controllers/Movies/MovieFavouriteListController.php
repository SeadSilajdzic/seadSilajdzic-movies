<?php

namespace App\Http\Controllers\Movies;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovieFavouriteListController extends Controller
{
    /**
     * @param Movie $movie
     * @return JsonResponse
     */
    public function favourite(Movie $movie): JsonResponse
    {
        if (Auth::check()) {
            $user = auth()->user();

            // Check if the movie is already a favorite for the user
            $movieExists = $user->movies()->where('movie_slug', $movie->slug)->exists();

            if (!$movieExists) {
                // Attach the movie to the user's favorites
                $user->movies()->attach($movie->slug);

                return response()->json([
                    'message'   => 'Movie has been added to favorites!',
                    'data'      => Movie::where('slug', $movie->slug)->first()
                ]);
            } else {
                return response()->json([
                    'message'   => 'Movie is already in favorites.',
                    'data'      => $movie
                ]);
            }
        } else {
            return response()->json([
                'message'   => 'You are not authenticated.',
                'data'      => []
            ]);
        }
    }
}
