<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashflowAdjustment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CashflowAdjustmentController extends Controller
{
    public function store(Request $request)
    {
        // Strict validation for enum
        $validated = $request->validate([
            'method' => ['required', Rule::in(['in', 'out'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:255'],
            'group' => ['required'],
        ]);

        $adjustment = CashflowAdjustment::create([
            'method' => $validated['method'],
            'amount' => $validated['amount'] * 100,
            'group' => $validated['group'],
            'description' => $validated['description'] ?? null,
            'posted_by' => auth()->guard('staff')->id(),
        ]);

        return response()->json([
            'message' => 'Adjustment posted successfully.',
            'adjustment' => $adjustment,
        ]);
    }

      public function destroy(CashflowAdjustment $adjustment)
     {
         
        $adjustment->delete();
        return response()->json([
            'success' => true,
            'message' => 'Adjustment deleted successfully.'
        ]);
    }
}
