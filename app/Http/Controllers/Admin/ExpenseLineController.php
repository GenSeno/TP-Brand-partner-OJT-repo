<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseLinesRequest;
use App\Models\Expense;
use App\Models\ExpenseLine;
use Illuminate\Http\Request;
use DB;

class ExpenseLineController extends Controller
{
    public function store(ExpenseLinesRequest $request, Expense $expense)
    {
        $validated = $request->validated();

        $status = $request->status; 
        $lines  = $validated['expense_lines'];
        $savedLines = [];

        DB::transaction(function () use ($expense, $lines, $status, &$savedLines) {

            // Loop through each line to create/update
            foreach ($lines as $line) {
                $qty   = $line['qty'];
                $price = $line['price'] * 100; 
                $total = $qty * $price;   

                $savedLines[] = ExpenseLine::updateOrCreate(
                    ['id' => $line['id'] ?? null],
                    [
                        'expense_id'         => $expense->id,
                        'expense_account_id' => $line['expense_account_id'],
                        'description'        => $line['description'] ?? null,
                        'quantity'                => $qty,
                        'price'              => $price,
                        'total'             => $total,
                    ]
                );
            }

            $expense->update([
                'status' => $status,
            ]);

            $expense->recalculateTotals();
        });

        $expense->load('lines');

        return response()->json([
            'message'       => 'Item(s) updated successfully!',
            'expense' => $expense,
            'expense_lines' => $savedLines
        ]);
    }

     /**
     * Delete a expense line.
     */
    public function destroy(ExpenseLine $expenseLine)
    {
        // Grab parent expense if exists
        $expense = $expenseLine->expense;

        // Delete the line
        $expenseLine->delete();

        // Recalculate totals if expense exists
        if ($expense) {
            $expense->recalculateTotals();
        }
        // Return updated lines and expense
        return response()->json([
            'message' => 'Item deleted successfully',
            'expense_lines' => $expense->lines()->get(), // fresh lines
            'expense' => $expense,
        ]);
    }
}

    