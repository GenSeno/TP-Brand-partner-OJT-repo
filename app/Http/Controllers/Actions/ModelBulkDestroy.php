<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Str;

class ModelBulkDestroy extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $model)
    {
        $modelClass = '\\App\\Models\\' . Str::studly($model);

        if (!class_exists($modelClass)) {
            abort(404, 'Model not found');
        }

        $ids = $request->input('ids', []);

        if (!is_array($ids) || empty($ids)) {
            abort(422, 'No IDs provided');
        }

        $modelClass::whereIn('id', $ids)->delete();

        return response()->json([
            'deleted' => $ids,
            'message' => __('crud.deleted', ['record' => Str::title(Str::plural($model))]),
        ]);
    }
}
