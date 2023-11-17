<?php

namespace App\Http\Controllers;

use App\Http\Requests\Movie\MovieRequest;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // Whitelist columns we work with
        $columns = ['id', 'title', 'rating', 'slug'];

        $query = Movie::query();
        $query->select($columns);

        // Apply some filters for the ordering
        $query
            ->when(request()->has('order-asc'), function ($query) use ($columns) {
                if(in_array(request('order-asc'), $columns)) {
                    return $query->orderBy(request('order-asc'), 'asc');
                }
            })
            ->when(request()->has('order-desc'), function ($query) use ($columns) {
                if(in_array(request('order-desc'), $columns)) {
                    return $query->orderBy(request('order-desc'), 'desc');
                }
            });

        $results = $query->paginate(10);

        return response()->json($results);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MovieRequest $request): JsonResponse
    {
        $movie = Movie::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'rating' => $request->rating,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Movie stored successfully',
            'data' => $movie
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie): JsonResponse
    {
        return response()->json($movie);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MovieRequest $request, Movie $movie): JsonResponse
    {
        $validatedData = $request->validated();

        if(isset($validatedData['title'])) {
            $validatedData['slug'] = Str::slug($validatedData['title']);
        }

        $movie->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Movie updated successfully',
            'data' => $movie
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie): JsonResponse
    {
        $movie->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Movie removed successfully',
        ]);
    }

    /**
     * @param $string
     * @return JsonResponse
     */
    public function search($string): JsonResponse
    {
        $movies = Movie::where(function($query) use ($string) {
            $query->where('title', 'like', '%'. $string.'%')
                ->orWhere('id', 'like', '%'. $string .'%');
        })->get();

        if($movies->count() > 0) {
            return response()->json([
                'movies_found' => $movies->count(),
                'movies' => $movies
            ]);
        } else {
            return response()->json([
                'message' => 'We don\'t have any results that match your search!'
            ]);
        }
    }

    /**
     * @param Movie $movie
     * @return JsonResponse
     */
    public function favourite(Movie $movie): JsonResponse
    {
        if (Auth::check()) {
            $user = auth()->user();

            // Check if the movie is already a favorite for the user
            if (!$user->movies()->where('movie_slug', $movie->slug)->exists()) {
                // Attach the movie to the user's favorites
                $user->movies()->attach($movie->slug);

                return response()->json(['message' => 'Movie has been added to favorites!']);
            } else {
                return response()->json(['message' => 'Movie is already in favorites.']);
            }
        } else {
            return response()->json(['message' => 'You are not authenticated.']);
        }
    }
}
