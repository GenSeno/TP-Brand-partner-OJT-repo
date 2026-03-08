<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ServePrivateStorage extends Controller
{
    public function __invoke(Media $media)
    {
        return $media;
    }
}
