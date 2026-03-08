<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property string $author_type
 * @property int $author_id
 * @property string $content
 * @property bool $internal
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class ModelNote extends Model
{
    protected $guarded = [];

    protected $appends = [
        'edited',
        'lined_content',
    ];

    public function model()
    {
        return $this->morphTo('model');
    }

    public function author()
    {
        return $this->morphTo('author');
    }

    protected function edited(): Attribute
    {
        return Attribute::get(
            fn() => $this->created_at?->notEqualTo($this->updated_at)
        );
    }

    protected function linedContent(): Attribute
    {
        return Attribute::get(
            fn() => nl2br(e($this->content))
        );
    }
}
