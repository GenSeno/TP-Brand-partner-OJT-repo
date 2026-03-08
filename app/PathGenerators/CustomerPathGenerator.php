<?php

namespace App\PathGenerators;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomerPathGenerator implements PathGenerator
{
    /**
     * Create a new class instance.
     */
     public function getPath(Media $media): string
    {
        // Store in avatars directory
        return 'customers/';
    }

    public function getPathForConversions(Media $media): string
    {
        return 'customers/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return 'customers/';
    }
}
