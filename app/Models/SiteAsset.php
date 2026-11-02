<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SiteAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_key',
        'title',
        'page',
        'placement',
        'image_path',
        'default_url',
        'recommended_size',
        'description',
    ];

    /**
     * Get image URL helper (uploaded file > fallback URL > empty string)
     */
    public function getUrlAttribute(): string
    {
        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
            return Storage::url($this->image_path);
        }

        return $this->default_url ?: asset('img/jelas.png');
    }

    /**
     * Helper static method to fetch asset URL easily in any Blade template:
     * e.g. SiteAsset::url('home_hero_banner', 'https://fallback.com/img.jpg')
     */
    public static function url(string $key, ?string $fallback = null): string
    {
        $asset = static::where('asset_key', $key)->first();
        if ($asset) {
            return $asset->url;
        }

        return $fallback ?: asset('img/jelas.png');
    }
}
