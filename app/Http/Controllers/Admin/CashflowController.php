<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\CashflowAdjustment;
use Inertia\Inertia;
use App\Enums\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CashflowController extends Controller
{
    protected $defaultPerPage = 50;

    public function index()
    {
        $groups = PaymentMethod::groups();
        $labels = PaymentMethod::groupLabels();
       
        $data = collect($groups)->map(function ($methods, $key) use ($labels) {
            // BALANCE → only posted entries
            $creditPosted = Payment::whereIn('method', $methods)
                ->whereNotNull('posted_at')
                ->sum('amount') / 100;

            $debitPosted = Expense::whereIn('payment_method', $methods)
                ->where('status', 'paid')
                ->whereNotNull('posted_at')
                ->sum('total_amount') / 100;

            $adj_in = CashflowAdjustment::whereIn('group', $methods)
              ->where('method', 'in')
              ->sum('amount') / 100;

            $ajd_out = CashflowAdjustment::whereIn('group', $methods)
              ->where('method', 'out')
              ->sum('amount') / 100;

             $balance = ($creditPosted + $adj_in ) - ($debitPosted + $ajd_out);

            // DEBIT FOR POSTING → expenses not posted
            $debitForPosting = Expense::whereIn('payment_method', $methods)
                ->where('status', 'paid')
                ->whereNull('posted_at')
                ->sum('total_amount') / 100;

            // CREDIT FOR POSTING → payments not posted
            $creditForPosting = Payment::whereIn('method', $methods)
                ->whereNull('posted_at')
                ->sum('amount') / 100;

            return [
                'id' => $key,
                'payment_method' => $labels[$key] ?? ucfirst($key),
                'balance' => $balance,
                'debit' => $debitForPosting,
                'credit' => $creditForPosting,
            ];
        })->values();

          $startOfMonth = now()->startOfMonth();
          $endOfMonth = now()->endOfMonth();

        // POSTED EXPENSES (this month)
        $pExpenses = Expense::whereNotNull('posted_at')
            ->where('status', 'paid')
            ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->sum('total_amount') / 100;

        // with adjustment
        $wadj_out = CashflowAdjustment::where('method', 'out')
              ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
              ->sum('amount') / 100;

        $postedExpenses = $pExpenses + $wadj_out;

        // POSTED PAYMENTS (this month)
        $pPayments = Payment::whereNotNull('posted_at')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->sum('amount') / 100;

        // with adjustment
         $wadj_in = CashflowAdjustment::where('method', 'in')
              ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
              ->sum('amount') / 100;

        $postedPayments = $pPayments + $wadj_in;

        // EXPENSES FOR POSTING (this month)
        $expensesForPosting = Expense::whereNull('posted_at')
            ->where('status', 'paid')
            ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->sum('total_amount') / 100;

        // PAYMENTS FOR POSTING (this month)
        $paymentsForPosting = Payment::whereNull('posted_at')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->sum('amount') / 100;

            return Inertia::render('admin/cashflow/index', [
            'account' => [
                'data' => $data,
            ],
            'counts' => [
                'posted_expenses' => $postedExpenses,
                'posted_payments' => $postedPayments,
                'expenses_for_posting' => $expensesForPosting,
                'payments_for_posting' => $paymentsForPosting,
            ],
        ]);
    }

   public function show(string $group)
{
    $groups = PaymentMethod::groups();
    abort_unless(isset($groups[$group]), 404);

    $methods = $groups[$group];

    $perPage = request('per_page', 10);
    $sort = request('sort', '-date'); // example: date, -date
    $search = request('filter.search');

    $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
    $sortColumn = ltrim($sort, '-');

    /* ==========================
    | PAYMENTS (Cash In)
    ========================== */
    $payments = DB::table('payments')
        ->selectRaw("
            payments.id,
            payments.paid_at as date,
             CASE
            WHEN payments.internal_reference IS NOT NULL AND payments.internal_reference != '' 
                 AND payments.reference IS NOT NULL AND payments.reference != ''
                THEN CONCAT(payments.internal_reference, ' / Ref #:', payments.reference)
            WHEN payments.internal_reference IS NOT NULL AND payments.internal_reference != ''
                THEN payments.internal_reference
            ELSE payments.reference
            END as reference,
            0 as cash_out,
            payments.amount as cash_in,
            payments.posted_at,
            payments.posted_by,
            'payment' as type
        ")
        ->whereIn('payments.method', $methods)
        ->when($search, fn($q) => $q->where('payments.reference', 'like', "%{$search}%"));

    /* ==========================
    | EXPENSES (Cash Out)
    ========================== */
    $expenses = DB::table('expenses')
        ->selectRaw("
            expenses.id,
            expenses.payment_date as date,
            expenses.reference,
            expenses.total_amount as cash_out,
            0 as cash_in,
            expenses.posted_at,
            expenses.posted_by,
            'expense' as type
        ")
        ->whereIn('expenses.payment_method', $methods)
        ->when($search, fn($q) => $q->where('expenses.reference', 'like', "%{$search}%"));

    /* ==========================
    | ADJUSTMENTS (Cash In / Out)
    ========================== */
    $adjustments = DB::table('cashflow_adjustments')
        ->selectRaw("
            cashflow_adjustments.id,
            cashflow_adjustments.created_at as date,
            cashflow_adjustments.description as reference,
            CASE WHEN cashflow_adjustments.method = 'out' THEN cashflow_adjustments.amount ELSE 0 END as cash_out,
            CASE WHEN cashflow_adjustments.method = 'in' THEN cashflow_adjustments.amount ELSE 0 END as cash_in,
            cashflow_adjustments.created_at as posted_at,
            cashflow_adjustments.posted_by,
            'adjustment' as type
        ")
        ->where('cashflow_adjustments.group', $group)
        ->when($search, fn($q) => $q->where('cashflow_adjustments.description', 'like', "%{$search}%"));

    /* ==========================
    | UNION + PAGINATION
    ========================== */
    $ledger = DB::query()
        ->fromSub(
            $payments->unionAll($expenses)->unionAll($adjustments),
            'ledger'
        )
        ->orderBy($sortColumn, $direction)
        ->paginate($perPage)
        ->withQueryString()
        ->through(fn($row) => [
            'id' => $row->id,
            'date' => $row->date,
            'reference' => $row->reference,
            'cash_in' => $row->cash_in / 100,
            'cash_out' => $row->cash_out / 100,
            'posted_at' => $row->posted_at,
            'posted_by' => $row->posted_by,
            'type' => $row->type,
        ]);

    /* ==========================
    | BALANCE (include adjustments)
    ========================== */
    $balancePayments = DB::table('payments')
        ->selectRaw("0 as cash_out, amount as cash_in, posted_at")
        ->whereIn('method', $methods)
        ->whereNotNull('posted_at');

    $balanceExpenses = DB::table('expenses')
        ->selectRaw("total_amount as cash_out, 0 as cash_in, posted_at")
        ->whereIn('payment_method', $methods)
        ->whereNotNull('posted_at');

    $balanceAdjustments = DB::table('cashflow_adjustments')
        ->selectRaw("
            CASE WHEN method = 'out' THEN amount ELSE 0 END as cash_out,
            CASE WHEN method = 'in' THEN amount ELSE 0 END as cash_in,
            created_at as posted_at
        ")
        ->where('group', $group);

    $postedBalance = DB::query()
        ->fromSub(
            $balancePayments->unionAll($balanceExpenses)->unionAll($balanceAdjustments),
            'ledger'
        )
        ->selectRaw('SUM(cash_in) - SUM(cash_out) as balance')
        ->value('balance') ?? 0;

    return Inertia::render('admin/cashflow/show', [
        'group' => $group,
        'entries' => $ledger,
        'balance' => $postedBalance / 100,
        'filter' => ['search' => $search],
        'default_per_page' => $this->defaultPerPage,
    ]);
}


    // Show modal for payment
    public function postPaymentModal(Payment $payment)
    {
        $groupKey = $payment->method; 

        return Inertia::modal('admin/cashflow/modal/payment', [
            'transaction' => $payment,
            'default_date' => now()->format('Y-m-d'),
        ])->baseRoute('admin.cashflow.show', ['group' => $groupKey]);
    }

    // Post payment
    public function postPayment(Request $request, Payment $payment)
    {
        $request->validate(['posted_at' => 'required|date']);
        $payment->update([
            'posted_at' => $request->posted_at,
            'posted_by' =>  auth()->guard('staff')->id(),
        ]);

        return redirect()->back()->with('success', 'Payment posted successfully.');
    }

    // Show modal for expense
    public function postExpenseModal(Expense $expense)
    {
        $groupKey = $expense->payment_method; 

        return Inertia::modal('admin/cashflow/modal/expense', [
            'transaction' => $expense,
            'default_date' => now()->format('Y-m-d'),
        ])->baseRoute('admin.cashflow.show', ['group' => $groupKey]);
    }

    // Post expense
    public function postExpense(Request $request, Expense $expense)
    {
        $request->validate(['posted_at' => 'required|date']);
        $expense->update([
            'posted_at' => $request->posted_at,
            'posted_by' =>  auth()->guard('staff')->id(),
        ]);

        return redirect()->back()->with('success', 'Expense posted successfully.');
    }

    public function unpostPayment(Payment $payment)
    {
        $payment->update(['posted_at' => null, 'posted_by' => null]);
        return response()->json(['message' => 'Payment unposted successfully.']);
    }

    public function unpostExpense(Expense $expense)
    {
        $expense->update(['posted_at' => null, 'posted_by' => null]);
        return response()->json(['message' => 'Expense unposted successfully.']);
    }


    public function adjustmentModal(string $group)
    {
        $groups = PaymentMethod::groups();
        abort_unless(isset($groups[$group]), 404);

        return Inertia::modal('admin/cashflow/modal/adjustment', [
            'group' => $group,
        ])->baseRoute('admin.cashflow.show', ['group' => $group]);
   }

}
