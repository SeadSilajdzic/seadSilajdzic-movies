<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Movie extends Model
{
    use HasFactory;

    public function getKeyName(): string
    {
        return 'slug';
    }

    protected $casts = ['slug' => 'string'];
    protected $primaryKey = 'slug';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'title',
        'description',
        'rating',
        'slug',
    ];

    const VALIDATION_RULES = [
        'post' => [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'rating' => 'required|numeric'
        ],

        'put' => [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'rating' => 'nullable|numeric',
        ]
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'movie_user', 'movie_slug', 'user_id');
    }
}
