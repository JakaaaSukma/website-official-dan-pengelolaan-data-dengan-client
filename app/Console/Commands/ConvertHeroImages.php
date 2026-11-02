<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConvertHeroImages extends Command
{
    protected $signature = 'convert:hero-images';
    protected $description = 'Convert static images to WebP format';

    public function handle()
    {
        $this->info('Converting hero images to WebP...');

        $images = [
            public_path('img/jelas.png'),
            public_path('img/about-1.png'),
            public_path('img/hero-img-1.png'),
        ];

        foreach ($images as $imagePath) {
            if (!file_exists($imagePath)) {
                $this->warn("File not found: $imagePath");
                continue;
            }

            $pathInfo = pathinfo($imagePath);
            $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';

            $imageInfo = getimagesize($imagePath);
            if (!$imageInfo) {
                $this->warn("Invalid image: $imagePath");
                continue;
            }

            list($width, $height, $type) = $imageInfo;

            $sourceImage = null;
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $sourceImage = imagecreatefromjpeg($imagePath);
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = imagecreatefrompng($imagePath);
                    imagepalettetotruecolor($sourceImage);
                    imagealphablending($sourceImage, false);
                    imagesavealpha($sourceImage, true);
                    break;
                default:
                    $this->warn("Unsupported image type: $imagePath");
                    continue 2;
            }

            if (!$sourceImage) {
                $this->warn("Failed to read image: $imagePath");
                continue;
            }

            imagewebp($sourceImage, $webpPath, 85);

            $this->info("✓ Converted: $imagePath → $webpPath");

            imagedestroy($sourceImage);
        }

        $this->info('All images converted successfully!');
        return 0;
    }
}
