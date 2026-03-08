<?php
namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Http\Requests\EmployeeRequest;
use App\Enums\EmployeeJobTitle;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EmployeeController extends Controller
{
    protected $defaultPerPage = 10;

    public function index(Request $request)
    {
        $employees = QueryBuilder::for(Employee::class)
            ->allowedSorts(['first_name', 'created_at'])
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::scope('search'),
            ])
            ->paginate($request->input('per_page', $this->defaultPerPage))
            ->withQueryString();

        return Inertia::render('admin/employee/index', [
            'employeeList' => $employees,
            'statuses' => fn() => UserStatus::getOptions(),
            'jobTitles' => fn() => EmployeeJobTitle::getOptions(),
            'filter' => $request->input('filter', []) + [
                'default_per_page' => $this->defaultPerPage,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::modal('admin/employee/create', [
            'statuses' => fn() => UserStatus::getOptions(),
            'jobTitles' => fn() => EmployeeJobTitle::getOptions(),
        ])->baseRoute('admin.employee.index');
    }

    public function store(EmployeeRequest $request)
    {
        $employee = DB::transaction(function () use ($request) {
            $employee = Employee::create($request->only([
                'first_name',
                'last_name',
                'job_title',
                'status',
            ]));

            if ($request->hasFile('avatar')) {
                $employee
                    ->addMediaFromRequest('avatar')
                    ->toMediaCollection('avatar');
            }

            return $employee;
        });

        return response()->json([
            'employee' => $employee,
            'message' => __('crud.created', ['record' => 'Employee'])
        ], 201);
    }

    public function edit(Employee $employee)
    {
        return Inertia::modal('admin/employee/edit', [
            'employee' => $employee,
            'statuses' => fn() => UserStatus::getOptions(),
            'jobTitles' => fn() => EmployeeJobTitle::getOptions(),
        ])->baseRoute('admin.employee.index');
    }

    public function update(EmployeeRequest $request, Employee $employee)
    {
        DB::transaction(function () use ($request, $employee) {
            $employee->update($request->only([
                'first_name',
                'last_name',
                'job_title',
                'status',
            ]));

            if ($request->hasFile('avatar')) {
                $employee
                    ->addMediaFromRequest('avatar')
                    ->toMediaCollection('avatar');
            } elseif ($request->input('avatar_removed')) {
                $employee->clearMediaCollection('avatar');
            }
        });

        return response()->json([
            'employee' => $employee->refresh(),
            'message' => __('crud.updated', ['record' => 'Employee'])
        ]);
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->json([
            'deleted' => [$employee->id],
            'message' => __('crud.deleted', ['record' => 'Employee']),
        ]);
    }
}
