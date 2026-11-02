<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'youtube_link',
        'is_active',
    ];

    public function getVideoIdAttribute()
    {
        $url = $this->youtube_link;
        if (empty($url)) return null;

        // Handle youtube.com/shorts/ID
        if (preg_match('/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return $matches[1];
        }

        // Handle youtube.com/live/ID
        if (preg_match('/youtube\.com\/live\/([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return $matches[1];
        }

        // Handle standard youtube.com/watch?v=ID, youtu.be/ID, embed/ID, etc.
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function getEmbedUrlAttribute()
    {
        if ($this->video_id) {
            return "https://www.youtube.com/embed/" . $this->video_id;
        }

        return null;
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->video_id) {
            return "https://img.youtube.com/vi/" . $this->video_id . "/0.jpg";
        }

        // Fallback: generic placeholder
        return "https://img.youtube.com/vi/default/0.jpg";
    }
}
