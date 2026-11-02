<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizeClientLogos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:client-logos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize client logos: resize, compress, and generate WebP versions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting client logo optimization...');

        $files = Storage::disk('public')->files('clients');
        
        if (empty($files)) {
            $this->warn('No logo files found in storage/app/public/clients/');
            return 0;
        }

        foreach ($files as $file) {
            // Skip if it's already a WebP file
            if (pathinfo($file, PATHINFO_EXTENSION) === 'webp') {
                continue;
            }

            $this->info("Processing: $file");
            
            $filePath = Storage::disk('public')->path($file);
            $imageInfo = getimagesize($filePath);
            
            if (!$imageInfo) {
                $this->warn("Skipping invalid image: $file");
                continue;
            }

            list($width, $height, $type) = $imageInfo;
            
            // Create image resource based on type
            $sourceImage = null;
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $sourceImage = imagecreatefromjpeg($filePath);
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = imagecreatefrompng($filePath);
                    break;
                case IMAGETYPE_GIF:
                    $sourceImage = imagecreatefromgif($filePath);
                    break;
                default:
                    $this->warn("Unsupported image type: $file");
                    continue 2;
            }

            if (!$sourceImage) {
                $this->warn("Failed to read image: $file");
                continue;
            }

            // Calculate new dimensions (max 240x120, keep aspect ratio)
            $maxWidth = 240;
            $maxHeight = 120;
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = (int) ($width * $ratio);
            $newHeight = (int) ($height * $ratio);

            // Create new image
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency for PNG/GIF
            if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            // Resample
            imagecopyresampled(
                $newImage, $sourceImage,
                0, 0, 0, 0,
                $newWidth, $newHeight,
                $width, $height
            );

            // Save optimized original
            $pathInfo = pathinfo($file);
            $optimizedPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $pathInfo['extension'];
            $optimizedFullPath = Storage::disk('public')->path($optimizedPath);

            switch ($type) {
                case IMAGETYPE_JPEG:
                    imagejpeg($newImage, $optimizedFullPath, 85);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($newImage, $optimizedFullPath, 8);
                    break;
                case IMAGETYPE_GIF:
                    imagegif($newImage, $optimizedFullPath);
                    break;
            }

            // Save WebP version
            $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
            $webpFullPath = Storage::disk('public')->path($webpPath);
            imagewebp($newImage, $webpFullPath, 85);

            $this->info("✓ Optimized: $file");

            // Cleanup
            imagedestroy($sourceImage);
            imagedestroy($newImage);
        }

        $this->info('Client logo optimization complete!');
        return 0;
    }
}
