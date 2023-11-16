<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    public function getKeyName(): string
    {
        return 'slug';
    }

    protected $fillable = [
        'title',
        'description',
        'rating',
        'slug',
    ];

    protected $casts = ['slug' => 'string'];

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
}
