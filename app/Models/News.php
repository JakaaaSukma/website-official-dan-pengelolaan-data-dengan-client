<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'thumbnail',
        'images',
        'content',
        'is_headline',
        'is_published',
        'views',
    ];

    protected $casts = [
        'is_headline' => 'boolean',
        'is_published' => 'boolean',
        'views' => 'integer',
        'images' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function (News $news) {
            if (empty($news->slug) || strlen($news->slug) > 100) {
                $news->slug = Str::slug($news->title);
                // Ensure uniqueness
                $baseSlug = $news->slug;
                $counter = 1;
                while (static::where('slug', $news->slug)->exists()) {
                    $news->slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
            } else {
                // Sanitize slug to remove invalid characters
                $news->slug = Str::slug($news->slug);
            }
        });

        static::updating(function (News $news) {
            // Only fix slug if it's empty, too long, or contains invalid characters
            if (empty($news->slug) || 
                strlen($news->slug) > 100 || 
                strpos($news->slug, '/') !== false ||
                strpos($news->slug, ' ') !== false) {
                $news->slug = Str::slug($news->title);
                // Ensure uniqueness
                $baseSlug = $news->slug;
                $counter = 1;
                while (static::where('slug', $news->slug)->where('id', '!=', $news->id)->exists()) {
                    $news->slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
            } else {
                // Sanitize slug to remove invalid characters
                $news->slug = Str::slug($news->slug);
            }
        });
    }
}



