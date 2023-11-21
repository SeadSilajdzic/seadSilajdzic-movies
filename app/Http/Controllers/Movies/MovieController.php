<?php

namespace App\Http\Controllers\Movies;

use App\Http\Controllers\Controller;
use App\Http\Requests\Movie\MovieRequest;
use App\Models\Movie;
use App\Models\MovieUser;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
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

        return response()->json([
            'message'   => 'Movies loaded successfully',
            'data'      => $results
        ]);
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
            'message'   => 'Movie stored successfully',
            'data'      => $movie
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie): JsonResponse
    {
        return response()->json([
            'message'   => 'Movie loaded successfully',
            'data'      => $movie
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MovieRequest $request, Movie $movie): JsonResponse
    {
        $validatedData = $request->validated();
        $movie->load('users');

        if(isset($validatedData['title'])) {
            $validatedData['slug'] = Str::slug($validatedData['title']);

            // If movie is in favourites, update it there too for all users that have it
            if($movie->users()->count() > 0) {
                $userIds = $movie->users->pluck('id');
                MovieUser::whereIn('user_id', $userIds)->delete();
            }
        }

        $movie->update($validatedData);

        if(!empty($userIds)) {
            foreach($userIds as $userId) {
                MovieUser::create([
                    'user_id' => $userId,
                    'movie_slug' => $movie->slug
                ]);
            }
        }

        return response()->json([
            'message'   => 'Movie updated successfully',
            'data'      => $movie
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie): JsonResponse
    {
        $removedModelData = $movie;

        MovieUser::where('movie_slug', $movie->slug)->delete();
        $movie->delete();

        return response()->json([
            'message'   => 'Movie removed successfully',
            'data'      => $removedModelData
        ]);
    }
}
