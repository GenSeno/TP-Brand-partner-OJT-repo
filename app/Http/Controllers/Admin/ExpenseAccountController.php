<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseAccount;
use App\Models\Supplier;
use App\Models\Currency;
use App\Http\Requests\ExpenseRequest;
use DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Actions\GenerateReference;


class ExpenseAccountController extends Controller
{
    protected $defaultPerPage = 10;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         $account = QueryBuilder::for(ExpenseAccount::class)
            ->allowedSorts(['created_at', 'name'])
            ->defaultSort('-created_at','name') 
            ->allowedFilters([
                AllowedFilter::exact('name'),
                AllowedFilter::exact('enabled'),
                AllowedFilter::scope('search'),
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::render('admin/expense/account/index', [
            'account' => $account,
            'filter' => $request->input('filter', []),
            'default_per_page' => $this->defaultPerPage
        ]);
    }   

     public function create()
    {
        return Inertia::modal('admin/expense/account/create')->baseRoute('admin.expense_account.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'enabled' => 'boolean',
        ]);

        $expense = DB::transaction(function () use ($request) {
            $expense = ExpenseAccount::create([
                'name' => $request->name,
                'code' => Str::slug($request->name),
                'description' => $request->description,
                'enabled' => $request->enabled,
            ]);

            return $expense;
        });


        return back()->with('success', 'Expense voucher created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExpenseAccount $account)
    {
        
        return Inertia::modal('admin/expense/account/edit', [
            'account' => $account,
        ])->baseRoute('admin.expense_account.edit', $account->id );
    }

    public function update(Request $request, ExpenseAccount $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'enabled' => 'boolean',
        ]);

        $account->update($validated);

        return response()->json([
            'message' => 'Expense account updated successfully.',
            'data' => $account,
        ]);
    }

     public function destroy(ExpenseAccount $account)
    {
        $account->delete();
        return to_route('admin.expense_account.index')
            ->with('success', __('crud.deleted', ['record' => 'Expense Account']));
    }

    public function toggleStatus(ExpenseAccount $account)
    {
        $account->enabled = ! $account->enabled;
        $account->save();

        return to_route('admin.expense_account.index')
            ->with('success', __('crud.updated', ['record' => 'Expense Account']));
    }

  
}
