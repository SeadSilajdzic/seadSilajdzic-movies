<?php

namespace App\Http\Controllers\Movies;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MovieCacheController extends Controller
{
    /**
     * @param $timeInSeconds
     * @return JsonResponse
     */
    public function cache($timeInSeconds = null): JsonResponse
    {
        $cacheKey = 'movie_user_data';

        Cache::forget($cacheKey);

        // If $timeInSeconds is null, cache indefinitely
        if ($timeInSeconds === null) {
            $data = Cache::rememberForever($cacheKey, function () {
                return $this->getDataFromDatabase();
            });

            return response()->json([
                'message'   => 'Data cached indefinitely',
                'data'      => $data,
            ]);
        } else {
            // Cache with the specified expiration time
            $data = Cache::remember($cacheKey, $timeInSeconds, function () {
                return $this->getDataFromDatabase();
            });

            return response()->json([
                'message'   => 'Data cached for ' . $timeInSeconds . ' seconds',
                'data'      => $data,
            ]);
        }
    }

    /**
     * @return JsonResponse
     */
    public function cached(): JsonResponse
    {
        $cachedData = Cache::get('movie_user_data');

        return response()->json([
            'message'   => 'Cached data',
            'data'      => $cachedData
        ]);
    }

    private function getDataFromDatabase(): array
    {
        return DB::table('movie_user')->get()->toArray();
    }
}
