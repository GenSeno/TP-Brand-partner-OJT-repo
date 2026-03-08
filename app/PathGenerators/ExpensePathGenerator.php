<?php

namespace App\PathGenerators;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class ExpensePathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return 'expense/' . $media->model->reference . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        return '';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return '';
    }
}
