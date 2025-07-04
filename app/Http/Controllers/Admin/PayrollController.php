<?php
// php artisan make:controller Admin/PayrollController --resource

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = Payroll::with(['employee.user', 'employee.department', 'employee.designation']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Sorting
        $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('created_at', 'desc');

        $payrolls = $query->paginate($request->get('per_page', 15))->withQueryString();

        // Get filter options
        $employees = Employee::where('status', 'active')->get(['id', 'employee_name']);
        $years = Payroll::distinct()->pluck('year')->sort()->reverse()->values();
        $months = collect(range(1, 12))->map(function ($month) {
            return [
                'value' => $month,
                'label' => Carbon::create()->month($month)->format('F')
            ];
        });

        // Statistics
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $stats = [
            'total_payrolls' => Payroll::count(),
            'current_month_payrolls' => Payroll::where('month', $currentMonth)->where('year', $currentYear)->count(),
            'pending_payments' => Payroll::where('status', 'pending')->count(),
            'paid_this_month' => Payroll::where('status', 'paid')->where('month', $currentMonth)->where('year', $currentYear)->count(),
            'total_amount_current_month' => Payroll::where('month', $currentMonth)->where('year', $currentYear)->sum('net_salary'),
            'pending_amount' => Payroll::where('status', 'pending')->sum('net_salary'),
        ];

        return inertia('Admin/Payroll/Index', [
            'payrolls' => $payrolls,
            'employees' => $employees,
            'years' => $years,
            'months' => $months,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status', 'month', 'year', 'employee_id', 'per_page']),
        ]);
    }

    public function create()
    {
        $employees = Employee::with(['user', 'department', 'designation'])
            ->where('status', 'active')
            ->get();

        return inertia('Admin/Payroll/Create', [
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'basic_salary' => 'required|numeric|min:0',
            'accommodation' => 'nullable|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'overtime_amount' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:' . (now()->year + 1),
        ]);

        // Check if payroll already exists for this employee, month, and year
        $existingPayroll = Payroll::where('employee_id', $validated['employee_id'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->first();

        if ($existingPayroll) {
            return redirect()->back()->withErrors([
                'employee_id' => 'Payroll already exists for this employee for the selected month and year.'
            ]);
        }

        // Set defaults
        $validated['accommodation'] = $validated['accommodation'] ?? 0;
        $validated['allowances'] = $validated['allowances'] ?? 0;
        $validated['overtime_amount'] = $validated['overtime_amount'] ?? 0;
        $validated['deductions'] = $validated['deductions'] ?? 0;

        // Calculate gross and net salary
        $validated['gross_salary'] = $validated['basic_salary'] + $validated['accommodation'] +
            $validated['allowances'] + $validated['overtime_amount'];
        $validated['net_salary'] = $validated['gross_salary'] - $validated['deductions'];
        $validated['status'] = 'pending';

        Payroll::create($validated);

        return redirect()->route('admin.payroll.index')
            ->with('success', 'Payroll created successfully.');
    }

    public function show(Payroll $payroll)
    {
        $payroll->load(['employee.user', 'employee.department', 'employee.designation', 'employee.location']);

        return inertia('Admin/Payroll/Show', [
            'payroll' => $payroll,
        ]);
    }

    public function edit(Payroll $payroll)
    {
        $payroll->load(['employee.user']);

        $employees = Employee::with(['user', 'department', 'designation'])
            ->where('status', 'active')
            ->get();

        return inertia('Admin/Payroll/Edit', [
            'payroll' => $payroll,
            'employees' => $employees,
        ]);
    }

    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'basic_salary' => 'required|numeric|min:0',
            'accommodation' => 'nullable|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'overtime_amount' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:' . (now()->year + 1),
        ]);

        // Check if payroll already exists for this employee, month, and year (excluding current)
        $existingPayroll = Payroll::where('employee_id', $validated['employee_id'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->where('id', '!=', $payroll->id)
            ->first();

        if ($existingPayroll) {
            return redirect()->back()->withErrors([
                'employee_id' => 'Payroll already exists for this employee for the selected month and year.'
            ]);
        }

        // Set defaults
        $validated['accommodation'] = $validated['accommodation'] ?? 0;
        $validated['allowances'] = $validated['allowances'] ?? 0;
        $validated['overtime_amount'] = $validated['overtime_amount'] ?? 0;
        $validated['deductions'] = $validated['deductions'] ?? 0;

        // Calculate gross and net salary
        $validated['gross_salary'] = $validated['basic_salary'] + $validated['accommodation'] +
            $validated['allowances'] + $validated['overtime_amount'];
        $validated['net_salary'] = $validated['gross_salary'] - $validated['deductions'];

        $payroll->update($validated);

        return redirect()->route('admin.payroll.index')
            ->with('success', 'Payroll updated successfully.');
    }

    public function destroy(Payroll $payroll)
    {
        if ($payroll->status === 'paid') {
            return redirect()->route('admin.payroll.index')
                ->with('error', 'Cannot delete paid payroll.');
        }

        $payroll->delete();

        return redirect()->route('admin.payroll.index')
            ->with('success', 'Payroll deleted successfully.');
    }

    public function markAsPaid(Payroll $payroll)
    {
        $payroll->markAsPaid();

        return redirect()->route('admin.payroll.index')
            ->with('success', 'Payroll marked as paid successfully.');
    }

    public function generatePayrolls(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:' . (now()->year + 1),
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $generatedCount = 0;
        $skippedCount = 0;

        foreach ($validated['employee_ids'] as $employeeId) {
            // Check if payroll already exists
            $existingPayroll = Payroll::where('employee_id', $employeeId)
                ->where('month', $validated['month'])
                ->where('year', $validated['year'])
                ->first();

            if ($existingPayroll) {
                $skippedCount++;
                continue;
            }

            $employee = Employee::find($employeeId);

            // Create payroll with basic salary (you can customize this logic)
            Payroll::create([
                'employee_id' => $employeeId,
                'basic_salary' => 5000, // Default basic salary - customize as needed
                'accommodation' => 1000, // Default accommodation - customize as needed
                'allowances' => 500, // Default allowances - customize as needed
                'overtime_amount' => 0,
                'deductions' => 0,
                'gross_salary' => 6500,
                'net_salary' => 6500,
                'month' => $validated['month'],
                'year' => $validated['year'],
                'status' => 'pending',
            ]);

            $generatedCount++;
        }

        $message = "Generated {$generatedCount} payroll(s) successfully.";
        if ($skippedCount > 0) {
            $message .= " Skipped {$skippedCount} existing payroll(s).";
        }

        return redirect()->route('admin.payroll.index')->with('success', $message);
    }

    public function generatePayslip(Payroll $payroll)
    {
        $payroll->load(['employee.user', 'employee.department', 'employee.designation', 'employee.location']);

        return inertia('Admin/Payroll/Payslip', [
            'payroll' => $payroll,
        ]);
    }
}