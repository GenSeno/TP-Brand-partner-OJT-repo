<?php

namespace App\Http\Controllers\Admin;

use App\Actions\GenerateReference;
use App\Enums\ExpenseStatus;
use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseRequest;
use App\Lunar\DataTypes\Price;
use App\Models\Currency;
use App\Models\Expense;
use App\Models\ExpenseAccount;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Sorts\Sort;

class ExpenseController extends Controller
{
    protected $defaultPerPage = 10;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $now = Carbon::now();
        $currentYear = $now->year;
        $currentMonth = $now->month;
        $expense = QueryBuilder::for(Expense::class)
            ->defaultSort('-expense_date')
            ->allowedSorts([
                'status',
                'expense_date',
                'reference',
                'payment_date',
                'payment_mode',
                AllowedSort::custom('supplier', new class implements Sort
                {
                    public function __invoke(Builder $query, bool $descending, string $property)
                    {
                        $query->join('suppliers', 'suppliers.id', '=', 'expenses.supplier_id')
                            ->orderBy('suppliers.name', $descending ? 'desc' : 'asc')
                            ->select('expenses.*'); // Important: avoid column ambiguity
                    }
                }),
            ])
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::scope('search'),
            ])
            ->with([
                'supplier.media',
                'lines.expenseAccount',
            ])
            ->paginate(request()->input('per_page', 10))
            ->withQueryString();

        $counts = [
            'paid' => Expense::where('status', 'paid')
                ->whereYear('payment_date', $currentYear)
                ->whereMonth('payment_date', $currentMonth)
                ->sum('total_amount') ?? 0,
            'upcoming' => Expense::where('status', 'upcoming')->sum('total_amount'),
            'expenses' => Expense::whereIn('status', ['paid', 'upcoming'])
                ->whereYear('expense_date', $currentYear)
                ->whereMonth('expense_date', $currentMonth)
                ->sum('total_amount') ?? 0,
            'total' => Expense::count() ?? 0,
        ];

        return Inertia::render('admin/expense/index', [
            'expense' => $expense,
            'counts' => $counts,
            'filter' => $request->input('filter', []),
            'default_per_page' => $this->defaultPerPage,
        ]);
    }

    public function create()
    {
        return Inertia::modal('admin/expense/create', [
            'suppliers' => Supplier::orderByName()->getOptions('name', 'id'),
        ])->baseRoute('admin.expense.index');
    }

    public function store(Request $request)
    {
        // Validate
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'expense_date' => 'required|date',
        ]);

        $expense = DB::transaction(function () use ($request) {
            $expense = Expense::create([
                'supplier_id' => $request->supplier_id,
                'expense_date' => $request->expense_date,
                'description' => $request->description,
                'currency_id' => Currency::defaultId(),
            ]);

            $expense->update([
                'reference' => GenerateReference::run($expense->id, 'generator.expense.reference_format'),
            ]);

            return $expense;
        });

        if ($request->has('createAnother')) {
            return response()->json([
                'expense' => $expense->fresh(['supplier', 'lines']),
                'message' => __('crud.created', ['record' => 'Expense']),
            ], 201);
        }

        // Otherwise, redirect to show page
        return redirect()->route('expenses.show', $expense->id)
            ->with('success', 'Payment voucher successfully!');
    }

    public function show(Expense $expense)
    {
        // Load lines with product info
        $expense->load([
            'supplier.media',
            'supplier.country',
            'lines.expenseAccount',
            'currency',
            'media',
        ]);

        return Inertia::render('admin/expense/main', [
            'expense' => $expense,
            'template' => 'template',
            'activities' => fn () => $expense->activities()->latest()->with('causer')->take(100)->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        $expense->load([
            'supplier.media',
            'supplier.country',
            'lines.expenseAccount',
            'currency',
        ]);

        $methods = PaymentMethod::cases();
        $paymentMethods = collect($methods)->map(fn ($method) => [
            'value' => $method->value,
            'label' => $method->getLabel(),
        ])->values();

        return Inertia::modal('admin/expense/edit', [
            'expense' => $expense,
            'suppliers' => Supplier::orderByName()->getOptions('name', 'id'),
            'paymentMethods' => $paymentMethods,
        ])->baseRoute('admin.expense.item', $expense->id);
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        $expense->update(
            $request->validated()
        );

        return response()->json([
            'message' => 'Payment voucher updated successfully.',
            'expense' => $expense->fresh(['lines', 'supplier.media', 'supplier.country', 'currency']),
        ]);
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return to_route('admin.expense.index')
            ->with('success', __('crud.deleted', ['record' => 'Expense']));
    }

    /**
     * Display the add/edit item.
     */
    public function item(Expense $expense)
    {

        $expense->load([
            'supplier.media',
            'supplier.country',
            'lines',
            'currency',
        ]);

        return Inertia::render('admin/expense/main', [
            'expense' => $expense,
            'expense_accounts' => ExpenseAccount::active()->orderByName()->getOptions('name', 'id'),
            'template' => 'item',
            'activities' => fn () => $expense->activities()->latest()->with('causer')->take(100)->get(),
        ]);
    }

    public function adjustment(Expense $expense)
    {
        return Inertia::modal('admin/expense/discount/add_adj', [
            'expense' => $expense,
            'template' => 'item',
        ])->baseRoute('admin.expense.item', $expense);
    }

    public function storeAdjustment(Request $request, Expense $expense)
    {
        $adjustments = collect($request->adjustment_breakdown ?? []);

        // -----------------------------
        // Subtotal (always source of truth)
        // -----------------------------
        $subtotal = $expense->lines->sum(
            fn ($line) => $line->quantity * $line->price->value
        ); // cents
        // -----------------------------
        // Build Discount Breakdown
        // -----------------------------
        $discountBreakdown = $adjustments
            ->where('type', 'discount')
            ->map(function ($row) use ($expense) {
                $baseAmount = $expense->lines->sum(fn ($line) => $line->quantity * $line->price->value);
                $method = $row['method'] ?? 'fixed';
                $value = $row['amount'];
                $amount = $method === 'percentage'
                    ? $baseAmount * ($row['amount'] / 100)
                    : $row['amount'] * 100;

                $price = new Price((int) $amount, $expense->currency);

                return [
                    'method' => $method,
                    'label' => $row['label'],
                    'value' => $value,
                    'amount' => $amount,
                    'decimal' => $price->decimal,
                    'format' => $price->formatted,
                ];
            })->values();
        // -----------------------------
        // Totals
        // -----------------------------
        $discountTotal = $discountBreakdown->sum('amount');
        $grandTotal = $subtotal - $discountTotal;

        // -----------------------------
        // Validation
        // -----------------------------
        if ($grandTotal < 0) {
            return redirect()
                ->route('admin.expense.item', $expense->id)
                ->withErrors([
                    'discount' => 'Total amount due cannot be negative.',
                ]);
        }

        // -----------------------------
        // Persist
        // -----------------------------
        $expense->update([
            'discount_breakdown' => $discountBreakdown,
            'discount_total' => $discountTotal,
            'total_amount' => $grandTotal,
        ]);

        return response()->json([
            'message' => 'Discount applied successfully',
            'expense_lines' => $expense->lines()->get(),
            'expense' => $expense->refresh(),
        ]);

    }

    public function cancelled(Expense $expense)
    {
        $expense->update([
            'status' => ExpenseStatus::CANCELLED,
        ]);

        return redirect()
            ->route('admin.expense.item', $expense->id)
            ->with('success', 'Status mark as cancelled successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function paid(Expense $expense)
    {
        $expense->load([
            'supplier.media',
            'supplier.country',
            'lines.expenseAccount',
            'currency',
        ]);

        $methods = PaymentMethod::cases();
        $paymentMethods = collect($methods)->map(fn ($method) => [
            'value' => $method->value,
            'label' => $method->getLabel(),
        ])->values();

        return Inertia::modal('admin/expense/paid', [
            'expense' => $expense,
            'paymentMethods' => $paymentMethods,
        ])->baseRoute('admin.expense.item', $expense->id);
    }

    public function markAsPaid(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:255',
            'status' => 'required|string|in:paid,cancelled,draft,upcoming',
        ]);

        // Only update these fields
        $expense->update($validated);

        return response()->json([
            'message' => 'Payment voucher paid successfully.',
            'expense' => $expense->fresh(['lines', 'supplier.media', 'supplier.country', 'currency']),
        ]);
    }

    public function fetchByStatus($status)
    {

        $statuses = explode(',', $status);
        $expenses = Expense::with([
            'supplier',
        ])
            ->whereIn('status', $statuses)
            ->get();

        return response()->json($expenses);

    }

    public function pdf(Expense $expense)
    {
        // Load lines with product info
        $expense->load([
            'supplier.media',
            'supplier.country',
            'lines.expenseAccount',
            'currency',
        ]);

        return Inertia::render('pdf/expense', [
            'expense' => $expense,
        ]);
    }

    public function download($id)
    {
        $expense = Expense::with([
            'supplier.media',
            'supplier.country',
            'lines.expenseAccount',
            'currency',
        ])->findOrFail($id);

        return Pdf::loadView('pdf.expense', compact('expense'))
            ->setPaper('legal', 'portrait')
            ->download("expense-{$expense->reference}.pdf");
    }

    public function addNote(Request $request, Expense $expense)
    {
        $request->validate([
            'note' => ['required', 'string', 'max:5000'],
        ]);

        $expense->logNote($request->input('note'));

        return response()->json([
            'message' => __('Note added successfully.'),
        ], 201);
    }

    public function upload(Request $request, Expense $expense)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:25600',
        ]);

        $uploaded = [];

        foreach ($request->file('files', []) as $file) {

            // Original filename
            $originalName = $file->getClientOriginalName();

            // Clean filename
            $cleanName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));

            $extension = $file->getClientOriginalExtension();

            // Final filename: REFERENCE_filename.ext
            $fileName = "{$expense->reference}_{$cleanName}.{$extension}";

            // ✅ Add to Spatie MediaLibrary → ExpensePathGenerator will handle folder
            $media = $expense
                ->addMedia($file)
                ->usingFileName($fileName)
                ->toMediaCollection('expense', 'public');

            $uploaded[] = [
                'id' => $media->id,
                'name' => $fileName,
                'path' => $media->getPathRelativeToRoot(), // relative path: expenses/{reference}/filename.ext
                'original_url' => $media->getUrl(),
                'extension' => $extension,
                'size' => $media->size,                // size in bytes
                'created_at' => $media->created_at->toDateTimeString(), // formatted datetime
            ];
        }

        return response()->json([
            'success' => true,
            'files' => $uploaded,
            'count' => $expense->getMedia('attachments')->count(),
        ]);
    }

    public function destroyMedia(Expense $expense, $mediaId)
    {
        $media = $expense->media()->findOrFail($mediaId);
        $media->delete();

        return response()->json(['success' => true]);
    }
}
