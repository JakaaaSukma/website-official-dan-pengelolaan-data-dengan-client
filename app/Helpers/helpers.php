<?php

if (!function_exists('storage_image_url')) {
    /**
     * Generate URL for storage images, works with or without symlink
     *
     * @param string|null $path
     * @return string|null
     */
    function storage_image_url($path)
    {
        if (empty($path)) {
            return null;
        }
        
        // Clean path - remove leading slash if any
        $path = ltrim($path, '/');
        
        // Use asset() with /storage/ prefix - this will work with our fallback route
        return asset('storage/' . $path);
    }
}

