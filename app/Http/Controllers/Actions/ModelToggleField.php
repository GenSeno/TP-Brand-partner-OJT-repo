<?php

namespace App\Http\Controllers\Actions;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Controller;
use Str;

class ModelToggleField extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($model, $id, $field)
    {
        $modelClass = '\\App\\Models\\' . Str::studly($model);
        if (!class_exists($modelClass)) {
            return response()->json(['error' => 'Model not found'], 404);
        }

        /**
         * @var Model $instance
         */
        $instance = $modelClass::findOrFail($id);

        if (!array_key_exists($field, $instance->getAttributes())) {
            abort(422, 'Invalid field');
        }

        if (!in_array($instance->getCasts()[$field] ?? null, ['bool', 'boolean'], true)) {
            abort(422, 'Field is not boolean');
        }

        $instance->{$field} = !$instance->{$field};
        $instance->save();

        return response()->json([
            $model => $instance,
            'message' => __('crud.updated', ['record' => Str::title($field)]),
        ]);
    }
}
