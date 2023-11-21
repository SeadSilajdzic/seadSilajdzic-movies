<?php

namespace App\Http\Controllers\Movies;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieSearchController extends Controller
{
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
                'message'   => 'There are ' . $movies->count() . ' results that match your search!',
                'data'      => $movies
            ]);
        } else {
            return response()->json([
                'message'   => 'We don\'t have any results that match your search!',
                'data'      => []
            ]);
        }
    }
}
